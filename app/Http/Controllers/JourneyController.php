<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Govuk\Renderer;
use App\Service\Application;
use App\Service\Journey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class JourneyController extends Controller
{
    public function show(Request $request, string $step): View
    {
        $stepDef = Journey::step($step);
        if ($stepDef === null) {
            abort(404);
        }
        $app = $this->application($request);
        $errors = session('errors_'.$step, []);
        $returnTo = $request->query('return') === 'check-answers' ? 'check-answers' : null;
        $previous = Journey::previousStep($step);
        $backHref = $returnTo !== null ? '/check-answers' : ($previous['path'] ?? '/');

        return view('pages.question', [
            'pageTitle' => $stepDef['heading'].' – '.config('govuk.service_name'),
            'step' => $stepDef,
            'app' => $app,
            'errors' => $errors,
            'returnTo' => $returnTo,
            'beforeContent' => Renderer::render('back-link', ['href' => $backHref]),
        ]);
    }

    public function store(Request $request, string $step): RedirectResponse
    {
        $stepDef = Journey::step($step);
        if ($stepDef === null) {
            abort(404);
        }
        $app = $this->application($request);
        $errors = $this->validateStep($step, $request);
        if ($errors !== []) {
            $location = $stepDef['path'];
            if ($request->input('returnTo') === 'check-answers') {
                $location .= '?return=check-answers';
            }

            return redirect($location)->with('errors_'.$step, $errors)->withInput();
        }
        $this->applyStep($step, $request, $app);
        $app->markCompleted($step);
        $this->save($request, $app);

        if ($request->input('returnTo') === 'check-answers') {
            return redirect('/check-answers');
        }

        $next = Journey::nextStep($step);

        return redirect($next['path'] ?? '/check-answers');
    }

    public function checkAnswers(Request $request): View|RedirectResponse
    {
        $app = $this->application($request);
        if ($app->submitted) {
            return redirect('/confirmation');
        }
        $incomplete = Journey::firstIncompleteStep($app);
        if ($incomplete !== null) {
            return redirect($incomplete['path']);
        }

        $dob = trim($app->day.' '.$app->month.' '.$app->year);

        return view('pages.check-answers', [
            'pageTitle' => 'Check your answers – '.config('govuk.service_name'),
            'app' => $app,
            'rows' => [
                $this->summaryRow('Licence length', Journey::lengthLabel($app->licenceLength), '/licence-length', 'licence length'),
                $this->summaryRow('Name', $app->fullName, '/name', 'name'),
                $this->summaryRow('Date of birth', $dob, '/date-of-birth', 'date of birth'),
                $this->summaryRow('Where you will fish', $app->country, '/where-you-will-fish', 'where you will fish'),
                $this->summaryRow('Email address', $app->email, '/email', 'email address'),
            ],
            'beforeContent' => Renderer::render('back-link', ['href' => '/email']),
        ]);
    }

    public function submitAnswers(Request $request): RedirectResponse
    {
        $app = $this->application($request);
        if ($app->submitted) {
            return redirect('/confirmation');
        }
        $incomplete = Journey::firstIncompleteStep($app);
        if ($incomplete !== null) {
            return redirect($incomplete['path']);
        }
        $app->submitted = true;
        $app->reference = Journey::createReference();
        $this->save($request, $app);

        return redirect('/confirmation');
    }

    public function confirmation(Request $request): View|RedirectResponse
    {
        $app = $this->application($request);
        if (! $app->submitted) {
            return redirect('/');
        }

        return view('pages.confirmation', [
            'pageTitle' => 'Application complete – '.config('govuk.service_name'),
            'app' => $app,
        ]);
    }

    private function application(Request $request): Application
    {
        $app = $request->session()->get('application');
        if (! $app instanceof Application) {
            $app = new Application;
            $request->session()->put('application', $app);
        }

        return $app;
    }

    private function save(Request $request, Application $app): void
    {
        $request->session()->put('application', $app);
    }

    /**
     * @return array<string, string>
     */
    private function validateStep(string $stepId, Request $request): array
    {
        $errors = [];
        switch ($stepId) {
            case 'licence-length':
                $value = (string) $request->input('licenceLength');
                $valid = false;
                foreach (Journey::LICENCE_LENGTHS as $length) {
                    if ($length['value'] === $value) {
                        $valid = true;
                        break;
                    }
                }
                if (! $valid) {
                    $errors['licenceLength'] = 'Select how long you need the licence for';
                }
                break;
            case 'name':
                $name = trim((string) $request->input('fullName'));
                if (strlen($name) < 2) {
                    $errors['fullName'] = 'Enter your full name';
                } elseif (strlen($name) > 100) {
                    $errors['fullName'] = 'Full name must be 100 characters or fewer';
                }
                break;
            case 'date-of-birth':
                $day = trim((string) $request->input('day'));
                $month = trim((string) $request->input('month'));
                $year = trim((string) $request->input('year'));
                $dobError = $this->validateDateOfBirth($day, $month, $year);
                if ($dobError !== null) {
                    $errors['date-of-birth'] = $dobError;
                }
                break;
            case 'where-you-will-fish':
                $country = (string) $request->input('country');
                if (! in_array($country, Journey::COUNTRIES, true)) {
                    $errors['country'] = 'Select where you will fish';
                }
                break;
            case 'email':
                $email = trim((string) $request->input('email'));
                if (! preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
                    $errors['email'] = 'Enter an email address in the correct format, like name@example.com';
                }
                break;
        }

        return $errors;
    }

    private function validateDateOfBirth(string $day, string $month, string $year): ?string
    {
        if ($day === '' || $month === '' || $year === '') {
            return 'Enter your date of birth';
        }
        if (! preg_match('/^\d{1,2}$/', $day) || ! preg_match('/^\d{1,2}$/', $month) || ! preg_match('/^\d{4}$/', $year)) {
            return 'Enter a real date of birth';
        }
        $dayNumber = (int) $day;
        $monthNumber = (int) $month;
        $yearNumber = (int) $year;
        if (! checkdate($monthNumber, $dayNumber, $yearNumber)) {
            return 'Enter a real date of birth';
        }
        $dob = new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $yearNumber, $monthNumber, $dayNumber));
        $today = new \DateTimeImmutable('today');
        if ($dob > $today) {
            return 'Date of birth must be in the past';
        }
        $age = $today->diff($dob)->y;
        if ($age < 13) {
            return 'You must be at least 13 to use this example';
        }

        return null;
    }

    private function applyStep(string $stepId, Request $request, Application $app): void
    {
        switch ($stepId) {
            case 'licence-length':
                $app->licenceLength = (string) $request->input('licenceLength');
                break;
            case 'name':
                $app->fullName = trim((string) $request->input('fullName'));
                break;
            case 'date-of-birth':
                $app->day = trim((string) $request->input('day'));
                $app->month = trim((string) $request->input('month'));
                $app->year = trim((string) $request->input('year'));
                break;
            case 'where-you-will-fish':
                $app->country = (string) $request->input('country');
                break;
            case 'email':
                $app->email = trim((string) $request->input('email'));
                break;
        }
    }

    /**
     * @return array{key: array{text: string}, value: array{text: string}, actions: array{items: list<array{href: string, text: string, visuallyHiddenText: string}>}}
     */
    private function summaryRow(string $key, string $value, string $href, string $hidden): array
    {
        return [
            'key' => ['text' => $key],
            'value' => ['text' => $value],
            'actions' => [
                'items' => [[
                    'href' => $href.'?return=check-answers',
                    'text' => 'Change',
                    'visuallyHiddenText' => $hidden,
                ]],
            ],
        ];
    }
}

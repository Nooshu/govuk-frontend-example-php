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
    public function taskList(Request $request): View
    {
        $app = $this->application($request);
        $items = [];
        foreach (Journey::STEPS as $step) {
            $status = 'Not started';
            $classes = 'govuk-tag--blue';
            if ($app->isCompleted($step['id'])) {
                $status = 'Completed';
                $classes = '';
            }
            $items[] = [
                'title' => ['text' => $step['heading']],
                'href' => $step['path'],
                'status' => ['tag' => ['text' => $status, 'classes' => $classes]],
            ];
        }

        return view('pages.task-list', [
            'pageTitle' => 'Your application – '.config('govuk.service_name'),
            'taskListHtml' => Renderer::render('task-list', ['idPrefix' => 'licence', 'items' => $items]),
            'beforeContent' => Renderer::render('back-link', ['href' => '/']),
            'canCheck' => Journey::requiredComplete($app),
        ]);
    }

    public function show(Request $request, string $step): View
    {
        $stepDef = Journey::step($step);
        if ($stepDef === null) {
            abort(404);
        }
        $app = $this->application($request);
        $errors = session('errors_'.$step, []);

        return view('pages.question', [
            'pageTitle' => $stepDef['heading'].' – '.config('govuk.service_name'),
            'step' => $stepDef,
            'app' => $app,
            'errors' => $errors,
            'beforeContent' => Renderer::render('back-link', ['href' => '/task-list']),
        ]);
    }

    public function store(Request $request, string $step): RedirectResponse
    {
        $stepDef = Journey::step($step);
        if ($stepDef === null) {
            abort(404);
        }
        $app = $this->application($request);
        $errors = $this->validateStep($step, $request, $app);
        if ($errors !== []) {
            return redirect($stepDef['path'])->with('errors_'.$step, $errors)->withInput();
        }
        $this->applyStep($step, $request, $app);
        $app->markCompleted($step);
        $this->save($request, $app);

        return redirect('/task-list');
    }

    public function checkAnswers(Request $request): View|RedirectResponse
    {
        $app = $this->application($request);
        if (! Journey::requiredComplete($app)) {
            return redirect('/task-list');
        }

        return view('pages.check-answers', [
            'pageTitle' => 'Check your answers – '.config('govuk.service_name'),
            'app' => $app,
            'beforeContent' => Renderer::render('back-link', ['href' => '/task-list']),
        ]);
    }

    public function submitAnswers(Request $request): RedirectResponse
    {
        $app = $this->application($request);
        if (! Journey::requiredComplete($app)) {
            return redirect('/task-list');
        }
        $app->submitted = true;
        $app->reference = 'RFL-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $this->save($request, $app);

        return redirect('/confirmation');
    }

    public function confirmation(Request $request): View|RedirectResponse
    {
        $app = $this->application($request);
        if (! $app->submitted) {
            return redirect('/task-list');
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
    private function validateStep(string $stepId, Request $request, Application $app): array
    {
        $errors = [];
        switch ($stepId) {
            case 'name':
                if (trim((string) $request->input('firstName')) === '') {
                    $errors['firstName'] = 'Enter your first name';
                }
                if (trim((string) $request->input('lastName')) === '') {
                    $errors['lastName'] = 'Enter your last name';
                }
                break;
            case 'date-of-birth':
                foreach (['day' => 'day', 'month' => 'month', 'year' => 'year'] as $field => $label) {
                    if (trim((string) $request->input($field)) === '') {
                        $errors[$field] = 'Enter a '.$label;
                    }
                }
                break;
            case 'email':
                $email = trim((string) $request->input('email'));
                if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Enter an email address in the correct format, like name@example.com';
                }
                break;
            case 'contact-preference':
                if (! in_array($request->input('contactBy'), ['email', 'telephone'], true)) {
                    $errors['contactBy'] = 'Select how we should contact you';
                }
                if ($request->input('contactBy') === 'telephone' && trim((string) $request->input('telephone')) === '') {
                    $errors['telephone'] = 'Enter a telephone number';
                }
                break;
            case 'where-you-will-fish':
                $regions = $request->input('regions', []);
                if (! is_array($regions) || $regions === []) {
                    $errors['regions'] = 'Select at least one region';
                }
                break;
            case 'licence-length':
                if (! in_array($request->input('licenceLength'), ['1-day', '8-day', '12-month'], true)) {
                    $errors['licenceLength'] = 'Select a licence length';
                }
                break;
            case 'start-month':
                if (trim((string) $request->input('startMonth')) === '') {
                    $errors['startMonth'] = 'Enter a start month';
                }
                break;
            case 'address':
                if (trim((string) $request->input('addressLine1')) === '') {
                    $errors['addressLine1'] = 'Enter address line 1';
                }
                if (trim((string) $request->input('town')) === '') {
                    $errors['town'] = 'Enter a town or city';
                }
                if (trim((string) $request->input('postcode')) === '') {
                    $errors['postcode'] = 'Enter a postcode';
                }
                break;
            case 'evidence':
                // optional
                break;
            case 'additional-details':
                // optional
                break;
            case 'create-a-password':
                $password = (string) $request->input('password');
                $confirm = (string) $request->input('confirmPassword');
                if (strlen($password) < 8) {
                    $errors['password'] = 'Enter a password that is at least 8 characters';
                }
                if ($password !== $confirm) {
                    $errors['confirmPassword'] = 'Enter the same password again';
                }
                break;
        }

        return $errors;
    }

    private function applyStep(string $stepId, Request $request, Application $app): void
    {
        switch ($stepId) {
            case 'name':
                $app->firstName = trim((string) $request->input('firstName'));
                $app->lastName = trim((string) $request->input('lastName'));
                break;
            case 'date-of-birth':
                $app->day = trim((string) $request->input('day'));
                $app->month = trim((string) $request->input('month'));
                $app->year = trim((string) $request->input('year'));
                break;
            case 'email':
                $app->email = trim((string) $request->input('email'));
                break;
            case 'contact-preference':
                $app->contactBy = (string) $request->input('contactBy');
                $app->telephone = trim((string) $request->input('telephone'));
                break;
            case 'where-you-will-fish':
                $regions = $request->input('regions', []);
                $app->regions = is_array($regions) ? array_values(array_map('strval', $regions)) : [];
                break;
            case 'licence-length':
                $app->licenceLength = (string) $request->input('licenceLength');
                break;
            case 'start-month':
                $app->startMonth = trim((string) $request->input('startMonth'));
                break;
            case 'address':
                $app->addressLine1 = trim((string) $request->input('addressLine1'));
                $app->addressLine2 = trim((string) $request->input('addressLine2'));
                $app->town = trim((string) $request->input('town'));
                $app->postcode = trim((string) $request->input('postcode'));
                break;
            case 'evidence':
                $file = $request->file('evidence');
                if ($file !== null) {
                    $ext = strtolower($file->getClientOriginalExtension());
                    if (in_array($ext, ['pdf', 'png', 'jpg', 'jpeg'], true)) {
                        $app->evidenceFilename = $file->getClientOriginalName();
                    }
                }
                break;
            case 'additional-details':
                $app->additionalDetails = trim((string) $request->input('additionalDetails'));
                break;
            case 'create-a-password':
                $app->passwordCreated = true;
                break;
        }
    }
}

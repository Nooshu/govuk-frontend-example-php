<?php

declare(strict_types=1);

namespace App\Govuk;

final class Catalogue
{
    private const DESIGN_SYSTEM = 'https://design-system.service.gov.uk/components';

    /** @var array<string, array{title: string, description: string, url: string}> */
    private const DETAILS = [
        'accordion' => ['title' => 'Accordion', 'description' => 'Lets users show and hide sections of related content.', 'url' => self::DESIGN_SYSTEM.'/accordion/'],
        'back-link' => ['title' => 'Back link', 'description' => 'Link to the previous page in a journey.', 'url' => self::DESIGN_SYSTEM.'/back-link/'],
        'breadcrumbs' => ['title' => 'Breadcrumbs', 'description' => 'Helps users move between levels of a section.', 'url' => self::DESIGN_SYSTEM.'/breadcrumbs/'],
        'button' => ['title' => 'Button', 'description' => 'Starts or continues an action.', 'url' => self::DESIGN_SYSTEM.'/button/'],
        'character-count' => ['title' => 'Character count', 'description' => 'Shows how many characters are left in a textarea.', 'url' => self::DESIGN_SYSTEM.'/character-count/'],
        'checkboxes' => ['title' => 'Checkboxes', 'description' => 'Lets users select one or more options.', 'url' => self::DESIGN_SYSTEM.'/checkboxes/'],
        'cookie-banner' => ['title' => 'Cookie banner', 'description' => 'Asks users to accept or reject analytics cookies.', 'url' => self::DESIGN_SYSTEM.'/cookie-banner/'],
        'date-input' => ['title' => 'Date input', 'description' => 'Asks users for a date they already know.', 'url' => self::DESIGN_SYSTEM.'/date-input/'],
        'details' => ['title' => 'Details', 'description' => 'Hides content that only some users need.', 'url' => self::DESIGN_SYSTEM.'/details/'],
        'error-message' => ['title' => 'Error message', 'description' => 'Tells users how to fix a field that failed validation.', 'url' => self::DESIGN_SYSTEM.'/error-message/'],
        'error-summary' => ['title' => 'Error summary', 'description' => 'Summarises form errors at the top of the page.', 'url' => self::DESIGN_SYSTEM.'/error-summary/'],
        'exit-this-page' => ['title' => 'Exit this page', 'description' => 'Lets users leave a page quickly. For services where someone may be in danger.', 'url' => self::DESIGN_SYSTEM.'/exit-this-page/'],
        'feedback' => ['title' => 'Feedback', 'description' => 'Asks users what they think of a page. Trial component in Frontend 6.5.', 'url' => self::DESIGN_SYSTEM.'/feedback/'],
        'fieldset' => ['title' => 'Fieldset', 'description' => 'Groups related form fields, such as an address.', 'url' => self::DESIGN_SYSTEM.'/fieldset/'],
        'file-upload' => ['title' => 'File upload', 'description' => 'Lets users select a file to upload.', 'url' => self::DESIGN_SYSTEM.'/file-upload/'],
        'footer' => ['title' => 'Footer', 'description' => 'Page footer with Open Government Licence and Crown copyright.', 'url' => self::DESIGN_SYSTEM.'/footer/'],
        'generic-header' => ['title' => 'Generic header', 'description' => 'Header for services that are not branded as GOV.UK. Shown in the catalogue only.', 'url' => 'https://design-system.service.gov.uk/styles/page-template/'],
        'header' => ['title' => 'Header', 'description' => 'The GOV.UK masthead.', 'url' => self::DESIGN_SYSTEM.'/header/'],
        'hint' => ['title' => 'Hint', 'description' => 'Extra help for a form field. Form controls include it; the catalogue shows it on its own.', 'url' => 'https://design-system.service.gov.uk/get-started/labels-legends-headings/'],
        'input' => ['title' => 'Text input', 'description' => 'Lets users enter a single line of text.', 'url' => self::DESIGN_SYSTEM.'/text-input/'],
        'inset-text' => ['title' => 'Inset text', 'description' => 'Draws attention to important content on the page.', 'url' => self::DESIGN_SYSTEM.'/inset-text/'],
        'label' => ['title' => 'Label', 'description' => 'Labels a form field. Form controls include it; the catalogue shows it on its own.', 'url' => 'https://design-system.service.gov.uk/get-started/labels-legends-headings/'],
        'language-navigation' => ['title' => 'Language navigation', 'description' => 'Lets users switch between languages. Trial component in Frontend 6.5.', 'url' => self::DESIGN_SYSTEM.'/language-navigation/'],
        'notification-banner' => ['title' => 'Notification banner', 'description' => 'Tells users about something that affects the whole service.', 'url' => self::DESIGN_SYSTEM.'/notification-banner/'],
        'pagination' => ['title' => 'Pagination', 'description' => 'Splits a long list across pages.', 'url' => self::DESIGN_SYSTEM.'/pagination/'],
        'panel' => ['title' => 'Panel', 'description' => 'Confirms a transaction is complete.', 'url' => self::DESIGN_SYSTEM.'/panel/'],
        'password-input' => ['title' => 'Password input', 'description' => 'Lets users enter a password, with a control to show or hide it.', 'url' => self::DESIGN_SYSTEM.'/password-input/'],
        'phase-banner' => ['title' => 'Phase banner', 'description' => 'Shows users that the service is still being tried out.', 'url' => self::DESIGN_SYSTEM.'/phase-banner/'],
        'radios' => ['title' => 'Radios', 'description' => 'Lets users select one option from a list.', 'url' => self::DESIGN_SYSTEM.'/radios/'],
        'select' => ['title' => 'Select', 'description' => 'Lets users choose one option from a long list.', 'url' => self::DESIGN_SYSTEM.'/select/'],
        'service-navigation' => ['title' => 'Service navigation', 'description' => 'Shows the service name under the GOV.UK masthead.', 'url' => self::DESIGN_SYSTEM.'/service-navigation/'],
        'skip-link' => ['title' => 'Skip link', 'description' => 'Lets keyboard users skip to the main content.', 'url' => self::DESIGN_SYSTEM.'/skip-link/'],
        'summary-list' => ['title' => 'Summary list', 'description' => 'Summarises answers so users can check them.', 'url' => self::DESIGN_SYSTEM.'/summary-list/'],
        'table' => ['title' => 'Table', 'description' => 'Shows information in rows and columns.', 'url' => self::DESIGN_SYSTEM.'/table/'],
        'tabs' => ['title' => 'Tabs', 'description' => 'Lets users switch between related views. Content stays in the page without JavaScript.', 'url' => self::DESIGN_SYSTEM.'/tabs/'],
        'tag' => ['title' => 'Tag', 'description' => 'Shows a short status, such as on a task list.', 'url' => self::DESIGN_SYSTEM.'/tag/'],
        'task-list' => ['title' => 'Task list', 'description' => 'Shows the tasks in an application and whether they are done.', 'url' => self::DESIGN_SYSTEM.'/task-list/'],
        'textarea' => ['title' => 'Textarea', 'description' => 'Lets users enter more than one line of text.', 'url' => self::DESIGN_SYSTEM.'/textarea/'],
        'warning-text' => ['title' => 'Warning text', 'description' => 'Tells users about something important before they continue.', 'url' => self::DESIGN_SYSTEM.'/warning-text/'],
    ];

    /**
     * @return array{name: string, title: string, description: string, designSystemUrl: string}
     */
    public static function describe(string $name): array
    {
        if (isset(self::DETAILS[$name])) {
            $d = self::DETAILS[$name];

            return [
                'name' => $name,
                'title' => $d['title'],
                'description' => $d['description'],
                'designSystemUrl' => $d['url'],
            ];
        }

        $title = ucwords(str_replace('-', ' ', $name));

        return [
            'name' => $name,
            'title' => $title,
            'description' => 'GOV.UK Frontend component.',
            'designSystemUrl' => self::DESIGN_SYSTEM.'/'.$name.'/',
        ];
    }
}

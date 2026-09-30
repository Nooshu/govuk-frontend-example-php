<?php

declare(strict_types=1);

namespace App\Govuk;

use App\Govuk\Components\Button;
use App\Govuk\Components\Chrome;
use App\Govuk\Components\Forms;
use App\Govuk\Components\Lists;
use App\Govuk\Components\Text;

/**
 * Renderer registry — mirrors GOV.UK Frontend component names to PHP ports.
 */
final class Renderer
{
    /** @var array<string, callable(Params): string> */
    private static array $renderers = [];

    /**
     * @return array<string, callable(Params): string>
     */
    private static function registry(): array
    {
        if (self::$renderers === []) {
            self::$renderers = [
                'accordion' => Lists::renderAccordion(...),
                'back-link' => Text::renderBackLink(...),
                'breadcrumbs' => Chrome::renderBreadcrumbs(...),
                'button' => Button::renderButton(...),
                'character-count' => Forms::renderCharacterCount(...),
                'checkboxes' => Forms::renderCheckboxes(...),
                'cookie-banner' => Chrome::renderCookieBanner(...),
                'date-input' => Forms::renderDateInput(...),
                'details' => Text::renderDetails(...),
                'error-message' => Text::renderErrorMessage(...),
                'error-summary' => Lists::renderErrorSummary(...),
                'exit-this-page' => Button::renderExitThisPage(...),
                'feedback' => Text::renderFeedback(...),
                'fieldset' => Text::renderFieldset(...),
                'file-upload' => Forms::renderFileUpload(...),
                'footer' => Chrome::renderFooter(...),
                'generic-header' => Chrome::renderGenericHeader(...),
                'header' => Chrome::renderHeader(...),
                'hint' => Text::renderHint(...),
                'input' => Forms::renderInput(...),
                'inset-text' => Text::renderInsetText(...),
                'label' => Text::renderLabel(...),
                'language-navigation' => Chrome::renderLanguageNavigation(...),
                'notification-banner' => Lists::renderNotificationBanner(...),
                'pagination' => Chrome::renderPagination(...),
                'panel' => Text::renderPanel(...),
                'password-input' => Forms::renderPasswordInput(...),
                'phase-banner' => Text::renderPhaseBanner(...),
                'radios' => Forms::renderRadios(...),
                'select' => Forms::renderSelect(...),
                'service-navigation' => Chrome::renderServiceNavigation(...),
                'skip-link' => Text::renderSkipLink(...),
                'summary-list' => Lists::renderSummaryList(...),
                'table' => Lists::renderTable(...),
                'tabs' => Lists::renderTabs(...),
                'tag' => Text::renderTag(...),
                'task-list' => Lists::renderTaskList(...),
                'textarea' => Forms::renderTextarea(...),
                'warning-text' => Text::renderWarningText(...),
            ];
        }

        return self::$renderers;
    }

    /**
     * Return trimmed HTML for one GOV.UK Frontend component (fixture-parity contract).
     *
     * @param  Params|array<string, mixed>|null  $params
     *
     * @throws \InvalidArgumentException when the component name is unknown
     */
    public static function render(string $component, Params|array|null $params = null): string
    {
        $renderers = self::registry();
        if (! isset($renderers[$component])) {
            throw new \InvalidArgumentException(
                'govuk: "'.$component.'" is not a GOV.UK Frontend component'
            );
        }
        if (is_array($params)) {
            $params = Params::fromArray($params);
        }
        $params ??= new Params;

        return trim($renderers[$component]($params));
    }

    /**
     * @param  Params|array<string, mixed>|null  $params
     */
    public static function mustRender(string $component, Params|array|null $params = null): string
    {
        return self::render($component, $params);
    }

    /**
     * @return list<string>
     */
    public static function components(): array
    {
        $names = array_keys(self::registry());
        sort($names);

        return $names;
    }
}

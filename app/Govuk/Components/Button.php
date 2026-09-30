<?php

declare(strict_types=1);

namespace App\Govuk\Components;

use App\Govuk\Attributes;
use App\Govuk\Nunjucks;
use App\Govuk\Params;
use App\Govuk\SafeString;

/**
 * Button and exit-this-page — ported from GOV.UK Frontend macros via Go reference.
 */
final class Button
{
    private const START_ICON = "\n".
        '  <svg class="govuk-button__start-icon" xmlns="http://www.w3.org/2000/svg" width="17.5" height="19" viewBox="0 0 33 40" aria-hidden="true" focusable="false">'."\n".
        '    <path fill="currentColor" d="M0 0h13l20 20-20 20H0l20-20z"/>'."\n".
        '  </svg>';

    private const EXIT_DEFAULT_HTML = '  <span class="govuk-visually-hidden">Emergency</span> Exit this page'."\n";

    public static function renderButton(Params $p): string
    {
        $classNames = 'govuk-button';
        $classes = $p->get('classes');
        if (Nunjucks::truthy($classes)) {
            $classNames .= ' '.Nunjucks::str($classes);
        }
        $startButton = Nunjucks::truthy($p->get('isStartButton'));
        if ($startButton) {
            $classNames .= ' govuk-button--start';
        }

        $commonAttributes = ' class="'.Nunjucks::escape($classNames).'" data-module="govuk-button"'.
            Attributes::render($p->get('attributes')).
            Nunjucks::attributeIf('id', $p->get('id'));

        $html = $p->get('html');
        $text = Nunjucks::out($p->get('text'));
        if (Nunjucks::truthy($html) && $startButton) {
            $text = '<span>'.Nunjucks::trim(Nunjucks::str($html)).'</span>';
        } elseif (Nunjucks::truthy($html)) {
            $text = Nunjucks::trim(Nunjucks::str($html));
        }

        $out = '';
        $href = $p->get('href');
        if (Nunjucks::truthy($href)) {
            $out .= '<a href="'.Nunjucks::out($href).'" role="button" draggable="false"'.
                $commonAttributes.">\n  ".Nunjucks::indent($text, 2, false);
        } else {
            $out .= '<button type="'.Nunjucks::out(Nunjucks::defTruthy($p->get('type'), 'submit')).'"'.
                Nunjucks::attributeIf('value', $p->get('value')).
                Nunjucks::attributeIf('name', $p->get('name')).
                Nunjucks::flagIf(' disabled aria-disabled="true"', $p->get('disabled'));
            $preventDoubleClick = $p->get('preventDoubleClick');
            if (! Nunjucks::isUndefined($preventDoubleClick)) {
                $out .= ' data-prevent-double-click="'.Nunjucks::out($preventDoubleClick).'"';
            }
            $out .= $commonAttributes.">\n  ".Nunjucks::indent($text, 2, false);
        }
        if ($startButton) {
            $out .= self::START_ICON;
        }
        $out .= Nunjucks::truthy($p->get('href')) ? "\n</a>" : "\n</button>";

        return $out;
    }

    public static function renderExitThisPage(Params $p): string
    {
        $html = $p->get('html');
        if (! Nunjucks::truthy($html) && ! Nunjucks::truthy($p->get('text'))) {
            $html = new SafeString(self::EXIT_DEFAULT_HTML);
        }

        $button = self::renderButton(Params::make(
            'html', $html,
            'text', $p->get('text'),
            'classes', 'govuk-button--warning govuk-exit-this-page__button govuk-js-exit-this-page-button',
            'href', Nunjucks::defTruthy($p->get('redirectUrl'), 'https://www.bbc.co.uk/weather'),
            'attributes', Params::make('rel', 'nofollow noreferrer'),
        ));

        return '<div'.Nunjucks::attributeIf('id', $p->get('id')).
            ' class="govuk-exit-this-page'.Nunjucks::classesIf($p->get('classes')).
            '" data-module="govuk-exit-this-page"'.
            Attributes::render($p->get('attributes')).
            Nunjucks::attributeIf('data-i18n.activated', $p->get('activatedText')).
            Nunjucks::attributeIf('data-i18n.timed-out', $p->get('timedOutText')).
            Nunjucks::attributeIf('data-i18n.press-two-more-times', $p->get('pressTwoMoreTimesText')).
            Nunjucks::attributeIf('data-i18n.press-one-more-time', $p->get('pressOneMoreTimeText')).
            ">\n  ".Nunjucks::indent(Nunjucks::trim($button), 2, false)."\n</div>";
    }
}

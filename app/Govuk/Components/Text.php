<?php

declare(strict_types=1);

namespace App\Govuk\Components;

use App\Govuk\Attributes;
use App\Govuk\Nunjucks;
use App\Govuk\Params;

/**
 * Ported from GOV.UK Frontend macros (via Python/Go reference) for byte-for-byte fixture parity.
 */
final class Text
{
    public static function renderBackLink(Params $p): string
    {
        $text = Nunjucks::out(Nunjucks::defTruthy($p->get('text'), 'Back'));
        $html = $p->get('html');
        if (Nunjucks::truthy($html)) {
            $text = Nunjucks::str($html);
        }

        return '<a href="'.Nunjucks::out(Nunjucks::defTruthy($p->get('href'), '#')).'" class="govuk-back-link'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>'.$text.'</a>';
    }

    public static function renderSkipLink(Params $p): string
    {
        return '<a href="'.Nunjucks::out(Nunjucks::defTruthy($p->get('href'), '#content')).'" class="govuk-skip-link'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).' data-module="govuk-skip-link">'.Nunjucks::content($p, 'html', 'text').'</a>';
    }

    public static function renderHint(Params $p): string
    {
        return '<div'.Nunjucks::attributeIf('id', $p->get('id')).' class="govuk-hint'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
  '.Nunjucks::contentIndent($p, 'html', 'text', 2).'
</div>';
    }

    public static function renderInsetText(Params $p): string
    {
        return '<div'.Nunjucks::attributeIf('id', $p->get('id')).' class="govuk-inset-text'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
  '.Nunjucks::contentIndent($p, 'html', 'text', 2).'
</div>';
    }

    public static function renderTag(?Params $p): string
    {
        if ($p === null) {
            $p = new Params;
        }

        return '<strong class="govuk-tag'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
  '.Nunjucks::contentIndent($p, 'html', 'text', 2).'
</strong>';
    }

    public static function renderWarningText(Params $p): string
    {
        return '<div class="govuk-warning-text'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
'.'  <span class="govuk-warning-text__icon" aria-hidden="true">!</span>
'.'  <strong class="govuk-warning-text__text">
'.'    <span class="govuk-visually-hidden">'.Nunjucks::out(Nunjucks::defTruthy($p->get('iconFallbackText'), 'Warning')).'</span>
'.'    '.Nunjucks::content($p, 'html', 'text').'
'.'  </strong>
</div>';
    }

    public static function renderErrorMessage(Params $p): string
    {
        $visuallyHidden = Nunjucks::def($p->get('visuallyHiddenText'), 'Error');
        $message = Nunjucks::contentIndent($p, 'html', 'text', 2);
        $parts = [];
        $parts[] = '<p'.Nunjucks::attributeIf('id', $p->get('id')).' class="govuk-error-message'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
';
        if (Nunjucks::truthy($visuallyHidden)) {
            $parts[] = '  <span class="govuk-visually-hidden">'.Nunjucks::out($visuallyHidden).':</span> '.$message.'
';
        } else {
            $parts[] = '  '.$message.'
';
        }
        $parts[] = '</p>';

        return implode('', $parts);
    }

    public static function renderDetails(Params $p): string
    {
        return '<details'.Nunjucks::attributeIf('id', $p->get('id')).' class="govuk-details'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).Nunjucks::flagIf(' open', $p->get('open')).'>
'.'  <summary class="govuk-details__summary">
'.'    <span class="govuk-details__summary-text">
'.'      '.Nunjucks::contentIndent($p, 'summaryHtml', 'summaryText', 6).'
'.'    </span>
  </summary>
'.'  <div class="govuk-details__text">
'.'    '.Nunjucks::content($p, 'html', 'text').'
'.'  </div>
</details>';
    }

    public static function renderLabel(Params $p): string
    {
        if ((! Nunjucks::truthy($p->get('html')) && ! Nunjucks::truthy($p->get('text')))) {
            return '';
        }
        $label = '<label class="govuk-label'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).Nunjucks::attributeIf('for', $p->get('for')).'>
  '.Nunjucks::contentIndent($p, 'html', 'text', 2).'
</label>
';
        if (Nunjucks::truthy($p->get('isPageHeading'))) {
            return '<h1 class="govuk-label-wrapper">
  '.Nunjucks::indent(Nunjucks::trim($label), 2, false).'
</h1>
';
        }

        return Nunjucks::trim($label).'
';
    }

    public static function renderPanel(Params $p): string
    {
        $classes = $p->get('classes');
        $interruption = (Nunjucks::truthy($classes) && Nunjucks::contains('govuk-panel--interruption', $classes));
        $level = Nunjucks::heading($p->get('headingLevel'), '1');
        $parts = [];
        $parts[] = '<div class="govuk-panel';
        if (! $interruption) {
            $parts[] = ' govuk-panel--confirmation';
        }
        $parts[] = Nunjucks::classesIf($classes).'"'.Attributes::render($p->get('attributes')).'>
';
        $parts[] = '  <h'.$level.' class="govuk-panel__title">'.'
    '.Nunjucks::content($p, 'titleHtml', 'titleText').'
  </h'.$level.'>
';
        if ((Nunjucks::truthy($p->get('html')) || Nunjucks::truthy($p->get('text')))) {
            $parts[] = '  <div class="govuk-panel__body">
    '.Nunjucks::contentIndent($p, 'html', 'text', 4).'
  </div>
';
        }
        $actions = $p->get('actions');
        if (($interruption && Nunjucks::truthy($actions))) {
            $parts[] = '  <div class="govuk-panel__actions'.Nunjucks::classesIf(Nunjucks::get($actions, 'classes')).'"'.Attributes::render(Nunjucks::get($actions, 'attributes')).'>';
            $entries = Nunjucks::items(Nunjucks::get($actions, 'items'));
            if (count($entries) > 0) {
                $parts[] = '<div class="govuk-button-group">
';
                foreach ($entries as $action) {
                    $parts[] = '      '.Nunjucks::indent(Nunjucks::trim(self::panelAction($action)), 6, false).'
';
                }
                $parts[] = '    </div>';
            }
            $parts[] = '</div>
';
        }
        $parts[] = '</div>';

        return implode('', $parts);
    }

    private static function panelAction(mixed $action): string
    {
        $href = Nunjucks::get($action, 'href');
        if ((! Nunjucks::truthy($href) || Nunjucks::str(Nunjucks::get($action, 'type')) === 'button')) {
            return Button::renderButton(Params::make('text', Nunjucks::get($action, 'text'), 'type', Nunjucks::defTruthy(Nunjucks::get($action, 'type'), 'button'), 'classes', 'govuk-button--inverse'.Nunjucks::concatIf(' ', Nunjucks::get($action, 'classes')), 'href', $href, 'attributes', Nunjucks::get($action, 'attributes')));
        }

        return '<a class="govuk-link govuk-link--inverse'.Nunjucks::classesIf(Nunjucks::get($action, 'classes')).'" href="'.Nunjucks::out($href).'"'.Attributes::render(Nunjucks::get($action, 'attributes')).'>'.Nunjucks::out(Nunjucks::get($action, 'text')).'</a>';
    }

    public static function renderPhaseBanner(Params $p): string
    {
        $tag = $p->get('tag');
        $tagHtml = self::renderTag(Params::make('text', Nunjucks::get($tag, 'text'), 'html', Nunjucks::get($tag, 'html'), 'classes', 'govuk-phase-banner__content__tag'.Nunjucks::concatIf(' ', Nunjucks::get($tag, 'classes'))));

        return '<div class="govuk-phase-banner govuk-width-container'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
'.'  <p class="govuk-phase-banner__content">
'.'    '.Nunjucks::indent(Nunjucks::trim($tagHtml), 4, false).'
'.'    <span class="govuk-phase-banner__text">
'.'      '.Nunjucks::contentIndent($p, 'html', 'text', 6).'
'.'    </span>
  </p>
</div>';
    }

    public static function renderFeedback(Params $p): string
    {
        $level = Nunjucks::heading($p->get('headingLevel'), '2');
        $parts = [];
        $parts[] = '<div class="govuk-feedback govuk-width-container'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
';
        $parts[] = '  <div class="govuk-grid-row">
';
        $parts[] = '    <div class="govuk-grid-column-two-thirds">
';
        $parts[] = '      <h'.$level.' class="govuk-feedback__title">'.'
        '.Nunjucks::content($p, 'titleHtml', 'titleText').'
      </h'.$level.'>
';
        [$html, $text] = [$p->get('html'), $p->get('text')];
        if ((Nunjucks::truthy($html) || Nunjucks::truthy($text))) {
            $parts[] = '        <div class="govuk-feedback__body">
';
            if (Nunjucks::truthy($html)) {
                $parts[] = '            '.Nunjucks::indent(Nunjucks::trim(Nunjucks::str($html)), 4, false).'
';
            } elseif (Nunjucks::truthy($text)) {
                $parts[] = '            <p class="govuk-body">
';
                $parts[] = '              '.Nunjucks::escape(Nunjucks::indent(Nunjucks::trim(Nunjucks::str($text)), 6, false)).'
';
                $parts[] = '            </p>
';
            }
            $parts[] = '        </div>
';
        }
        $parts[] = '    </div>
  </div>
</div>';

        return implode('', $parts);
    }

    public static function renderFieldset(Params $p): string
    {
        $parts = [];
        $parts[] = '<fieldset class="govuk-fieldset'.Nunjucks::classesIf($p->get('classes')).'"'.Nunjucks::attributeIf('role', $p->get('role')).Nunjucks::attributeIf('aria-describedby', $p->get('describedBy')).Attributes::render($p->get('attributes')).'>
';
        $legend = $p->get('legend');
        if ((Nunjucks::truthy(Nunjucks::get($legend, 'html')) || Nunjucks::truthy(Nunjucks::get($legend, 'text')))) {
            $parts[] = '  <legend class="govuk-fieldset__legend'.Nunjucks::classesIf(Nunjucks::get($legend, 'classes')).'">
';
            if (Nunjucks::truthy(Nunjucks::get($legend, 'isPageHeading'))) {
                $parts[] = '    <h1 class="govuk-fieldset__heading">
';
                $parts[] = '      '.Nunjucks::contentIndent($legend, 'html', 'text', 6).'
';
                $parts[] = '    </h1>
';
            } else {
                $parts[] = '    '.Nunjucks::contentIndent($legend, 'html', 'text', 4).'
';
            }
            $parts[] = '  </legend>
';
        }
        $html = $p->get('html');
        if (Nunjucks::truthy($html)) {
            $parts[] = '  '.Nunjucks::str($html).'
';
        }
        $parts[] = '</fieldset>';

        return implode('', $parts);
    }
}

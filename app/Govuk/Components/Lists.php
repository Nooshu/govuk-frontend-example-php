<?php

declare(strict_types=1);

namespace App\Govuk\Components;

use App\Govuk\Attributes;
use App\Govuk\Nunjucks;
use App\Govuk\Params;

/**
 * Ported from GOV.UK Frontend macros (via Python/Go reference) for byte-for-byte fixture parity.
 */
final class Lists
{
    public static function renderAccordion(Params $p): string
    {
        $parts = [];
        $parts[] = '<div class="govuk-accordion'.Nunjucks::classesIf($p->get('classes')).'" data-module="govuk-accordion" id="'.Nunjucks::out($p->get('id')).'"'.Attributes::i18n('hide-all-sections', $p->get('hideAllSectionsText'), Nunjucks::undefined()).Attributes::i18n('hide-section', $p->get('hideSectionText'), Nunjucks::undefined()).Attributes::i18n('hide-section-aria-label', $p->get('hideSectionAriaLabelText'), Nunjucks::undefined()).Attributes::i18n('show-all-sections', $p->get('showAllSectionsText'), Nunjucks::undefined()).Attributes::i18n('show-section', $p->get('showSectionText'), Nunjucks::undefined()).Attributes::i18n('show-section-aria-label', $p->get('showSectionAriaLabelText'), Nunjucks::undefined());
        $remember = $p->get('rememberExpanded');
        if (! Nunjucks::isUndefined($remember)) {
            $parts[] = ' data-remember-expanded="'.Nunjucks::escape(Nunjucks::str($remember)).'"';
        }
        $parts[] = Attributes::render($p->get('attributes')).'>
';
        foreach (Nunjucks::items($p->get('items')) as $_i => $item) {
            $index = $_i + 0;
            if (! Nunjucks::truthy($item)) {
                continue;
            }
            $parts[] = self::accordionItem($p, $item, ($index + 1));
        }
        $parts[] = '</div>';

        return implode('', $parts);
    }

    private static function accordionItem(Params $p, mixed $item, int $index): string
    {
        $level = Nunjucks::heading($p->get('headingLevel'), '2');
        $id_ = Nunjucks::out($p->get('id'));
        $position = (string) ($index);
        $itemHeading = Nunjucks::get($item, 'heading');
        $summary = Nunjucks::get($item, 'summary');
        $itemContent = Nunjucks::get($item, 'content');
        $parts = [];
        $parts[] = '  <div class="govuk-accordion__section'.Nunjucks::flagIf(' govuk-accordion__section--expanded', Nunjucks::get($item, 'expanded')).'">
';
        $parts[] = '    <div class="govuk-accordion__section-header">
';
        $parts[] = '      <h'.$level.' class="govuk-accordion__section-heading">
';
        $parts[] = '        <span class="govuk-accordion__section-button" id="'.$id_.'-heading-'.$position.'">
';
        $parts[] = '          '.Nunjucks::contentIndent($itemHeading, 'html', 'text', 8).'
';
        $parts[] = '        </span>
      </h'.$level.'>
';
        if ((Nunjucks::truthy(Nunjucks::get($summary, 'html')) || Nunjucks::truthy(Nunjucks::get($summary, 'text')))) {
            $parts[] = '      <div class="govuk-accordion__section-summary govuk-body" id="'.$id_.'-summary-'.$position.'">
';
            $parts[] = '        '.Nunjucks::contentIndent($summary, 'html', 'text', 8).'
';
            $parts[] = '      </div>
';
        }
        $parts[] = '    </div>
';
        $parts[] = '    <div id="'.$id_.'-content-'.$position.'" class="govuk-accordion__section-content">
';
        [$html, $text] = [Nunjucks::get($itemContent, 'html'), Nunjucks::get($itemContent, 'text')];
        if (Nunjucks::truthy($html)) {
            $parts[] = '      '.Nunjucks::indent(Nunjucks::trim(Nunjucks::str($html)), 6, false).'
';
        } elseif (Nunjucks::truthy($text)) {
            $parts[] = '      <p class="govuk-body">
';
            $parts[] = '        '.Nunjucks::escape(Nunjucks::indent(Nunjucks::trim(Nunjucks::str($text)), 8, false)).'
';
            $parts[] = '      </p>
';
        }
        $parts[] = '    </div>
  </div>
';

        return implode('', $parts);
    }

    public static function renderErrorSummary(Params $p): string
    {
        $parts = [];
        $parts[] = '<div class="govuk-error-summary'.Nunjucks::classesIf($p->get('classes')).'"';
        $autoFocus = $p->get('disableAutoFocus');
        if (! Nunjucks::isUndefined($autoFocus)) {
            $parts[] = ' data-disable-auto-focus="'.Nunjucks::out($autoFocus).'"';
        }
        $parts[] = Attributes::render($p->get('attributes')).' data-module="govuk-error-summary">';
        $parts[] = '
  <div role="alert">
';
        $parts[] = '    <h2 class="govuk-error-summary__title">
';
        $parts[] = '      '.Nunjucks::contentIndent($p, 'titleHtml', 'titleText', 6).'
';
        $parts[] = '    </h2>
';
        $parts[] = '    <div class="govuk-error-summary__body">
';
        if ((Nunjucks::truthy($p->get('descriptionHtml')) || Nunjucks::truthy($p->get('descriptionText')))) {
            $parts[] = '      <p>
        '.Nunjucks::contentIndent($p, 'descriptionHtml', 'descriptionText', 8).'
      </p>
';
        }
        $errorList = Nunjucks::items($p->get('errorList'));
        if (count($errorList) > 0) {
            $parts[] = '        <ul class="govuk-list govuk-error-summary__list">
';
            foreach ($errorList as $item) {
                $parts[] = '          <li>
';
                $href = Nunjucks::get($item, 'href');
                if (Nunjucks::truthy($href)) {
                    $parts[] = '            <a href="'.Nunjucks::out($href).'"'.Attributes::render(Nunjucks::get($item, 'attributes')).'>'.Nunjucks::contentIndent($item, 'html', 'text', 12).'</a>
';
                } else {
                    $parts[] = '            '.Nunjucks::contentIndent($item, 'html', 'text', 10).'
';
                }
                $parts[] = '          </li>
';
            }
            $parts[] = '        </ul>
';
        }
        $parts[] = '    </div>
  </div>
</div>';

        return implode('', $parts);
    }

    public static function renderNotificationBanner(Params $p): string
    {
        $success = Nunjucks::str($p->get('type')) === 'success';
        $typeClass = '';
        if ($success) {
            $typeClass = ' govuk-notification-banner--'.Nunjucks::escape(Nunjucks::str($p->get('type')));
        }
        $role = 'region';
        if (Nunjucks::truthy($p->get('role'))) {
            $role = Nunjucks::str($p->get('role'));
        } elseif ($success) {
            $role = 'alert';
        }
        if (Nunjucks::truthy($p->get('titleHtml'))) {
            $title = Nunjucks::str($p->get('titleHtml'));
        } elseif (Nunjucks::truthy($p->get('titleText'))) {
            $title = Nunjucks::out($p->get('titleText'));
        } elseif ($success) {
            $title = 'Success';
        } else {
            $title = 'Important';
        }
        $titleId = Nunjucks::out(Nunjucks::defTruthy($p->get('titleId'), 'govuk-notification-banner-title'));
        $level = Nunjucks::out(Nunjucks::defTruthy($p->get('titleHeadingLevel'), '2'));
        $parts = [];
        $parts[] = '<div class="govuk-notification-banner'.$typeClass.Nunjucks::classesIf($p->get('classes')).'" role="'.Nunjucks::escape($role).'" aria-labelledby="'.$titleId.'" data-module="govuk-notification-banner"';
        $autoFocus = $p->get('disableAutoFocus');
        if (! Nunjucks::isUndefined($autoFocus)) {
            $parts[] = ' data-disable-auto-focus="'.Nunjucks::out($autoFocus).'"';
        }
        $parts[] = Attributes::render($p->get('attributes')).'>
';
        $parts[] = '  <div class="govuk-notification-banner__header">
';
        $parts[] = '    <h'.$level.' class="govuk-notification-banner__title" id="'.$titleId.'">
';
        $parts[] = '      '.$title.'
    </h'.$level.'>
  </div>
';
        $parts[] = '  <div class="govuk-notification-banner__content">
';
        [$html, $text] = [$p->get('html'), $p->get('text')];
        if (Nunjucks::truthy($html)) {
            $parts[] = '    '.Nunjucks::indent(Nunjucks::trim(Nunjucks::str($html)), 4, false).'
';
        } elseif (Nunjucks::truthy($text)) {
            $parts[] = '    <p class="govuk-notification-banner__heading">
';
            $parts[] = '      '.Nunjucks::escape(Nunjucks::indent(Nunjucks::trim(Nunjucks::str($text)), 6, false)).'
';
            $parts[] = '    </p>
';
        }
        $parts[] = '  </div>
</div>';

        return implode('', $parts);
    }

    public static function renderSummaryList(Params $p): string
    {
        $card = $p->get('card');
        $cardTitle = Nunjucks::get($card, 'title');
        $anyRowHasActions = false;
        foreach (Nunjucks::items($p->get('rows')) as $row) {
            if (Nunjucks::length(Nunjucks::get($row, 'actions', 'items')) > 0) {
                $anyRowHasActions = true;
            }
        }
        $listParts = [];
        $listParts[] = '<dl class="govuk-summary-list'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
';
        foreach (Nunjucks::items($p->get('rows')) as $row) {
            if (! Nunjucks::truthy($row)) {
                continue;
            }
            [$key, $value, $actions] = [Nunjucks::get($row, 'key'), Nunjucks::get($row, 'value'), Nunjucks::get($row, 'actions')];
            $listParts[] = '  <div class="govuk-summary-list__row'.Nunjucks::flagIf(' govuk-summary-list__row--no-actions', ($anyRowHasActions && ! Nunjucks::truthy(Nunjucks::get($actions, 'items')))).Nunjucks::classesIf(Nunjucks::get($row, 'classes')).'">
';
            $listParts[] = '    <dt class="govuk-summary-list__key'.Nunjucks::classesIf(Nunjucks::get($key, 'classes')).'">
      '.Nunjucks::contentIndent($key, 'html', 'text', 6).'
    </dt>
';
            $listParts[] = '    <dd class="govuk-summary-list__value'.Nunjucks::classesIf(Nunjucks::get($value, 'classes')).'">
      '.Nunjucks::contentIndent($value, 'html', 'text', 6).'
    </dd>
';
            $entries = Nunjucks::items(Nunjucks::get($actions, 'items'));
            if (count($entries) > 0) {
                $listParts[] = '    <dd class="govuk-summary-list__actions'.Nunjucks::classesIf(Nunjucks::get($actions, 'classes')).'">
';
                if (count($entries) === 1) {
                    $listParts[] = Nunjucks::indent(Nunjucks::trim(self::summaryActionLink($entries[0], $cardTitle)), 6, true).'
';
                } else {
                    $listParts[] = '      <ul class="govuk-summary-list__actions-list">
';
                    foreach ($entries as $action) {
                        $listParts[] = '        <li class="govuk-summary-list__actions-list-item">
';
                        $listParts[] = '          '.Nunjucks::indent(Nunjucks::trim(self::summaryActionLink($action, $cardTitle)), 8, false).'
';
                        $listParts[] = '        </li>
';
                    }
                    $listParts[] = '      </ul>
';
                }
                $listParts[] = '    </dd>
';
            }
            $listParts[] = '  </div>
';
        }
        $listParts[] = '</dl>';
        if (Nunjucks::truthy($card)) {
            return self::summaryCard($card, Nunjucks::indent(Nunjucks::trim(implode('', $listParts)), 4, false));
        }

        return Nunjucks::trim(implode('', $listParts));
    }

    private static function summaryActionLink(mixed $action, mixed $cardTitle): string
    {
        $parts = [];
        $parts[] = '  <a class="govuk-link'.Nunjucks::classesIf(Nunjucks::get($action, 'classes')).'" href="'.Nunjucks::out(Nunjucks::get($action, 'href')).'"'.Attributes::render(Nunjucks::get($action, 'attributes')).'>';
        $html = Nunjucks::get($action, 'html');
        if (Nunjucks::truthy($html)) {
            $parts[] = Nunjucks::indent(Nunjucks::str($html), 4, false);
        } else {
            $parts[] = Nunjucks::out(Nunjucks::get($action, 'text'));
        }
        $visuallyHidden = Nunjucks::get($action, 'visuallyHiddenText');
        if ((Nunjucks::truthy($visuallyHidden) || Nunjucks::truthy($cardTitle))) {
            $parts[] = '<span class="govuk-visually-hidden">';
            if (Nunjucks::truthy($visuallyHidden)) {
                $parts[] = ' '.Nunjucks::out($visuallyHidden);
            }
            if (Nunjucks::truthy($cardTitle)) {
                $title = Nunjucks::out(Nunjucks::get($cardTitle, 'text'));
                $html = Nunjucks::get($cardTitle, 'html');
                if (Nunjucks::truthy($html)) {
                    $title = Nunjucks::indent(Nunjucks::str($html), 6, false);
                }
                $parts[] = ' ('.$title.')';
            }
            $parts[] = '</span>';
        }
        $parts[] = '</a>
';

        return implode('', $parts);
    }

    private static function summaryCard(mixed $card, string $body): string
    {
        $title = Nunjucks::get($card, 'title');
        $level = Nunjucks::heading(Nunjucks::get($title, 'headingLevel'), '2');
        $actions = Nunjucks::get($card, 'actions');
        $parts = [];
        $parts[] = '<div class="govuk-summary-card'.Nunjucks::classesIf(Nunjucks::get($card, 'classes')).'"'.Attributes::render(Nunjucks::get($card, 'attributes')).'>
';
        $parts[] = '  <div class="govuk-summary-card__title-wrapper">
';
        if (Nunjucks::truthy($title)) {
            $parts[] = '    <h'.$level.' class="govuk-summary-card__title'.Nunjucks::classesIf(Nunjucks::get($title, 'classes')).'">
      '.Nunjucks::contentIndent($title, 'html', 'text', 6).'
    </h'.$level.'>
';
        }
        $entries = Nunjucks::items(Nunjucks::get($actions, 'items'));
        if (count($entries) > 0) {
            if (count($entries) === 1) {
                $parts[] = '    <div class="govuk-summary-card__actions'.Nunjucks::classesIf(Nunjucks::get($actions, 'classes')).'">
';
                $parts[] = '      '.Nunjucks::indent(Nunjucks::trim(self::summaryActionLink($entries[0], $title)), 4, false).'
';
                $parts[] = '    </div>
';
            } else {
                $parts[] = '    <ul class="govuk-summary-card__actions'.Nunjucks::classesIf(Nunjucks::get($actions, 'classes')).'">
';
                foreach ($entries as $action) {
                    $parts[] = '      <li class="govuk-summary-card__action">
';
                    $parts[] = '        '.Nunjucks::indent(Nunjucks::trim(self::summaryActionLink($action, $title)), 8, false).'
';
                    $parts[] = '      </li>
';
                }
                $parts[] = '    </ul>
';
            }
        }
        $parts[] = '  </div>

';
        $parts[] = '  <div class="govuk-summary-card__content">
    '.$body.'
  </div>
</div>
';

        return implode('', $parts);
    }

    public static function renderTable(Params $p): string
    {
        $parts = [];
        $parts[] = '<table class="govuk-table'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
';
        $caption = $p->get('caption');
        if (Nunjucks::truthy($caption)) {
            $parts[] = '  <caption class="govuk-table__caption'.Nunjucks::classesIf($p->get('captionClasses')).'">'.Nunjucks::out($caption).'</caption>
';
        }
        $head = Nunjucks::items($p->get('head'));
        if (Nunjucks::truthy($p->get('head'))) {
            $parts[] = '  <thead class="govuk-table__head">
';
            $parts[] = '    <tr class="govuk-table__row">
';
            foreach ($head as $item) {
                $parts[] = '      <th scope="col" class="govuk-table__header'.self::formatClass('govuk-table__header--', Nunjucks::get($item, 'format')).Nunjucks::classesIf(Nunjucks::get($item, 'classes')).'"'.Nunjucks::attributeIf('colspan', Nunjucks::get($item, 'colspan')).Nunjucks::attributeIf('rowspan', Nunjucks::get($item, 'rowspan')).Attributes::render(Nunjucks::get($item, 'attributes')).'>'.Nunjucks::content($item, 'html', 'text').'</th>
';
            }
            $parts[] = '    </tr>
  </thead>
';
        }
        $parts[] = '  <tbody class="govuk-table__body">
';
        foreach (Nunjucks::items($p->get('rows')) as $row) {
            if (! Nunjucks::truthy($row)) {
                continue;
            }
            $parts[] = '    <tr class="govuk-table__row">
';
            foreach (Nunjucks::items($row) as $_i => $cell) {
                $index = $_i + 0;
                $common = Nunjucks::attributeIf('colspan', Nunjucks::get($cell, 'colspan')).Nunjucks::attributeIf('rowspan', Nunjucks::get($cell, 'rowspan')).Attributes::render(Nunjucks::get($cell, 'attributes'));
                if (($index === 0 && Nunjucks::truthy($p->get('firstCellIsHeader')))) {
                    $parts[] = '      <th scope="row" class="govuk-table__header'.Nunjucks::classesIf(Nunjucks::get($cell, 'classes')).'"'.$common.'>'.Nunjucks::content($cell, 'html', 'text').'</th>
';
                } else {
                    $parts[] = '      <td class="govuk-table__cell'.self::formatClass('govuk-table__cell--', Nunjucks::get($cell, 'format')).Nunjucks::classesIf(Nunjucks::get($cell, 'classes')).'"'.$common.'>'.Nunjucks::content($cell, 'html', 'text').'</td>
';
                }
            }
            $parts[] = '    </tr>
';
        }
        $parts[] = '  </tbody>
</table>';

        return implode('', $parts);
    }

    private static function formatClass(string $prefix, mixed $format_): string
    {
        if (! Nunjucks::truthy($format_)) {
            return '';
        }

        return ' '.$prefix.Nunjucks::out($format_);
    }

    public static function renderTabs(Params $p): string
    {
        $idPrefix = '';
        $prefix = $p->get('idPrefix');
        if (Nunjucks::truthy($prefix)) {
            $idPrefix = Nunjucks::str($prefix);
        }
        $parts = [];
        $parts[] = '<div'.Nunjucks::attributeIf('id', $p->get('id')).' class="govuk-tabs'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).' data-module="govuk-tabs">
';
        $parts[] = '  <h2 class="govuk-tabs__title">
    '.Nunjucks::out(Nunjucks::def($p->get('title'), 'Contents')).'
  </h2>
';
        $entries = Nunjucks::items($p->get('items'));
        if (count($entries) > 0) {
            $parts[] = '  <ul class="govuk-tabs__list">
';
            foreach ($entries as $_i => $item) {
                $index = $_i + 0;
                if (! Nunjucks::truthy($item)) {
                    continue;
                }
                $parts[] = Nunjucks::indent(Nunjucks::trim(self::tabListItem($item, ($index + 1), $idPrefix)), 4, true).'
';
            }
            $parts[] = '  </ul>
';
            foreach ($entries as $_i => $item) {
                $index = $_i + 0;
                if (! Nunjucks::truthy($item)) {
                    continue;
                }
                $parts[] = Nunjucks::indent(Nunjucks::trim(self::tabPanel($item, ($index + 1), $idPrefix)), 2, true).'
';
            }
        }
        $parts[] = '</div>';

        return implode('', $parts);
    }

    private static function tabPanelId(mixed $item, int $index, string $idPrefix): string
    {
        $id_ = Nunjucks::get($item, 'id');
        if (Nunjucks::truthy($id_)) {
            return Nunjucks::str($id_);
        }

        return $idPrefix.'-'.(string) ($index);
    }

    private static function tabListItem(mixed $item, int $index, string $idPrefix): string
    {
        return '<li class="govuk-tabs__list-item'.Nunjucks::flagIf(' govuk-tabs__list-item--selected', $index === 1).'">
'.'  <a class="govuk-tabs__tab" href="#'.Nunjucks::escape(self::tabPanelId($item, $index, $idPrefix)).'"'.Attributes::render(Nunjucks::get($item, 'attributes')).'>
    '.Nunjucks::out(Nunjucks::get($item, 'label')).'
  </a>
</li>
';
    }

    private static function tabPanel(mixed $item, int $index, string $idPrefix): string
    {
        $panel = Nunjucks::get($item, 'panel');
        $parts = [];
        $parts[] = '<div class="govuk-tabs__panel'.Nunjucks::flagIf(' govuk-tabs__panel--hidden', $index > 1).'" id="'.Nunjucks::escape(self::tabPanelId($item, $index, $idPrefix)).'"'.Attributes::render(Nunjucks::get($panel, 'attributes')).'>
';
        [$html, $text] = [Nunjucks::get($panel, 'html'), Nunjucks::get($panel, 'text')];
        if (Nunjucks::truthy($html)) {
            $parts[] = '  '.Nunjucks::indent(Nunjucks::trim(Nunjucks::str($html)), 2, false).'
';
        } elseif (Nunjucks::truthy($text)) {
            $parts[] = '  <p class="govuk-body">'.Nunjucks::out($text).'</p>
';
        }
        $parts[] = '</div>
';

        return implode('', $parts);
    }

    public static function renderTaskList(Params $p): string
    {
        $idPrefix = 'task-list';
        $prefix = $p->get('idPrefix');
        if (Nunjucks::truthy($prefix)) {
            $idPrefix = Nunjucks::str($prefix);
        }
        $parts = [];
        $parts[] = '<ul class="govuk-task-list'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
';
        foreach (Nunjucks::items($p->get('items')) as $_i => $item) {
            $index = $_i + 0;
            if (Nunjucks::truthy($item)) {
                $parts[] = self::taskListItem($item, ($index + 1), $idPrefix).'
';
            } else {
                $parts[] = '
';
            }
        }
        $parts[] = '</ul>';

        return implode('', $parts);
    }

    private static function taskListItem(mixed $item, int $index, string $idPrefix): string
    {
        $position = (string) ($index);
        $hintId = $idPrefix.'-'.$position.'-hint';
        $statusId = $idPrefix.'-'.$position.'-status';
        $title = Nunjucks::get($item, 'title');
        $hint = Nunjucks::get($item, 'hint');
        $status = Nunjucks::get($item, 'status');
        $parts = [];
        $parts[] = '  <li class="govuk-task-list__item'.Nunjucks::flagIf(' govuk-task-list__item--with-link', Nunjucks::get($item, 'href')).Nunjucks::classesIf(Nunjucks::get($item, 'classes')).'">
';
        $parts[] = '    <div class="govuk-task-list__name-and-hint">
';
        $href = Nunjucks::get($item, 'href');
        if (Nunjucks::truthy($href)) {
            $describedBy = $statusId;
            if (Nunjucks::truthy($hint)) {
                $describedBy = $hintId.' '.$statusId;
            }
            $parts[] = '      <a class="govuk-link govuk-task-list__link'.Nunjucks::classesIf(Nunjucks::get($title, 'classes')).'" href="'.Nunjucks::out($href).'" aria-describedby="'.Nunjucks::escape($describedBy).'">
';
            $parts[] = '        '.Nunjucks::contentIndent($title, 'html', 'text', 8).'
';
            $parts[] = '      </a>
';
        } else {
            $parts[] = '      <div'.Nunjucks::attributeIf('class', Nunjucks::get($title, 'classes')).'>
';
            $parts[] = '        '.Nunjucks::contentIndent($title, 'html', 'text', 8).'
';
            $parts[] = '      </div>
';
        }
        if (Nunjucks::truthy($hint)) {
            $parts[] = '      <div id="'.Nunjucks::escape($hintId).'" class="govuk-task-list__hint">
';
            $parts[] = '        '.Nunjucks::contentIndent($hint, 'html', 'text', 8).'
';
            $parts[] = '      </div>
';
        }
        $parts[] = '    </div>
';
        $parts[] = '    <div class="govuk-task-list__status'.Nunjucks::classesIf(Nunjucks::get($status, 'classes')).'" id="'.Nunjucks::escape($statusId).'">
';
        $tag = Nunjucks::get($status, 'tag');
        if (Nunjucks::truthy($tag)) {
            $tagParams = (($tag instanceof Params) ? $tag : null);
            $parts[] = '      '.Nunjucks::indent(Nunjucks::trim(Text::renderTag($tagParams)), 6, false).'
';
        } else {
            $parts[] = '      '.Nunjucks::contentIndent($status, 'html', 'text', 6).'
';
        }
        $parts[] = '    </div>
  </li>';

        return implode('', $parts);
    }
}

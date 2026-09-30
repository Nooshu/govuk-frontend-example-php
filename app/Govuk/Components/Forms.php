<?php

declare(strict_types=1);

namespace App\Govuk\Components;

use App\Govuk\Attributes;
use App\Govuk\Nunjucks;
use App\Govuk\Params;
use App\Govuk\SafeString;

/**
 * Ported from GOV.UK Frontend macros (via Python/Go reference) for byte-for-byte fixture parity.
 */
final class Forms
{
    private static function formGroupOpen(Params $p): string
    {
        $formGroup = $p->get('formGroup');

        return '<div class="govuk-form-group'.Nunjucks::flagIf(' govuk-form-group--error', $p->get('errorMessage')).Nunjucks::classesIf(Nunjucks::get($formGroup, 'classes')).'"'.Attributes::render(Nunjucks::get($formGroup, 'attributes')).'>
';
    }

    private static function describedByAppend(string $describedBy, string $id_): string
    {
        if ($describedBy !== '') {
            return $describedBy.' '.$id_;
        }

        return $id_;
    }

    private static function hintBlock(Params $p, string $id_, string $describedBy, int $width): array
    {
        $hint = $p->get('hint');
        if (! Nunjucks::truthy($hint)) {
            return ['', $describedBy];
        }
        $hintId = $id_.'-hint';
        $describedBy = self::describedByAppend($describedBy, $hintId);
        $html = Text::renderHint(Params::make('id', $hintId, 'classes', Nunjucks::get($hint, 'classes'), 'attributes', Nunjucks::get($hint, 'attributes'), 'html', Nunjucks::get($hint, 'html'), 'text', Nunjucks::get($hint, 'text')));

        return [str_repeat(' ', $width).Nunjucks::indent(Nunjucks::trim($html), $width, false).'
', $describedBy];
    }

    private static function errorBlock(Params $p, string $id_, string $describedBy, int $width): array
    {
        $message = $p->get('errorMessage');
        if (! Nunjucks::truthy($message)) {
            return ['', $describedBy];
        }
        $errorId = $id_.'-error';
        $describedBy = self::describedByAppend($describedBy, $errorId);
        $html = Text::renderErrorMessage(Params::make('id', $errorId, 'classes', Nunjucks::get($message, 'classes'), 'attributes', Nunjucks::get($message, 'attributes'), 'html', Nunjucks::get($message, 'html'), 'text', Nunjucks::get($message, 'text'), 'visuallyHiddenText', Nunjucks::get($message, 'visuallyHiddenText')));

        return [str_repeat(' ', $width).Nunjucks::indent(Nunjucks::trim($html), $width, false).'
', $describedBy];
    }

    private static function labelBlock(Params $p, mixed $id_, int $width): string
    {
        $label = $p->get('label');
        $html = Text::renderLabel(Params::make('html', Nunjucks::get($label, 'html'), 'text', Nunjucks::get($label, 'text'), 'classes', Nunjucks::get($label, 'classes'), 'isPageHeading', Nunjucks::get($label, 'isPageHeading'), 'attributes', Nunjucks::get($label, 'attributes'), 'for', $id_));

        return str_repeat(' ', $width).Nunjucks::indent(Nunjucks::trim($html), $width, false).'
';
    }

    private static function slotContent(mixed $slot, int $width, bool $indentFirst): string
    {
        $html = Nunjucks::get($slot, 'html');
        if (Nunjucks::truthy($html)) {
            return Nunjucks::indent(Nunjucks::trim(Nunjucks::str($html)), $width, $indentFirst);
        }

        return Nunjucks::out(Nunjucks::get($slot, 'text'));
    }

    private static function componentId(Params $p): mixed
    {
        $id_ = $p->get('id');
        if (Nunjucks::truthy($id_)) {
            return $id_;
        }

        return $p->get('name');
    }

    public static function renderInput(Params $p): string
    {
        $classNames = 'govuk-input';
        $classes = $p->get('classes');
        if (Nunjucks::truthy($classes)) {
            $classNames .= ' '.Nunjucks::str($classes);
        }
        if (Nunjucks::truthy($p->get('errorMessage'))) {
            $classNames .= ' govuk-input--error';
        }
        $id_ = self::componentId($p);
        $describedBy = '';
        $supplied = $p->get('describedBy');
        if (Nunjucks::truthy($supplied)) {
            $describedBy = Nunjucks::str($supplied);
        }
        $formGroup = $p->get('formGroup');
        [$prefix, $suffix] = [$p->get('prefix'), $p->get('suffix')];
        [$beforeInput, $afterInput] = [Nunjucks::get($formGroup, 'beforeInput'), Nunjucks::get($formGroup, 'afterInput')];
        $hasPrefix = (Nunjucks::truthy($prefix) && (Nunjucks::truthy(Nunjucks::get($prefix, 'text')) || Nunjucks::truthy(Nunjucks::get($prefix, 'html'))));
        $hasSuffix = (Nunjucks::truthy($suffix) && (Nunjucks::truthy(Nunjucks::get($suffix, 'text')) || Nunjucks::truthy(Nunjucks::get($suffix, 'html'))));
        $hasBefore = (Nunjucks::truthy($beforeInput) && (Nunjucks::truthy(Nunjucks::get($beforeInput, 'text')) || Nunjucks::truthy(Nunjucks::get($beforeInput, 'html'))));
        $hasAfter = (Nunjucks::truthy($afterInput) && (Nunjucks::truthy(Nunjucks::get($afterInput, 'text')) || Nunjucks::truthy(Nunjucks::get($afterInput, 'html'))));
        $parts = [];
        $parts[] = self::formGroupOpen($p);
        $parts[] = self::labelBlock($p, $id_, 2);
        [$hint, $describedBy] = self::hintBlock($p, Nunjucks::str($id_), $describedBy, 2);
        $parts[] = $hint;
        [$errorMessage, $describedBy] = self::errorBlock($p, Nunjucks::str($id_), $describedBy, 2);
        $parts[] = $errorMessage;
        $element = self::inputElement($p, $classNames, $id_, $describedBy);
        if (($hasPrefix || $hasSuffix || $hasBefore || $hasAfter)) {
            $wrapper = $p->get('inputWrapper');
            $parts[] = '  <div class="govuk-input__wrapper'.Nunjucks::classesIf(Nunjucks::get($wrapper, 'classes')).'"'.Attributes::render(Nunjucks::get($wrapper, 'attributes')).'>
';
            if ($hasBefore) {
                $parts[] = self::slotContent($beforeInput, 4, true).'
';
            }
            if ($hasPrefix) {
                $parts[] = Nunjucks::indent(self::affixItem($prefix, 'prefix'), 2, true).'
';
            }
            $parts[] = '    '.$element.'
';
            if ($hasSuffix) {
                $parts[] = Nunjucks::indent(self::affixItem($suffix, 'suffix'), 2, true).'
';
            }
            if ($hasAfter) {
                $parts[] = self::slotContent($afterInput, 4, true).'
';
            }
            $parts[] = '  </div>
';
        } else {
            $parts[] = '  '.$element.'
';
        }
        $parts[] = '</div>';

        return implode('', $parts);
    }

    private static function inputElement(Params $p, string $classNames, mixed $id_, string $describedBy): string
    {
        $spellcheck = false;
        $flag = $p->get('spellcheck');
        if (is_bool($flag)) {
            $spellcheck = ($flag ? 'true' : 'false');
        }
        $ariaDescribedBy = Nunjucks::undefined();
        if ($describedBy !== '') {
            $ariaDescribedBy = $describedBy;
        }
        $attributes = Params::make('class', $classNames, 'id', $id_, 'name', $p->get('name'), 'type', Nunjucks::defTruthy($p->get('type'), 'text'), 'spellcheck', Params::make('value', $spellcheck, 'optional', true), 'value', Params::make('value', $p->get('value'), 'optional', true), 'disabled', Params::make('value', $p->get('disabled'), 'optional', true), 'aria-describedby', Params::make('value', $ariaDescribedBy, 'optional', true), 'autocomplete', Params::make('value', $p->get('autocomplete'), 'optional', true), 'autocapitalize', Params::make('value', $p->get('autocapitalize'), 'optional', true), 'pattern', Params::make('value', $p->get('pattern'), 'optional', true), 'inputmode', Params::make('value', $p->get('inputmode'), 'optional', true));

        return '<input'.Attributes::render($attributes).Attributes::render($p->get('attributes')).'>';
    }

    private static function affixItem(mixed $affix, string $kind): string
    {
        return '  <div class="govuk-input__'.$kind.Nunjucks::classesIf(Nunjucks::get($affix, 'classes')).'" aria-hidden="true"'.Attributes::render(Nunjucks::get($affix, 'attributes')).'>'.Nunjucks::contentIndent($affix, 'html', 'text', 4).'</div>';
    }

    public static function renderTextarea(Params $p): string
    {
        $id_ = self::componentId($p);
        $describedBy = '';
        $supplied = $p->get('describedBy');
        if (Nunjucks::truthy($supplied)) {
            $describedBy = Nunjucks::str($supplied);
        }
        $formGroup = $p->get('formGroup');
        $parts = [];
        $parts[] = self::formGroupOpen($p);
        $parts[] = self::labelBlock($p, $id_, 2);
        [$hint, $describedBy] = self::hintBlock($p, Nunjucks::str($id_), $describedBy, 2);
        $parts[] = $hint;
        [$errorMessage, $describedBy] = self::errorBlock($p, Nunjucks::str($id_), $describedBy, 2);
        $parts[] = $errorMessage;
        $before = Nunjucks::get($formGroup, 'beforeInput');
        if (Nunjucks::truthy($before)) {
            $parts[] = '  '.self::slotContent($before, 2, false).'
';
        }
        $spellcheck = '';
        $flag = $p->get('spellcheck');
        if (is_bool($flag)) {
            $spellcheck = ' spellcheck="'.($flag ? 'true' : 'false').'"';
        }
        $parts[] = '  <textarea class="govuk-textarea'.Nunjucks::flagIf(' govuk-textarea--error', $p->get('errorMessage')).Nunjucks::classesIf($p->get('classes')).'" id="'.Nunjucks::out($id_).'" name="'.Nunjucks::out($p->get('name')).'" rows="'.Nunjucks::out(Nunjucks::defTruthy($p->get('rows'), '5')).'"'.$spellcheck.Nunjucks::flagIf(' disabled', $p->get('disabled')).Nunjucks::attributeIf('aria-describedby', $describedBy).Nunjucks::attributeIf('autocomplete', $p->get('autocomplete')).Attributes::render($p->get('attributes')).'>'.Nunjucks::out($p->get('value')).'</textarea>
';
        $after = Nunjucks::get($formGroup, 'afterInput');
        if (Nunjucks::truthy($after)) {
            $parts[] = '  '.self::slotContent($after, 2, false).'
';
        }
        $parts[] = '</div>';

        return implode('', $parts);
    }

    public static function renderSelect(Params $p): string
    {
        $id_ = self::componentId($p);
        $describedBy = '';
        $supplied = $p->get('describedBy');
        if (Nunjucks::truthy($supplied)) {
            $describedBy = Nunjucks::str($supplied);
        }
        $formGroup = $p->get('formGroup');
        $parts = [];
        $parts[] = self::formGroupOpen($p);
        $parts[] = self::labelBlock($p, $id_, 2);
        [$hint, $describedBy] = self::hintBlock($p, Nunjucks::str($id_), $describedBy, 2);
        $parts[] = $hint;
        [$errorMessage, $describedBy] = self::errorBlock($p, Nunjucks::str($id_), $describedBy, 2);
        $parts[] = $errorMessage;
        $before = Nunjucks::get($formGroup, 'beforeInput');
        if (Nunjucks::truthy($before)) {
            $parts[] = '  '.self::slotContent($before, 2, false).'
';
        }
        $parts[] = '  <select class="govuk-select'.Nunjucks::classesIf($p->get('classes')).Nunjucks::flagIf(' govuk-select--error', $p->get('errorMessage')).'" id="'.Nunjucks::out($id_).'" name="'.Nunjucks::out($p->get('name')).'"'.Nunjucks::flagIf(' disabled', $p->get('disabled')).Nunjucks::attributeIf('aria-describedby', $describedBy).Attributes::render($p->get('attributes')).'>
';
        $selected = $p->get('value');
        foreach (Nunjucks::items($p->get('items')) as $item) {
            if (! Nunjucks::truthy($item)) {
                continue;
            }
            $value = Nunjucks::get($item, 'value');
            $effective = Nunjucks::def($value, Nunjucks::get($item, 'text'));
            $isSelected = Nunjucks::truthy(Nunjucks::get($item, 'selected'));
            if ((! $isSelected && Nunjucks::truthy($selected))) {
                $isSelected = (Nunjucks::looseEq($effective, $selected) && ! Nunjucks::looseEq(Nunjucks::get($item, 'selected'), false));
            }
            $parts[] = '    <option';
            if (! Nunjucks::isUndefined($value)) {
                $parts[] = ' value="'.Nunjucks::out($value).'"';
            }
            $parts[] = Nunjucks::flagIf(' selected', $isSelected).Nunjucks::flagIf(' disabled', Nunjucks::get($item, 'disabled')).Attributes::render(Nunjucks::get($item, 'attributes')).'>'.Nunjucks::out(Nunjucks::get($item, 'text')).'</option>
';
        }
        $parts[] = '  </select>
';
        $after = Nunjucks::get($formGroup, 'afterInput');
        if (Nunjucks::truthy($after)) {
            $parts[] = '  '.self::slotContent($after, 2, false).'
';
        }
        $parts[] = '</div>';

        return implode('', $parts);
    }

    public static function renderFileUpload(Params $p): string
    {
        $id_ = self::componentId($p);
        $describedBy = '';
        $supplied = $p->get('describedBy');
        if (Nunjucks::truthy($supplied)) {
            $describedBy = Nunjucks::str($supplied);
        }
        $formGroup = $p->get('formGroup');
        $parts = [];
        $parts[] = self::formGroupOpen($p);
        $parts[] = self::labelBlock($p, $id_, 2);
        [$hint, $describedBy] = self::hintBlock($p, Nunjucks::str($id_), $describedBy, 2);
        $parts[] = $hint;
        [$errorMessage, $describedBy] = self::errorBlock($p, Nunjucks::str($id_), $describedBy, 2);
        $parts[] = $errorMessage;
        $before = Nunjucks::get($formGroup, 'beforeInput');
        if (Nunjucks::truthy($before)) {
            $parts[] = '  '.self::slotContent($before, 2, false).'
';
        }
        $javascript = Nunjucks::truthy($p->get('javascript'));
        if ($javascript) {
            $parts[] = '  <div
    class="govuk-file-upload-wrapper'.Nunjucks::classesIf($p->get('wrapperClasses')).'"
    data-module="govuk-file-upload"'.Attributes::i18n('choose-files-button', $p->get('chooseFilesButtonText'), Nunjucks::undefined()).Attributes::i18n('no-file-chosen', $p->get('noFileChosenText'), Nunjucks::undefined()).Attributes::i18n('multiple-files-chosen', Nunjucks::undefined(), $p->get('multipleFilesChosenText')).Attributes::i18n('drop-instruction', $p->get('dropInstructionText'), Nunjucks::undefined()).Attributes::i18n('entered-drop-zone', $p->get('enteredDropZoneText'), Nunjucks::undefined()).Attributes::i18n('left-drop-zone', $p->get('leftDropZoneText'), Nunjucks::undefined()).Attributes::render($p->get('wrapperAttributes')).'
  >
';
        }
        $parts[] = '  <input class="govuk-file-upload'.Nunjucks::classesIf($p->get('classes')).Nunjucks::flagIf(' govuk-file-upload--error', $p->get('errorMessage')).'" id="'.Nunjucks::out($id_).'" name="'.Nunjucks::out($p->get('name')).'" type="file"'.Nunjucks::flagIf(' disabled', $p->get('disabled')).Nunjucks::flagIf(' multiple', $p->get('multiple')).Nunjucks::attributeIf('aria-describedby', $describedBy).Attributes::render($p->get('attributes')).'>
';
        if ($javascript) {
            $parts[] = '  </div>
';
        }
        $after = Nunjucks::get($formGroup, 'afterInput');
        if (Nunjucks::truthy($after)) {
            $parts[] = '  '.self::slotContent($after, 2, false).'
';
        }
        $parts[] = '</div>';

        return implode('', $parts);
    }

    public static function renderCharacterCount(Params $p): string
    {
        [$maxwords, $maxlength] = [$p->get('maxwords'), $p->get('maxlength')];
        $hasNoLimit = (! Nunjucks::truthy($maxwords) && ! Nunjucks::truthy($maxlength));
        $id_ = self::componentId($p);
        $descriptionNoLimit = Nunjucks::undefined();
        if (! $hasNoLimit) {
            $limit = $maxlength;
            if (Nunjucks::truthy($maxwords)) {
                $limit = $maxwords;
            }
            $unit = 'characters';
            if (Nunjucks::truthy($maxwords)) {
                $unit = 'words';
            }
            $description = 'You can enter up to %{count} '.$unit;
            $supplied = $p->get('textareaDescriptionText');
            if (Nunjucks::truthy($supplied)) {
                $description = Nunjucks::str($supplied);
            }
            $descriptionNoLimit = str_replace('%{count}', Nunjucks::str($limit), $description);
        }
        $countMessage = $p->get('countMessage');
        $countMessageHtml = Nunjucks::trim(Text::renderHint(Params::make('text', $descriptionNoLimit, 'id', Nunjucks::str($id_).'-info', 'classes', 'govuk-character-count__message'.Nunjucks::concatIf(' ', Nunjucks::get($countMessage, 'classes'))))).'
';
        $formGroup = $p->get('formGroup');
        $after = Nunjucks::get($formGroup, 'afterInput');
        if (Nunjucks::truthy($after)) {
            $html = Nunjucks::get($after, 'html');
            if (Nunjucks::truthy($html)) {
                $countMessageHtml .= Nunjucks::trim(Nunjucks::str($html)).'
';
            } else {
                $countMessageHtml .= Nunjucks::out(Nunjucks::get($after, 'text')).'
';
            }
        }
        $attributesHtml = Attributes::render(Params::make('data-module', 'govuk-character-count', 'data-maxlength', Params::make('value', $maxlength, 'optional', true), 'data-threshold', Params::make('value', $p->get('threshold'), 'optional', true), 'data-maxwords', Params::make('value', $maxwords, 'optional', true)));
        $description = $p->get('textareaDescriptionText');
        if (($hasNoLimit && Nunjucks::truthy($description))) {
            $attributesHtml .= Attributes::i18n('textarea-description', Nunjucks::undefined(), Params::make('other', $description));
        }
        $attributesHtml .= Attributes::i18n('characters-under-limit', Nunjucks::undefined(), $p->get('charactersUnderLimitText')).Attributes::i18n('characters-at-limit', $p->get('charactersAtLimitText'), Nunjucks::undefined()).Attributes::i18n('characters-over-limit', Nunjucks::undefined(), $p->get('charactersOverLimitText')).Attributes::i18n('words-under-limit', Nunjucks::undefined(), $p->get('wordsUnderLimitText')).Attributes::i18n('words-at-limit', $p->get('wordsAtLimitText'), Nunjucks::undefined()).Attributes::i18n('words-over-limit', Nunjucks::undefined(), $p->get('wordsOverLimitText'));
        $attributesHtml .= self::appendedAttributes(Nunjucks::get($formGroup, 'attributes'));
        $label = $p->get('label');

        return Nunjucks::trim(self::renderTextarea(Params::make('id', $id_, 'name', $p->get('name'), 'describedBy', Nunjucks::str($id_).'-info', 'rows', $p->get('rows'), 'spellcheck', $p->get('spellcheck'), 'value', $p->get('value'), 'formGroup', Params::make('classes', 'govuk-character-count'.Nunjucks::concatIf(' ', Nunjucks::get($formGroup, 'classes')), 'attributes', $attributesHtml, 'beforeInput', Nunjucks::get($formGroup, 'beforeInput'), 'afterInput', Params::make('html', new SafeString($countMessageHtml))), 'classes', 'govuk-js-character-count'.Nunjucks::concatIf(' ', $p->get('classes')), 'label', Params::make('html', Nunjucks::get($label, 'html'), 'text', Nunjucks::get($label, 'text'), 'classes', Nunjucks::get($label, 'classes'), 'isPageHeading', Nunjucks::get($label, 'isPageHeading'), 'attributes', Nunjucks::get($label, 'attributes'), 'for', $id_), 'hint', $p->get('hint'), 'errorMessage', $p->get('errorMessage'), 'attributes', $p->get('attributes'))));
    }

    private static function appendedAttributes(mixed $attributes): string
    {
        if (! ($attributes instanceof Params)) {
            return '';
        }
        $parts = [];
        foreach ($attributes as $name) {
            $parts[] = ' '.Nunjucks::escape($name).'="'.Nunjucks::escape(Nunjucks::str($attributes->get($name))).'"';
        }

        return implode('', $parts);
    }

    public static function renderPasswordInput(Params $p): string
    {
        $id_ = self::componentId($p);
        $formGroup = $p->get('formGroup');
        $attributesHtml = ' data-module="govuk-password-input"'.Attributes::i18n('show-password', $p->get('showPasswordText'), Nunjucks::undefined()).Attributes::i18n('hide-password', $p->get('hidePasswordText'), Nunjucks::undefined()).Attributes::i18n('show-password-aria-label', $p->get('showPasswordAriaLabelText'), Nunjucks::undefined()).Attributes::i18n('hide-password-aria-label', $p->get('hidePasswordAriaLabelText'), Nunjucks::undefined()).Attributes::i18n('password-shown-announcement', $p->get('passwordShownAnnouncementText'), Nunjucks::undefined()).Attributes::i18n('password-hidden-announcement', $p->get('passwordHiddenAnnouncementText'), Nunjucks::undefined()).self::appendedAttributes(Nunjucks::get($formGroup, 'attributes'));
        $button = $p->get('button');
        $buttonHtml = Nunjucks::trim(Button::renderButton(Params::make('type', 'button', 'classes', 'govuk-button--secondary govuk-password-input__toggle govuk-js-password-input-toggle'.Nunjucks::concatIf(' ', Nunjucks::get($button, 'classes')), 'text', Nunjucks::def($p->get('showPasswordText'), 'Show'), 'attributes', Params::make('aria-controls', $id_, 'aria-label', Nunjucks::def($p->get('showPasswordAriaLabelText'), 'Show password'), 'hidden', Params::make('value', true, 'optional', true))))).'
';
        $after = Nunjucks::get($formGroup, 'afterInput');
        if (Nunjucks::truthy($after)) {
            $html = Nunjucks::get($after, 'html');
            if (Nunjucks::truthy($html)) {
                $buttonHtml .= Nunjucks::trim(Nunjucks::str($html)).'
';
            } else {
                $buttonHtml .= Nunjucks::out(Nunjucks::get($after, 'text')).'
';
            }
        }

        return Nunjucks::trim(self::renderInput(Params::make('formGroup', Params::make('classes', 'govuk-password-input'.Nunjucks::concatIf(' ', Nunjucks::get($formGroup, 'classes')), 'attributes', $attributesHtml, 'beforeInput', Nunjucks::get($formGroup, 'beforeInput'), 'afterInput', Params::make('html', new SafeString($buttonHtml))), 'inputWrapper', Params::make('classes', 'govuk-password-input__wrapper'), 'label', $p->get('label'), 'hint', $p->get('hint'), 'classes', 'govuk-password-input__input govuk-js-password-input-input'.Nunjucks::concatIf(' ', $p->get('classes')), 'errorMessage', $p->get('errorMessage'), 'id', $id_, 'name', $p->get('name'), 'type', 'password', 'spellcheck', false, 'autocapitalize', 'none', 'autocomplete', Nunjucks::defTruthy($p->get('autocomplete'), 'current-password'), 'value', $p->get('value'), 'disabled', $p->get('disabled'), 'describedBy', $p->get('describedBy'), 'attributes', $p->get('attributes'))));
    }

    public static function renderCheckboxes(Params $p): string
    {
        $idPrefix = $p->get('idPrefix');
        if (! Nunjucks::truthy($idPrefix)) {
            $idPrefix = $p->get('name');
        }
        $fieldset = $p->get('fieldset');
        $describedBy = '';
        $supplied = $p->get('describedBy');
        if (Nunjucks::truthy($supplied)) {
            $describedBy = Nunjucks::str($supplied);
        }
        $supplied = Nunjucks::get($fieldset, 'describedBy');
        if (Nunjucks::truthy($supplied)) {
            $describedBy = Nunjucks::str($supplied);
        }
        $hasFieldset = Nunjucks::truthy($fieldset);
        $inner = [];
        [$hint, $describedBy] = self::hintBlock($p, Nunjucks::str($idPrefix), $describedBy, 2);
        $inner[] = $hint;
        [$errorMessage, $describedBy] = self::errorBlock($p, Nunjucks::str($idPrefix), $describedBy, 2);
        $inner[] = $errorMessage;
        $formGroup = $p->get('formGroup');
        $inner[] = '  <div class="govuk-checkboxes'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).' data-module="govuk-checkboxes">
';
        $before = Nunjucks::get($formGroup, 'beforeInputs');
        if (Nunjucks::truthy($before)) {
            $inner[] = '    '.self::slotContent($before, 4, false).'
';
        }
        foreach (Nunjucks::items($p->get('items')) as $_i => $item) {
            $index = $_i + 0;
            if (! Nunjucks::truthy($item)) {
                continue;
            }
            $inner[] = self::checkboxItem($p, $item, ($index + 1), Nunjucks::str($idPrefix), $describedBy, $hasFieldset);
        }
        $after = Nunjucks::get($formGroup, 'afterInputs');
        if (Nunjucks::truthy($after)) {
            $inner[] = '    '.self::slotContent($after, 4, false).'
';
        }
        $inner[] = '  </div>
';

        return self::fieldsetWrapper($p, implode('', $inner), $describedBy, Nunjucks::undefined(), false);
    }

    private static function fieldsetWrapper(Params $p, string $inner, string $describedBy, mixed $role, bool $indentFieldset): string
    {
        $fieldset = $p->get('fieldset');
        $body = Nunjucks::trim($inner);
        if (Nunjucks::truthy($fieldset)) {
            $html = Text::renderFieldset(Params::make('describedBy', $describedBy, 'classes', Nunjucks::get($fieldset, 'classes'), 'role', $role, 'attributes', Nunjucks::get($fieldset, 'attributes'), 'legend', Nunjucks::get($fieldset, 'legend'), 'html', new SafeString($body)));
            $body = Nunjucks::trim($html);
            if ($indentFieldset) {
                $body = Nunjucks::indent($body, 2, false);
            }
        }

        return self::formGroupOpen($p).'  '.$body.'
</div>';
    }

    private static function checkboxItem(Params $p, mixed $item, int $index, string $idPrefix, string $describedBy, bool $hasFieldset): string
    {
        $itemId = $idPrefix;
        if ($index > 1) {
            $itemId .= '-'.(string) ($index);
        }
        $supplied = Nunjucks::get($item, 'id');
        if (Nunjucks::truthy($supplied)) {
            $itemId = Nunjucks::str($supplied);
        }
        $itemName = $p->get('name');
        $supplied = Nunjucks::get($item, 'name');
        if (Nunjucks::truthy($supplied)) {
            $itemName = $supplied;
        }
        $conditionalId = 'conditional-'.$itemId;
        $divider = Nunjucks::get($item, 'divider');
        if (Nunjucks::truthy($divider)) {
            return '    <div class="govuk-checkboxes__divider">'.Nunjucks::out($divider).'</div>
';
        }
        $checked = Nunjucks::truthy(Nunjucks::get($item, 'checked'));
        if ((! $checked && Nunjucks::truthy($p->get('values')))) {
            $checked = (Nunjucks::contains(Nunjucks::get($item, 'value'), $p->get('values')) && ! Nunjucks::looseEq(Nunjucks::get($item, 'checked'), false));
        }
        $hint = Nunjucks::get($item, 'hint');
        $hasHint = (Nunjucks::truthy(Nunjucks::get($hint, 'text')) || Nunjucks::truthy(Nunjucks::get($hint, 'html')));
        $itemHintId = '';
        if ($hasHint) {
            $itemHintId = $itemId.'-item-hint';
        }
        $itemDescribedBy = '';
        if (! $hasFieldset) {
            $itemDescribedBy = $describedBy;
        }
        $itemDescribedBy = Nunjucks::trim($itemDescribedBy.' '.$itemHintId);
        $conditional = Nunjucks::get($item, 'conditional');
        $label = Nunjucks::get($item, 'label');
        $parts = [];
        $parts[] = '    <div class="govuk-checkboxes__item">
';
        $parts[] = '      <input class="govuk-checkboxes__input" id="'.Nunjucks::escape($itemId).'" name="'.Nunjucks::out($itemName).'" type="checkbox" value="'.Nunjucks::out(Nunjucks::get($item, 'value')).'"'.Nunjucks::flagIf(' checked', $checked).Nunjucks::flagIf(' disabled', Nunjucks::get($item, 'disabled')).Nunjucks::attributeIf('data-aria-controls', self::ifTruthy(Nunjucks::get($conditional, 'html'), $conditionalId)).Nunjucks::attributeIf('data-behaviour', Nunjucks::get($item, 'behaviour')).Nunjucks::attributeIf('aria-describedby', $itemDescribedBy).Attributes::render(Nunjucks::get($item, 'attributes')).'>
';
        $parts[] = '      '.Nunjucks::indent(Nunjucks::trim(Text::renderLabel(Params::make('html', Nunjucks::get($item, 'html'), 'text', Nunjucks::get($item, 'text'), 'classes', 'govuk-checkboxes__label'.Nunjucks::concatIf(' ', Nunjucks::get($label, 'classes')), 'attributes', Nunjucks::get($label, 'attributes'), 'for', $itemId))), 6, false).'
';
        if ($hasHint) {
            $parts[] = '      '.Nunjucks::indent(Nunjucks::trim(Text::renderHint(Params::make('id', $itemHintId, 'classes', 'govuk-checkboxes__hint'.Nunjucks::concatIf(' ', Nunjucks::get($hint, 'classes')), 'attributes', Nunjucks::get($hint, 'attributes'), 'html', Nunjucks::get($hint, 'html'), 'text', Nunjucks::get($hint, 'text')))), 6, false).'
';
        }
        $parts[] = '    </div>
';
        $html = Nunjucks::get($conditional, 'html');
        if (Nunjucks::truthy($html)) {
            $parts[] = '    <div class="govuk-checkboxes__conditional'.Nunjucks::flagIf(' govuk-checkboxes__conditional--hidden', ! $checked).'" id="'.Nunjucks::escape($conditionalId).'">
      '.Nunjucks::trim(Nunjucks::str($html)).'
    </div>
';
        }

        return implode('', $parts);
    }

    private static function ifTruthy(mixed $condition, mixed $value): mixed
    {
        if (Nunjucks::truthy($condition)) {
            return $value;
        }

        return Nunjucks::undefined();
    }

    public static function renderRadios(Params $p): string
    {
        $idPrefix = $p->get('idPrefix');
        if (! Nunjucks::truthy($idPrefix)) {
            $idPrefix = $p->get('name');
        }
        $fieldset = $p->get('fieldset');
        $describedBy = '';
        $supplied = Nunjucks::get($fieldset, 'describedBy');
        if (Nunjucks::truthy($supplied)) {
            $describedBy = Nunjucks::str($supplied);
        }
        $inner = [];
        [$hint, $describedBy] = self::hintBlock($p, Nunjucks::str($idPrefix), $describedBy, 2);
        $inner[] = $hint;
        [$errorMessage, $describedBy] = self::errorBlock($p, Nunjucks::str($idPrefix), $describedBy, 2);
        $inner[] = $errorMessage;
        $formGroup = $p->get('formGroup');
        $inner[] = '  <div class="govuk-radios'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).' data-module="govuk-radios">
';
        $before = Nunjucks::get($formGroup, 'beforeInputs');
        if (Nunjucks::truthy($before)) {
            $inner[] = '    '.self::slotContent($before, 4, false).'
';
        }
        foreach (Nunjucks::items($p->get('items')) as $_i => $item) {
            $index = $_i + 0;
            if (! Nunjucks::truthy($item)) {
                continue;
            }
            $inner[] = self::radioItem($p, $item, ($index + 1), Nunjucks::str($idPrefix));
        }
        $after = Nunjucks::get($formGroup, 'afterInputs');
        if (Nunjucks::truthy($after)) {
            $inner[] = '    '.self::slotContent($after, 4, false).'
';
        }
        $inner[] = '  </div>
';

        return self::fieldsetWrapper($p, implode('', $inner), $describedBy, Nunjucks::undefined(), false);
    }

    private static function radioItem(Params $p, mixed $item, int $index, string $idPrefix): string
    {
        $itemId = $idPrefix;
        if ($index > 1) {
            $itemId .= '-'.(string) ($index);
        }
        $supplied = Nunjucks::get($item, 'id');
        if (Nunjucks::truthy($supplied)) {
            $itemId = Nunjucks::str($supplied);
        }
        $conditionalId = 'conditional-'.$itemId;
        $divider = Nunjucks::get($item, 'divider');
        if (Nunjucks::truthy($divider)) {
            return '    <div class="govuk-radios__divider">'.Nunjucks::out($divider).'</div>
';
        }
        $checked = Nunjucks::truthy(Nunjucks::get($item, 'checked'));
        if ((! $checked && Nunjucks::truthy($p->get('value')))) {
            $checked = (Nunjucks::looseEq(Nunjucks::get($item, 'value'), $p->get('value')) && ! Nunjucks::looseEq(Nunjucks::get($item, 'checked'), false));
        }
        $hint = Nunjucks::get($item, 'hint');
        $hasHint = (Nunjucks::truthy(Nunjucks::get($hint, 'text')) || Nunjucks::truthy(Nunjucks::get($hint, 'html')));
        $itemHintId = $itemId.'-item-hint';
        $conditional = Nunjucks::get($item, 'conditional');
        $label = Nunjucks::get($item, 'label');
        $parts = [];
        $parts[] = '    <div class="govuk-radios__item">
';
        $parts[] = '      <input class="govuk-radios__input" id="'.Nunjucks::escape($itemId).'" name="'.Nunjucks::out($p->get('name')).'" type="radio" value="'.Nunjucks::out(Nunjucks::get($item, 'value')).'"'.Nunjucks::flagIf(' checked', $checked).Nunjucks::flagIf(' disabled', Nunjucks::get($item, 'disabled')).Nunjucks::attributeIf('data-aria-controls', self::ifTruthy(Nunjucks::get($conditional, 'html'), $conditionalId)).Nunjucks::attributeIf('aria-describedby', self::ifTruthy($hasHint, $itemHintId)).Attributes::render(Nunjucks::get($item, 'attributes')).'>
';
        $parts[] = '      '.Nunjucks::indent(Nunjucks::trim(Text::renderLabel(Params::make('html', Nunjucks::get($item, 'html'), 'text', Nunjucks::get($item, 'text'), 'classes', 'govuk-radios__label'.Nunjucks::concatIf(' ', Nunjucks::get($label, 'classes')), 'attributes', Nunjucks::get($label, 'attributes'), 'for', $itemId))), 6, false).'
';
        if ($hasHint) {
            $parts[] = '      '.Nunjucks::indent(Nunjucks::trim(Text::renderHint(Params::make('id', $itemHintId, 'classes', 'govuk-radios__hint'.Nunjucks::concatIf(' ', Nunjucks::get($hint, 'classes')), 'attributes', Nunjucks::get($hint, 'attributes'), 'html', Nunjucks::get($hint, 'html'), 'text', Nunjucks::get($hint, 'text')))), 6, false).'
';
        }
        $parts[] = '    </div>
';
        $html = Nunjucks::get($conditional, 'html');
        if (Nunjucks::truthy($html)) {
            $parts[] = '    <div class="govuk-radios__conditional'.Nunjucks::flagIf(' govuk-radios__conditional--hidden', ! $checked).'" id="'.Nunjucks::escape($conditionalId).'">
      '.Nunjucks::trim(Nunjucks::str($html)).'
    </div>
';
        }

        return implode('', $parts);
    }

    public static function renderDateInput(Params $p): string
    {
        $fieldset = $p->get('fieldset');
        $describedBy = '';
        $supplied = Nunjucks::get($fieldset, 'describedBy');
        if (Nunjucks::truthy($supplied)) {
            $describedBy = Nunjucks::str($supplied);
        }
        $values = $p->get('values');
        $day = Nunjucks::def($p->get('day'), Params::make('name', 'day', 'value', Nunjucks::get($values, 'day'), 'classes', 'govuk-input--width-2'));
        $month = Nunjucks::def($p->get('month'), Params::make('name', 'month', 'value', Nunjucks::get($values, 'month'), 'classes', 'govuk-input--width-2'));
        $year = Nunjucks::def($p->get('year'), Params::make('name', 'year', 'value', Nunjucks::get($values, 'year'), 'classes', 'govuk-input--width-4'));
        $dateItems = Nunjucks::items($p->get('items'));
        if (count($dateItems) === 0) {
            $dateItems = [$day, $month, $year];
        }
        $anyItemHasError = false;
        foreach ($dateItems as $item) {
            if (self::dateItemHasError($item)) {
                $anyItemHasError = true;
            }
        }
        $inner = [];
        [$hint, $describedBy] = self::hintBlock($p, Nunjucks::str($p->get('id')), $describedBy, 2);
        $inner[] = $hint;
        [$errorMessage, $describedBy] = self::errorBlock($p, Nunjucks::str($p->get('id')), $describedBy, 2);
        $inner[] = $errorMessage;
        $formGroup = $p->get('formGroup');
        $inner[] = '  <div class="govuk-date-input'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).Nunjucks::attributeIf('id', $p->get('id')).'>
';
        $before = Nunjucks::get($formGroup, 'beforeInputs');
        if (Nunjucks::truthy($before)) {
            $inner[] = '    '.self::slotContent($before, 4, false).'
';
        }
        foreach ($dateItems as $item) {
            if (! Nunjucks::truthy($item)) {
                continue;
            }
            $inner[] = Nunjucks::indent(Nunjucks::trim(self::dateInputItem($p, $item, $anyItemHasError, $day, $month, $year)), 4, true).'
';
        }
        $after = Nunjucks::get($formGroup, 'afterInputs');
        if (Nunjucks::truthy($after)) {
            $inner[] = '    '.self::slotContent($after, 4, false).'
';
        }
        $inner[] = '  </div>
';

        return self::fieldsetWrapper($p, implode('', $inner), $describedBy, 'group', true);
    }

    private static function dateItemHasError(mixed $item): bool
    {
        if (Nunjucks::truthy(Nunjucks::get($item, 'error'))) {
            return true;
        }
        $classes = Nunjucks::get($item, 'classes');

        return Nunjucks::truthy($classes) && Nunjucks::contains('govuk-input--error', $classes);
    }

    private static function dateInputItem(Params $p, mixed $item, bool $anyItemHasError, mixed $day, mixed $month, mixed $year): string
    {
        $itemName = Nunjucks::get($item, 'name');
        $itemValue = Nunjucks::get($item, 'value');
        $itemWidth = '2';
        $itemClasses = '';
        $itemHasError = self::dateItemHasError($item);
        $name = Nunjucks::get($item, 'name');
        if (($item === $day || (Nunjucks::truthy($name) && Nunjucks::contains($name, ['day', Nunjucks::get($day, 'name')])))) {
            $itemName = Nunjucks::def($name, 'day');
            $itemValue = Nunjucks::def($itemValue, Nunjucks::get($day, 'value'));
        } elseif (($item === $month || (Nunjucks::truthy($name) && Nunjucks::contains($name, ['month', Nunjucks::get($month, 'name')])))) {
            $itemName = Nunjucks::def($name, 'month');
            $itemValue = Nunjucks::def($itemValue, Nunjucks::get($month, 'value'));
        } elseif (($item === $year || (Nunjucks::truthy($name) && Nunjucks::contains($name, ['year', Nunjucks::get($year, 'name')])))) {
            $itemName = Nunjucks::def($name, 'year');
            $itemValue = Nunjucks::def($itemValue, Nunjucks::get($year, 'value'));
            $itemWidth = '4';
        }
        $classes = Nunjucks::get($item, 'classes');
        $hasErrorClass = (Nunjucks::truthy($classes) && Nunjucks::contains('govuk-input--error', $classes));
        if ((! $hasErrorClass && ($itemHasError || (! Nunjucks::looseEq(Nunjucks::get($item, 'error'), false) && Nunjucks::truthy($p->get('errorMessage')) && ! $anyItemHasError)))) {
            $itemClasses = Nunjucks::trim($itemClasses.' govuk-input--error');
        }
        if ((! Nunjucks::truthy($classes) || ! Nunjucks::contains('govuk-input--width-', $classes))) {
            $itemClasses = Nunjucks::trim($itemClasses.' govuk-input--width-'.$itemWidth);
        }
        if (Nunjucks::truthy($classes)) {
            $itemClasses = Nunjucks::trim($itemClasses.' '.Nunjucks::str($classes));
        }
        $namePrefix = '';
        $prefix = $p->get('namePrefix');
        if (Nunjucks::truthy($prefix)) {
            $namePrefix = Nunjucks::str($prefix).'-';
        }
        $label = Nunjucks::get($item, 'label');
        if (! Nunjucks::truthy($label)) {
            $label = self::capitalise(Nunjucks::str($itemName));
        }
        $id_ = Nunjucks::get($item, 'id');
        if (! Nunjucks::truthy($id_)) {
            $id_ = Nunjucks::str($p->get('id')).'-'.Nunjucks::str($itemName);
        }
        $value = $itemValue;
        if (Nunjucks::isUndefined($value)) {
            $value = Nunjucks::get($p->get('values'), $namePrefix.Nunjucks::str($itemName));
        }
        $inputHtml = self::renderInput(Params::make('label', Params::make('text', $label, 'classes', 'govuk-date-input__label'), 'id', $id_, 'classes', 'govuk-date-input__input'.Nunjucks::concatIf(' ', $itemClasses), 'name', $namePrefix.Nunjucks::str($itemName), 'value', $value, 'type', 'text', 'inputmode', Nunjucks::defTruthy(Nunjucks::get($item, 'inputmode'), 'numeric'), 'autocomplete', Nunjucks::get($item, 'autocomplete'), 'pattern', Nunjucks::get($item, 'pattern'), 'attributes', Nunjucks::get($item, 'attributes')));

        return '<div class="govuk-date-input__item">
  '.Nunjucks::indent(Nunjucks::trim($inputHtml), 2, false).'
</div>';
    }

    private static function capitalise(string $text): string
    {
        if ($text === '') {
            return '';
        }
        $lowered = strtolower($text);

        return strtoupper($lowered[0]).substr($lowered, 1);
    }
}

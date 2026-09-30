<?php

declare(strict_types=1);

namespace App\Govuk;

/**
 * Attribute helpers matching GOV.UK Frontend's govukAttributes / govukI18nAttributes.
 */
final class Attributes
{
    public static function render(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if ($value instanceof SafeString) {
            return $value->value;
        }
        if ($value instanceof Params) {
            $out = '';
            foreach ($value->keys() as $name) {
                $out .= self::attribute($name, $value->get($name));
            }

            return $out;
        }

        return '';
    }

    public static function i18n(string $key, mixed $message, mixed $messages): string
    {
        if (Nunjucks::truthy($messages)) {
            if (! $messages instanceof Params) {
                return '';
            }
            $out = '';
            foreach ($messages->keys() as $rule) {
                $out .= ' data-i18n.'.$key.'.'.$rule.'="'.Nunjucks::escape(Nunjucks::str($messages->get($rule))).'"';
            }

            return $out;
        }
        if (Nunjucks::truthy($message)) {
            return ' data-i18n.'.$key.'="'.Nunjucks::escape(Nunjucks::str($message)).'"';
        }

        return '';
    }

    private static function attribute(string $name, mixed $item): string
    {
        $value = $item;
        $optional = false;
        if ($item instanceof Params) {
            $value = $item->get('value');
            $flag = $item->get('optional');
            $optional = is_bool($flag) && $flag;
        }

        $empty = $value === null || Nunjucks::isUndefined($value);
        $escaped = '';
        if (! $empty) {
            $escaped = $value instanceof SafeString
                ? $value->value
                : Nunjucks::escape(Nunjucks::str($value));
        }

        if ($optional) {
            if (is_bool($value) && $value) {
                return ' '.Nunjucks::escape($name);
            }
            if ($empty || is_bool($value)) {
                return '';
            }
        }

        return ' '.Nunjucks::escape($name).'="'.$escaped.'"';
    }
}

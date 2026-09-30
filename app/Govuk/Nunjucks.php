<?php

declare(strict_types=1);

namespace App\Govuk;

/**
 * Nunjucks-parity helpers used by component renderers.
 */
final class Nunjucks
{
    private const ESCAPE_MAP = [
        '&' => '&amp;',
        '"' => '&quot;',
        "'" => '&#39;',
        '<' => '&lt;',
        '>' => '&gt;',
        '\\' => '&#92;',
    ];

    public static function undefined(): UndefinedValue
    {
        return UndefinedValue::instance();
    }

    public static function isUndefined(mixed $value): bool
    {
        return $value instanceof UndefinedValue;
    }

    public static function get(mixed $value, string ...$names): mixed
    {
        foreach ($names as $name) {
            if (! $value instanceof Params) {
                return self::undefined();
            }
            $value = $value->get($name);
        }

        return $value;
    }

    /**
     * @return list<mixed>
     */
    public static function items(mixed $value): array
    {
        return is_array($value) && array_is_list($value) ? $value : [];
    }

    public static function at(mixed $value, int $index): mixed
    {
        $list = self::items($value);
        if ($index < 0 || $index >= count($list)) {
            return self::undefined();
        }

        return $list[$index];
    }

    public static function truthy(mixed $value): bool
    {
        if ($value === null || $value instanceof UndefinedValue) {
            return false;
        }
        if (is_bool($value)) {
            return $value;
        }
        if (is_string($value)) {
            return $value !== '';
        }
        // Numbers stored as spelling strings from JSON — 0 is falsy in JS
        if (is_int($value) || is_float($value)) {
            return $value != 0;
        }
        // json number spelling
        if ($value instanceof JsonNumber) {
            return (float) $value->value != 0.0;
        }

        // Empty arrays and objects are truthy in JS/Nunjucks
        return true;
    }

    public static function str(mixed $value): string
    {
        if ($value === null || $value instanceof UndefinedValue) {
            return '';
        }
        if (is_string($value)) {
            return $value;
        }
        if ($value instanceof SafeString) {
            return $value->value;
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if ($value instanceof JsonNumber) {
            return self::formatNumber($value->value);
        }
        if (is_int($value) || is_float($value)) {
            return self::formatNumber((string) $value);
        }
        if (is_array($value) && array_is_list($value)) {
            return implode(',', array_map(self::str(...), $value));
        }

        return '[object Object]';
    }

    public static function formatNumber(string $value): string
    {
        if (preg_match('/^-?\d+$/', $value) === 1) {
            return $value;
        }
        $float = (float) $value;

        return rtrim(rtrim(sprintf('%.15F', $float), '0'), '.') ?: '0';
    }

    public static function out(mixed $value): string
    {
        if ($value instanceof SafeString) {
            return $value->value;
        }

        return self::escape(self::str($value));
    }

    public static function escape(string $text): string
    {
        return strtr($text, self::ESCAPE_MAP);
    }

    public static function trim(string $text): string
    {
        return trim($text);
    }

    public static function indent(string $text, int $width, bool $first): string
    {
        if ($text === '') {
            return '';
        }
        $padding = str_repeat(' ', $width);
        $lines = explode("\n", $text);
        foreach ($lines as $i => $line) {
            if ($i === 0 && ! $first) {
                continue;
            }
            $lines[$i] = $padding.$line;
        }

        return implode("\n", $lines);
    }

    public static function def(mixed $value, mixed $fallback): mixed
    {
        return self::isUndefined($value) ? $fallback : $value;
    }

    public static function defTruthy(mixed $value, mixed $fallback): mixed
    {
        return self::truthy($value) ? $value : $fallback;
    }

    public static function length(mixed $value): int
    {
        if ($value === null || $value instanceof UndefinedValue) {
            return 0;
        }
        if (is_bool($value)) {
            return 0;
        }
        if (is_array($value) && array_is_list($value)) {
            return count($value);
        }
        if ($value instanceof Params) {
            return $value->len();
        }
        if (is_string($value)) {
            return strlen($value);
        }
        if ($value instanceof SafeString) {
            return strlen($value->value);
        }

        return 0;
    }

    public static function looseEq(mixed $left, mixed $right): bool
    {
        $leftNil = $left === null || self::isUndefined($left);
        $rightNil = $right === null || self::isUndefined($right);
        if ($leftNil || $rightNil) {
            return $leftNil && $rightNil;
        }
        $leftIsBool = is_bool($left);
        $rightIsBool = is_bool($right);
        if ($leftIsBool && $rightIsBool) {
            return $left === $right;
        }
        if ($leftIsBool) {
            return self::looseEq($left ? new JsonNumber('1') : new JsonNumber('0'), $right);
        }
        if ($rightIsBool) {
            return self::looseEq($left, $right ? new JsonNumber('1') : new JsonNumber('0'));
        }
        $leftNum = self::asNumberSpelling($left);
        $rightNum = self::asNumberSpelling($right);
        if ($leftNum !== null && $rightNum !== null) {
            return self::numeric($leftNum) === self::numeric($rightNum);
        }
        if ($leftNum !== null) {
            return self::sameNumber($leftNum, self::str($right));
        }
        if ($rightNum !== null) {
            return self::sameNumber(self::str($left), $rightNum);
        }

        return self::str($left) === self::str($right);
    }

    public static function strictEq(mixed $left, mixed $right): bool
    {
        if (self::isUndefined($left) || self::isUndefined($right)) {
            return self::isUndefined($left) && self::isUndefined($right);
        }
        if ($left === null || $right === null) {
            return $left === null && $right === null;
        }
        $leftNum = self::asNumberSpelling($left);
        $rightNum = self::asNumberSpelling($right);
        if (($leftNum !== null) !== ($rightNum !== null)) {
            return false;
        }
        if ($leftNum !== null && $rightNum !== null) {
            return self::numeric($leftNum) === self::numeric($rightNum);
        }
        if (is_bool($left)) {
            return is_bool($right) && $left === $right;
        }
        if (is_string($left)) {
            return is_string($right) && $left === $right;
        }

        return $left === $right;
    }

    public static function contains(mixed $needle, mixed $haystack): bool
    {
        if (is_string($haystack)) {
            return str_contains($haystack, self::str($needle));
        }
        if ($haystack instanceof SafeString) {
            return str_contains($haystack->value, self::str($needle));
        }
        if (is_array($haystack) && array_is_list($haystack)) {
            foreach ($haystack as $item) {
                if (self::strictEq($needle, $item)) {
                    return true;
                }
            }

            return false;
        }
        if ($haystack instanceof Params) {
            return $haystack->has(self::str($needle));
        }

        return false;
    }

    public static function attributeIf(string $name, mixed $value): string
    {
        if (! self::truthy($value)) {
            return '';
        }

        return ' '.$name.'="'.self::out($value).'"';
    }

    public static function classesIf(mixed $value): string
    {
        if (! self::truthy($value)) {
            return '';
        }

        return ' '.self::out($value);
    }

    public static function flagIf(string $suffix, mixed $value): string
    {
        return self::truthy($value) ? $suffix : '';
    }

    public static function content(mixed $params, string $htmlKey, string $textKey): string
    {
        $html = self::get($params, $htmlKey);
        if (self::truthy($html)) {
            return self::str($html);
        }

        return self::out(self::get($params, $textKey));
    }

    public static function contentIndent(mixed $params, string $htmlKey, string $textKey, int $width): string
    {
        $html = self::get($params, $htmlKey);
        if (self::truthy($html)) {
            return self::indent(self::trim(self::str($html)), $width, false);
        }

        return self::out(self::get($params, $textKey));
    }

    public static function concatIf(string $prefix, mixed $value): string
    {
        return self::truthy($value) ? $prefix.self::str($value) : '';
    }

    public static function heading(mixed $level, string $fallback): string
    {
        if (! self::truthy($level)) {
            return $fallback;
        }
        $text = self::str($level);
        if (preg_match('/^[1-6]$/', $text) === 1) {
            return $text;
        }

        return $fallback;
    }

    private static function asNumberSpelling(mixed $value): ?string
    {
        if ($value instanceof JsonNumber) {
            return $value->value;
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return null;
    }

    private static function numeric(string $text): float
    {
        return (float) $text;
    }

    private static function sameNumber(string $left, string $right): bool
    {
        $trimmed = trim($right);
        if ($trimmed === '') {
            $trimmed = '0';
        }
        if (! is_numeric($trimmed)) {
            return false;
        }

        return self::numeric($left) === (float) $trimmed;
    }
}

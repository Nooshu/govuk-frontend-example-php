<?php

declare(strict_types=1);

namespace App\Govuk;

/**
 * Byte-for-byte HTML equality against official GOV.UK Frontend fixtures.
 */
final class Parity
{
    public static function matches(string $rendered, string $fixtureHtml): bool
    {
        return $rendered === $fixtureHtml;
    }

    /**
     * First differing line, for readable Pest failures.
     */
    public static function difference(string $want, string $got): string
    {
        $wantLines = explode("\n", $want);
        $gotLines = explode("\n", $got);
        $max = max(count($wantLines), count($gotLines));
        for ($i = 0; $i < $max; $i++) {
            $wantLine = $i < count($wantLines) ? $wantLines[$i] : '<missing line>';
            $gotLine = $i < count($gotLines) ? $gotLines[$i] : '<missing line>';
            if ($wantLine !== $gotLine) {
                return sprintf(
                    "first difference on line %d\nwant: %s\ngot:  %s",
                    $i + 1,
                    var_export($wantLine, true),
                    var_export($gotLine, true),
                );
            }
        }

        // @codeCoverageIgnoreStart
        return sprintf(
            "line-by-line equal but strings differ\nwant: %s\ngot:  %s",
            var_export($want, true),
            var_export($got, true),
        );
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return array{type: string, titleText: string, text: string}
     */
    public static function banner(bool $matches): array
    {
        if ($matches) {
            return [
                'type' => 'success',
                'titleText' => 'HTML matches the fixture',
                'text' => 'The PHP renderer output is the same as the official fixture HTML.',
            ];
        }

        return [
            'titleText' => 'HTML does not match the fixture',
            'text' => 'The PHP renderer output differs from the official fixture HTML.',
            'type' => '',
        ];
    }
}

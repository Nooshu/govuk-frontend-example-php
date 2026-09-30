<?php

declare(strict_types=1);

namespace App\Govuk;

/**
 * Minimal JSON tokenizer that keeps number tokens as their original spelling.
 *
 * @phpstan-type Token array{type: string, value?: string}
 */
final class JsonTokenizer
{
    private int $i = 0;

    private int $len;

    public function __construct(private readonly string $json)
    {
        $this->len = strlen($json);
    }

    /**
     * @return Token
     */
    public function next(): array
    {
        $this->skipWs();
        if ($this->i >= $this->len) {
            throw new \InvalidArgumentException('unexpected end of JSON');
        }
        $c = $this->json[$this->i];
        if ($c === '{' || $c === '}' || $c === '[' || $c === ']' || $c === ':' || $c === ',') {
            $this->i++;

            return ['type' => $c];
        }
        if ($c === '"') {
            return ['type' => 'string', 'value' => $this->readString()];
        }
        if ($c === '-' || ($c >= '0' && $c <= '9')) {
            return ['type' => 'number', 'value' => $this->readNumber()];
        }
        if (substr($this->json, $this->i, 4) === 'true') {
            $this->i += 4;

            return ['type' => 'true'];
        }
        if (substr($this->json, $this->i, 5) === 'false') {
            $this->i += 5;

            return ['type' => 'false'];
        }
        if (substr($this->json, $this->i, 4) === 'null') {
            $this->i += 4;

            return ['type' => 'null'];
        }
        throw new \InvalidArgumentException('invalid JSON near '.$c);
    }

    public function peekType(): string
    {
        $saved = $this->i;
        try {
            return $this->next()['type'];
        } finally {
            $this->i = $saved;
        }
    }

    private function skipWs(): void
    {
        while ($this->i < $this->len) {
            $c = $this->json[$this->i];
            if ($c !== ' ' && $c !== "\t" && $c !== "\n" && $c !== "\r") {
                break;
            }
            $this->i++;
        }
    }

    private function readString(): string
    {
        $this->i++; // opening quote
        $out = '';
        while ($this->i < $this->len) {
            $c = $this->json[$this->i++];
            if ($c === '"') {
                return $out;
            }
            if ($c === '\\') {
                if ($this->i >= $this->len) {
                    throw new \InvalidArgumentException('bad escape');
                }
                $esc = $this->json[$this->i++];
                $out .= match ($esc) {
                    '"', '\\', '/' => $esc,
                    'b' => "\x08",
                    'f' => "\x0c",
                    'n' => "\n",
                    'r' => "\r",
                    't' => "\t",
                    'u' => $this->readUnicode(),
                    default => throw new \InvalidArgumentException('bad escape'),
                };

                continue;
            }
            $out .= $c;
        }
        throw new \InvalidArgumentException('unterminated string');
    }

    private function readUnicode(): string
    {
        $hex = substr($this->json, $this->i, 4);
        if (strlen($hex) !== 4 || ! ctype_xdigit($hex)) {
            throw new \InvalidArgumentException('bad unicode escape');
        }
        $this->i += 4;
        $code = hexdec($hex);

        return mb_chr($code, 'UTF-8');
    }

    private function readNumber(): string
    {
        $start = $this->i;
        if ($this->json[$this->i] === '-') {
            $this->i++;
        }
        while ($this->i < $this->len && $this->json[$this->i] >= '0' && $this->json[$this->i] <= '9') {
            $this->i++;
        }
        if ($this->i < $this->len && $this->json[$this->i] === '.') {
            $this->i++;
            while ($this->i < $this->len && $this->json[$this->i] >= '0' && $this->json[$this->i] <= '9') {
                $this->i++;
            }
        }
        if ($this->i < $this->len && ($this->json[$this->i] === 'e' || $this->json[$this->i] === 'E')) {
            $this->i++;
            if ($this->i < $this->len && ($this->json[$this->i] === '+' || $this->json[$this->i] === '-')) {
                $this->i++;
            }
            while ($this->i < $this->len && $this->json[$this->i] >= '0' && $this->json[$this->i] <= '9') {
                $this->i++;
            }
        }

        return substr($this->json, $start, $this->i - $start);
    }
}

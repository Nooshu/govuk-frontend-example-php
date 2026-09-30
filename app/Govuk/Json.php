<?php

declare(strict_types=1);

namespace App\Govuk;

/**
 * Decode JSON into Params / arrays while preserving object key order and number spelling.
 */
final class Json
{
    public static function decode(string $json): mixed
    {
        return self::parseValue(new JsonTokenizer($json));
    }

    private static function parseValue(JsonTokenizer $t): mixed
    {
        $tok = $t->next();

        return match ($tok['type']) {
            '{' => self::parseObject($t),
            '[' => self::parseArray($t),
            'string' => $tok['value'],
            'number' => new JsonNumber($tok['value']),
            'true' => true,
            'false' => false,
            'null' => null,
            default => throw new \InvalidArgumentException('unexpected JSON token'),
        };
    }

    private static function parseObject(JsonTokenizer $t): Params
    {
        $object = new Params;
        if ($t->peekType() === '}') {
            $t->next();

            return $object;
        }
        while (true) {
            $keyTok = $t->next();
            if ($keyTok['type'] !== 'string') {
                throw new \InvalidArgumentException('expected object key');
            }
            $colon = $t->next();
            if ($colon['type'] !== ':') {
                throw new \InvalidArgumentException('expected :');
            }
            $object->set($keyTok['value'], self::parseValue($t));
            $sep = $t->next();
            if ($sep['type'] === '}') {
                break;
            }
            if ($sep['type'] !== ',') {
                throw new \InvalidArgumentException('expected , or }');
            }
        }

        return $object;
    }

    /**
     * @return list<mixed>
     */
    private static function parseArray(JsonTokenizer $t): array
    {
        $items = [];
        if ($t->peekType() === ']') {
            $t->next();

            return $items;
        }
        while (true) {
            $items[] = self::parseValue($t);
            $sep = $t->next();
            if ($sep['type'] === ']') {
                break;
            }
            if ($sep['type'] !== ',') {
                throw new \InvalidArgumentException('expected , or ]');
            }
        }

        return $items;
    }
}

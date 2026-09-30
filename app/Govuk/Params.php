<?php

declare(strict_types=1);

namespace App\Govuk;

/**
 * Ordered component options — insertion order must match fixture attributes.
 *
 * @phpstan-type ParamValue mixed
 */
final class Params implements \IteratorAggregate
{
    /** @var list<string> */
    private array $keys = [];

    /** @var array<string, mixed> */
    private array $values = [];

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->keys);
    }

    public static function make(mixed ...$pairs): self
    {
        if (count($pairs) % 2 !== 0) {
            throw new \InvalidArgumentException('Params::make needs an even number of arguments');
        }
        $p = new self;
        for ($i = 0; $i < count($pairs); $i += 2) {
            $key = $pairs[$i];
            if (! is_string($key)) {
                throw new \InvalidArgumentException('Params::make key must be a string');
            }
            $p->set($key, $pairs[$i + 1]);
        }

        return $p;
    }

    /**
     * Decode fixture options JSON while preserving key order and number spelling.
     */
    public static function fromJson(string $json): self
    {
        $decoded = Json::decode($json);
        if (! $decoded instanceof self) {
            throw new \InvalidArgumentException('expected a JSON object');
        }

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $p = new self;
        foreach ($data as $key => $value) {
            $p->set((string) $key, self::hydrate($value));
        }

        return $p;
    }

    public function set(string $key, mixed $value): void
    {
        if (! array_key_exists($key, $this->values)) {
            $this->keys[] = $key;
        }
        $this->values[$key] = $value;
    }

    public function get(string $key): mixed
    {
        if (! array_key_exists($key, $this->values)) {
            return UndefinedValue::instance();
        }

        return $this->values[$key];
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return $this->keys;
    }

    public function len(): int
    {
        return count($this->keys);
    }

    private static function hydrate(mixed $value): mixed
    {
        if (is_array($value)) {
            if (array_is_list($value)) {
                return array_map(self::hydrate(...), $value);
            }

            return self::fromArray($value);
        }

        return $value;
    }
}

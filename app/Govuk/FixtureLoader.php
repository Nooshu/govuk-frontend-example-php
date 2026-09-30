<?php

declare(strict_types=1);

namespace App\Govuk;

/**
 * Official GOV.UK Frontend fixtures.json loader (ordered Params, number spelling preserved).
 */
final class FixtureLoader
{
    public function __construct(private readonly string $componentsDir) {}

    public static function fromConfig(): self
    {
        return new self((string) config('govuk.fixtures_path'));
    }

    /**
     * @return list<string>
     */
    public function componentNames(): array
    {
        return self::fixtureComponents($this->componentsDir);
    }

    /**
     * @return array{component: string, fixtures: list<array{name: string, options: Params, hidden: bool, description: string, html: string}>}
     */
    public function load(string $component): array
    {
        return self::loadFixtures($this->componentsDir, $component);
    }

    /**
     * @return list<string>
     */
    public static function fixtureComponents(string $componentsDir): array
    {
        if (! is_dir($componentsDir)) {
            throw new \RuntimeException('govuk: reading '.$componentsDir.': not a directory');
        }
        $names = [];
        $entries = scandir($componentsDir);
        if ($entries === false) { // @codeCoverageIgnore
            throw new \RuntimeException('govuk: reading '.$componentsDir); // @codeCoverageIgnore
        }
        sort($entries);
        foreach ($entries as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }
            $path = $componentsDir.DIRECTORY_SEPARATOR.$name;
            if (! is_dir($path)) {
                continue;
            }
            if (is_file($path.DIRECTORY_SEPARATOR.'fixtures.json')) {
                $names[] = $name;
            }
        }

        return $names;
    }

    /**
     * @return array{component: string, fixtures: list<array{name: string, options: Params, hidden: bool, description: string, html: string}>}
     */
    public static function loadFixtures(string $componentsDir, string $component): array
    {
        $path = $componentsDir.DIRECTORY_SEPARATOR.$component.DIRECTORY_SEPARATOR.'fixtures.json';
        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException('govuk: reading '.$path);
        }

        $data = Json::decode($raw);
        if (! $data instanceof Params) {
            throw new \InvalidArgumentException('govuk: parsing '.$path.': expected a JSON object');
        }

        $componentName = $data->get('component');
        if (Nunjucks::isUndefined($componentName) || $componentName === null) {
            $componentName = $component;
        } else {
            $componentName = Nunjucks::str($componentName);
        }

        $fixtures = [];
        foreach (Nunjucks::items($data->get('fixtures')) as $item) {
            if (! $item instanceof Params) {
                continue;
            }
            $options = $item->get('options');
            if (! $options instanceof Params) {
                $options = new Params;
            }
            $hidden = $item->get('hidden');
            if (! is_bool($hidden)) {
                $hidden = false;
            }
            $name = $item->get('name');
            $description = $item->get('description');
            $html = $item->get('html');
            $fixtures[] = [
                'name' => (Nunjucks::isUndefined($name) || $name === null) ? '' : Nunjucks::str($name),
                'options' => $options,
                'hidden' => $hidden,
                'description' => (Nunjucks::isUndefined($description) || $description === null)
                    ? ''
                    : Nunjucks::str($description),
                'html' => (Nunjucks::isUndefined($html) || $html === null) ? '' : Nunjucks::str($html),
            ];
        }

        return [
            'component' => $componentName,
            'fixtures' => $fixtures,
        ];
    }
}

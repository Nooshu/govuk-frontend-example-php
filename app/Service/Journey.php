<?php

declare(strict_types=1);

namespace App\Service;

final class Journey
{
    /** @var list<array{value: string, text: string}> */
    public const LICENCE_LENGTHS = [
        ['value' => '1-day', 'text' => '1 day'],
        ['value' => '8-days', 'text' => '8 days'],
        ['value' => '12-months', 'text' => '12 months'],
    ];

    /** @var list<string> */
    public const COUNTRIES = ['England', 'Wales', 'Scotland'];

    /** @var list<array{id: string, path: string, heading: string}> */
    public const STEPS = [
        [
            'id' => 'licence-length',
            'path' => '/licence-length',
            'heading' => 'How long do you need the licence for?',
        ],
        [
            'id' => 'name',
            'path' => '/name',
            'heading' => 'What is your full name?',
        ],
        [
            'id' => 'date-of-birth',
            'path' => '/date-of-birth',
            'heading' => 'What is your date of birth?',
        ],
        [
            'id' => 'where-you-will-fish',
            'path' => '/where-you-will-fish',
            'heading' => 'Where will you fish?',
        ],
        [
            'id' => 'email',
            'path' => '/email',
            'heading' => 'What is your email address?',
        ],
    ];

    /**
     * @return array{id: string, path: string, heading: string}|null
     */
    public static function step(string $id): ?array
    {
        foreach (self::STEPS as $step) {
            if ($step['id'] === $id) {
                return $step;
            }
        }

        return null;
    }

    /**
     * @return array{id: string, path: string, heading: string}|null
     */
    public static function nextStep(string $id): ?array
    {
        foreach (self::STEPS as $index => $step) {
            if ($step['id'] === $id) {
                return self::STEPS[$index + 1] ?? null;
            }
        }

        return null;
    }

    /**
     * @return array{id: string, path: string, heading: string}|null
     */
    public static function previousStep(string $id): ?array
    {
        foreach (self::STEPS as $index => $step) {
            if ($step['id'] === $id) {
                return $index > 0 ? self::STEPS[$index - 1] : null;
            }
        }

        return null;
    }

    public static function requiredComplete(Application $app): bool
    {
        foreach (self::STEPS as $step) {
            if (! $app->isCompleted($step['id'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{id: string, path: string, heading: string}|null
     */
    public static function firstIncompleteStep(Application $app): ?array
    {
        foreach (self::STEPS as $step) {
            if (! $app->isCompleted($step['id'])) {
                return $step;
            }
        }

        return null;
    }

    public static function lengthLabel(string $value): string
    {
        foreach (self::LICENCE_LENGTHS as $length) {
            if ($length['value'] === $value) {
                return $length['text'];
            }
        }

        return $value;
    }

    public static function createReference(): string
    {
        return 'FR'.str_pad((string) random_int(0, 99_999_999), 8, '0', STR_PAD_LEFT);
    }
}

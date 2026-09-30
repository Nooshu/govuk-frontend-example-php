<?php

declare(strict_types=1);

namespace App\Service;

final class Journey
{
    /** @var list<array{id: string, path: string, heading: string, optional: bool}> */
    public const STEPS = [
        ['id' => 'name', 'path' => '/name', 'heading' => 'What is your name?', 'optional' => false],
        ['id' => 'date-of-birth', 'path' => '/date-of-birth', 'heading' => 'What is your date of birth?', 'optional' => false],
        ['id' => 'email', 'path' => '/email', 'heading' => 'What is your email address?', 'optional' => false],
        ['id' => 'contact-preference', 'path' => '/contact-preference', 'heading' => 'How should we contact you?', 'optional' => false],
        ['id' => 'where-you-will-fish', 'path' => '/where-you-will-fish', 'heading' => 'Where will you fish?', 'optional' => false],
        ['id' => 'licence-length', 'path' => '/licence-length', 'heading' => 'How long do you need a licence for?', 'optional' => false],
        ['id' => 'start-month', 'path' => '/start-month', 'heading' => 'When should the licence start?', 'optional' => false],
        ['id' => 'address', 'path' => '/address', 'heading' => 'What is your address?', 'optional' => false],
        ['id' => 'evidence', 'path' => '/evidence', 'heading' => 'Upload evidence of a concession', 'optional' => true],
        ['id' => 'additional-details', 'path' => '/additional-details', 'heading' => 'Is there anything else we should know?', 'optional' => true],
        ['id' => 'create-a-password', 'path' => '/create-a-password', 'heading' => 'Create a password', 'optional' => false],
    ];

    /**
     * @return array{id: string, path: string, heading: string, optional: bool}|null
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

    public static function isOptional(string $id): bool
    {
        return self::step($id)['optional'] ?? false;
    }

    public static function requiredComplete(Application $app): bool
    {
        foreach (self::STEPS as $step) {
            if ($step['optional']) {
                continue;
            }
            if (! $app->isCompleted($step['id'])) {
                return false;
            }
        }

        return true;
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

final class Application
{
    public string $licenceLength = '';

    public string $fullName = '';

    public string $day = '';

    public string $month = '';

    public string $year = '';

    public string $country = '';

    public string $email = '';

    public bool $submitted = false;

    public string $reference = '';

    /** @var list<string> */
    public array $completed = [];

    public function isCompleted(string $step): bool
    {
        return in_array($step, $this->completed, true);
    }

    public function markCompleted(string $step): void
    {
        if (! $this->isCompleted($step)) {
            $this->completed[] = $step;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

final class Application
{
    public string $firstName = '';

    public string $lastName = '';

    public string $day = '';

    public string $month = '';

    public string $year = '';

    public string $email = '';

    public string $contactBy = '';

    public string $telephone = '';

    /** @var list<string> */
    public array $regions = [];

    public string $licenceLength = '';

    public string $startMonth = '';

    public string $addressLine1 = '';

    public string $addressLine2 = '';

    public string $town = '';

    public string $postcode = '';

    public string $evidenceFilename = '';

    public string $additionalDetails = '';

    public bool $passwordCreated = false;

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

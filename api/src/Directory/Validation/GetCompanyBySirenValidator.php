<?php

declare(strict_types=1);

namespace App\Directory\Validation;

use App\Common\Exception\InvalidInputException;

final readonly class GetCompanyBySirenValidator
{
    private const ALLOWED_FIELDS = [
        'siren',
        'businessName',
        'entityType',
        'administrativeStatus',
        'idInstance',
    ];

    public function validate(string $siren, ?array $fields = null): void
    {
        $this->validateSiren($siren);
        $this->validateFields($fields);
    }

    private function validateSiren(string $siren): void
    {
        if (!$siren) {
            throw new InvalidInputException('siren', 'siren cannot be empty');
        }

        if (!preg_match('/^\d{9}$/', $siren)) {
            throw new InvalidInputException('siren', 'siren must be exactly 9 digits (0-9)');
        }
    }

    private function validateFields(?array $fields): void
    {
        if (!$fields) {
            return;
        }

        foreach ($fields as $field) {
            if (!is_string($field)) {
                throw new InvalidInputException('fields', 'fields must be an array of strings');
            }

            if (!in_array($field, self::ALLOWED_FIELDS, true)) {
                throw new InvalidInputException('fields', sprintf('Invalid field "%s". Allowed: %s', $field, implode(', ', self::ALLOWED_FIELDS)));
            }
        }
    }
}

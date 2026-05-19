<?php

declare(strict_types=1);

namespace App\Directory\Validation;

use App\Common\Exception\InvalidInputException;

final readonly class GetCompanyByIdValidator
{
    private const ALLOWED_FIELDS = [
        'siren',
        'businessName',
        'entityType',
        'administrativeStatus',
        'idInstance',
    ];

    public function validate(int $idInstance, ?array $fields = null): void
    {
        $this->validateIdInstance($idInstance);
        $this->validateFields($fields);
    }

    private function validateIdInstance(int $idInstance): void
    {
        if (!$idInstance) {
            throw new InvalidInputException('idInstance', 'idInstance cannot be 0');
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

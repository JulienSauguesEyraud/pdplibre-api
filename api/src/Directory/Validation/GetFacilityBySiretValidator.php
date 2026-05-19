<?php

declare(strict_types=1);

namespace App\Directory\Validation;

use App\Common\Exception\InvalidInputException;

final readonly class GetFacilityBySiretValidator
{
    private const ALLOWED_FIELDS = [
        'siret',
        'siren',
        'name',
        'facilityType',
        'address',
        'diffusible',
        'administrativeStatus',
        'pmStatus',
        'pmOnly',
        'managesPaymentStatus',
        'managesLegalCommitment',
        'managesLegalCommitmentOrService',
        'serviceCodeStatus',
        'idInstance',
    ];

    public function validate(string $siret, ?array $fields = null): void
    {
        $this->validateSiret($siret);
        $this->validateFields($fields);
    }

    private function validateSiret(string $siret): void
    {
        if (!$siret) {
            throw new InvalidInputException('siret', 'siret cannot be empty');
        }

        if (!preg_match('/^\d{14}$/', $siret)) {
            throw new InvalidInputException('siret', 'siret must be exactly 14 digits (0-9)');
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

<?php

declare(strict_types=1);

namespace App\Directory\Validation;

use App\Common\Exception\InvalidInputException;
use App\Directory\Input\SearchSiretFilters;
use App\Directory\Input\SearchSiretSorting;

final readonly class SearchFacilityBySiretValidator
{
    private const ALLOWED_FIELDS = [
        "siret",
        "siren",
        "name",
        "facilityType",
        "address",
        "diffusible",
        "administrativeStatus",
        "pmStatus",
        "pmOnly",
        "managesPaymentStatus",
        "managesLegalCommitment",
        "managesLegalCommitmentOrService",
        "serviceCodeStatus",
        "idInstance"
    ];

    /**
     * @param array<SearchSiretSorting> $sorting
     */
    public function validate(?array $fields = null, ?SearchSiretFilters $filters = null, ?array $sorting = null): void
    {
        $this->validateFields($fields);
        $this->validateFilters($filters);
        $this->validateSorting($sorting);
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
                throw new InvalidInputException(
                    'fields',
                    sprintf('Invalid field "%s". Allowed: %s', $field, implode(', ', self::ALLOWED_FIELDS))
                );
            }
        }
    }

    private function validateFilters(?SearchSiretFilters $filters): void
    {
        if ($filters->siret !== null && !preg_match('/^\d{14}$/', $filters->siret->siret)) {
            throw new InvalidInputException(
                'siret',
                'siret must be exactly 14 digits (0-9)'
            );
        }
        if ($filters->siren !== null && !preg_match('/^\d{9}$/', $filters->siren->siren)) {
            throw new InvalidInputException(
                'siren',
                'siren must be exactly 9 digits (0-9)'
            );
        }
        if ($filters->postalCode !== null && !preg_match('/^\d{5}$/', $filters->postalCode->postalCode)) {
            throw new InvalidInputException(
                'postalCode',
                'postalCode must be exactly 5 digits (0-9)'
            );
        }
        if($filters->name !== null && $filters->name->name === '') {
            throw new InvalidInputException(
                'name',
                'name cannot be empty'
            );
        }
        if($filters->addressLines !== null && $filters->addressLines->addressLines === '') {
            throw new InvalidInputException(
                'addressLines',
                'addressLines cannot be empty'
            );
        }
        if($filters->countrySubdivision !== null && $filters->countrySubdivision->countrySubdivision === '') {
            throw new InvalidInputException(
                'countrySubdivision',
                'countrySubdivision cannot be empty'
            );
        }
        if($filters->locality !== null && $filters->locality->locality === '') {
            throw new InvalidInputException(
                'locality',
                'locality cannot be empty'
            );
        }
    }

    /**
     * @param array<SearchSiretSorting> $sorting
     */
    private function validateSorting(?array $sorting): void
    {
        if (!$sorting) {
            return;
        }

        foreach ($sorting as $sort) {
            if (!is_string($sort->field)) {
                throw new InvalidInputException('sorting', 'sorting fields must be strings');
            }

            if (!in_array($sort->field, self::ALLOWED_FIELDS, true)) {
                throw new InvalidInputException(
                    'fields',
                    sprintf('Invalid field "%s". Allowed: %s', $sort->field, implode(', ', self::ALLOWED_FIELDS))
                );
            }

            if(!$sort->order) {
                throw new InvalidInputException('sorting', 'Sorting order must exist');
            }
        }
    }
}

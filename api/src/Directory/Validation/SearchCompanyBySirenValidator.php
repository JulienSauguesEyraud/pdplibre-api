<?php

declare(strict_types=1);

namespace App\Directory\Validation;

use App\Common\Exception\InvalidInputException;
use App\Directory\Input\SearchSirenFilters;
use App\Directory\Input\SearchSirenSortingInner;

final readonly class SearchCompanyBySirenValidator
{
    private const ALLOWED_FIELDS = [
        'siren',
        'businessName',
        'entityType',
        'administrativeStatus',
        'idInstance',
    ];

    /**
     * @param array<SearchSirenSortingInner> $sorting
     */
    public function validate(SearchSirenFilters $filters, ?array $fields = null, ?array $sorting = null): void
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
                throw new InvalidInputException('fields', sprintf('Invalid field "%s". Allowed: %s', $field, implode(', ', self::ALLOWED_FIELDS)));
            }
        }
    }

    private function validateFilters(SearchSirenFilters $filters): void
    {
        if (null !== $filters->siren && !preg_match('/^\d{9}$/', $filters->siren->siren)) {
            throw new InvalidInputException('siren', 'siren must be exactly 9 digits (0-9)');
        }

        if (null !== $filters->businessName && '' === $filters->businessName->businessName) {
            throw new InvalidInputException('businessName', 'businessName cannot be empty');
        }
    }

    /**
     * @param array<SearchSirenSortingInner> $sorting
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
                throw new InvalidInputException('fields', sprintf('Invalid field "%s". Allowed: %s', $sort->field, implode(', ', self::ALLOWED_FIELDS)));
            }

            if (!$sort->order) {
                throw new InvalidInputException('sorting', 'Sorting order must exist');
            }
        }
    }
}

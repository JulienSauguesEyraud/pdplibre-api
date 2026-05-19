<?php

declare(strict_types=1);

namespace App\Tests\Unit\Directory;

use App\Common\Exception\InvalidInputException;
use App\Directory\Enum\Order;
use App\Directory\Input\SearchSirenFilters;
use App\Directory\Input\SearchSirenFiltersBusinessName;
use App\Directory\Input\SearchSirenFiltersSiren;
use App\Directory\Input\SearchSirenSortingInner;
use App\Directory\Validation\SearchCompanyBySirenValidator;
use PHPUnit\Framework\TestCase;

final class ValidationSearchCompanyBySirenTest extends TestCase
{
    public function testValidateWithValidFieldsFiltersAndSorting(): void
    {
        $validator = new SearchCompanyBySirenValidator();

        $filters = new SearchSirenFilters();

        $sirenFilter = new SearchSirenFiltersSiren();
        $sirenFilter->siren = '123456789';

        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = 'business name test';

        $filters->siren = $sirenFilter;
        $filters->businessName = $businessNameFilter;

        $sorting = new SearchSirenSortingInner();
        $sorting->field = 'siren';
        $sorting->order = Order::ascending;

        $sorting2 = new SearchSirenSortingInner();
        $sorting2->field = 'businessName';
        $sorting2->order = Order::descending;

        $validator->validate(
            $filters,
            [
                'siren',
                'businessName',
                'entityType',
                'administrativeStatus',
                'idInstance',
            ],
            [$sorting, $sorting2],
        );

        self::assertTrue(true);
    }

    public function testThrowsIfFieldIsNotString(): void
    {
        $validator = new SearchCompanyBySirenValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'fields must be an array of strings'
        );

        $filters = new SearchSirenFilters();

        $sirenFilter = new SearchSirenFiltersSiren();
        $sirenFilter->siren = '123456789';

        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = 'business name test';

        $filters->siren = $sirenFilter;
        $filters->businessName = $businessNameFilter;

        $validator->validate(
            $filters,
            [123]
        );
    }

    public function testThrowsIfFieldIsInvalid(): void
    {
        $validator = new SearchCompanyBySirenValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'Invalid field "invalid field". Allowed: siren, businessName, entityType, administrativeStatus, idInstance'
        );

        $filters = new SearchSirenFilters();

        $sirenFilter = new SearchSirenFiltersSiren();
        $sirenFilter->siren = '123456789';

        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = 'business name test';

        $filters->siren = $sirenFilter;
        $filters->businessName = $businessNameFilter;

        $validator->validate(
            $filters,
            ['invalid field', 'siren']
        );
    }

    public function testThrowsIfSirenFilterIsInvalid(): void
    {
        $validator = new SearchCompanyBySirenValidator();

        $filters = new SearchSirenFilters();

        $sirenFilter = new SearchSirenFiltersSiren();
        $sirenFilter->siren = 'invalid siren';

        $filters->siren = $sirenFilter;

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'siren must be exactly 9 digits (0-9)'
        );

        $validator->validate(
            $filters
        );
    }

    public function testThrowsIfBusinessNameFilterIsEmpty(): void
    {
        $validator = new SearchCompanyBySirenValidator();

        $filters = new SearchSirenFilters();

        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = '';

        $filters->businessName = $businessNameFilter;

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'businessName cannot be empty'
        );

        $validator->validate(
            $filters,
        );
    }

    public function testThrowsIfSortingIsInvalid(): void
    {
        $validator = new SearchCompanyBySirenValidator();

        $sorting = new SearchSirenSortingInner();
        $sorting->field = 'invalid field';
        $sorting->order = Order::ascending;

        $this->expectException(InvalidInputException::class);

        $filters = new SearchSirenFilters();

        $sirenFilter = new SearchSirenFiltersSiren();
        $sirenFilter->siren = '123456789';

        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = 'business name test';

        $filters->siren = $sirenFilter;
        $filters->businessName = $businessNameFilter;

        $validator->validate(
            $filters,
            null,
            [$sorting],
        );
    }

    public function testThrowsIfSortingOrderIsNull(): void
    {
        $validator = new SearchCompanyBySirenValidator();

        $sorting = new SearchSirenSortingInner();
        $sorting->field = 'siren';

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'Sorting order must exist'
        );

        $filters = new SearchSirenFilters();

        $sirenFilter = new SearchSirenFiltersSiren();
        $sirenFilter->siren = '123456789';

        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = 'business name test';

        $filters->siren = $sirenFilter;
        $filters->businessName = $businessNameFilter;

        $validator->validate(
            $filters,
            null,
            [$sorting],
        );
    }
}

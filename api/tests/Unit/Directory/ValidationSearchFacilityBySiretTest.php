<?php

declare(strict_types=1);

namespace App\Tests\Unit\Directory;

use App\Common\Exception\InvalidInputException;
use App\Directory\Enum\Order;
use App\Directory\Input\SearchSirenFiltersSiren;
use App\Directory\Input\SearchSiretFilters;
use App\Directory\Input\SearchSiretFiltersAddressLines;
use App\Directory\Input\SearchSiretFiltersCountrySubdivision;
use App\Directory\Input\SearchSiretFiltersLocality;
use App\Directory\Input\SearchSiretFiltersName;
use App\Directory\Input\SearchSiretFiltersPostalCode;
use App\Directory\Input\SearchSiretFiltersSiret;
use App\Directory\Input\SearchSiretSorting;
use App\Directory\Validation\SearchFacilityBySiretValidator;
use PHPUnit\Framework\TestCase;

final class ValidationSearchFacilityBySiretTest extends TestCase
{
    public function testValidateWithValidFieldsFiltersAndSorting(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $filters = new SearchSiretFilters();

        $siretFilter = new SearchSiretFiltersSiret();
        $siretFilter->siret = '12345678900000';

        $nameFilter = new SearchSiretFiltersName();
        $nameFilter->name = 'name test';

        $filters->siret = $siretFilter;
        $filters->name = $nameFilter;

        $sorting = new SearchSiretSorting();
        $sorting->field = 'siret';
        $sorting->order = Order::ascending;

        $sorting2 = new SearchSiretSorting();
        $sorting2->field = 'name';
        $sorting2->order = Order::descending;

        $validator->validate(
            [
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
            ],
            $filters,
            [$sorting, $sorting2],
        );

        self::assertTrue(true);
    }

    public function testThrowsIfFieldIsNotString(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'fields must be an array of strings'
        );

        $validator->validate(
            [123]
        );
    }

    public function testThrowsIfFieldIsInvalid(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $this->expectException(InvalidInputException::class);

        $validator->validate(
            ['invalid field', 'siret']
        );
    }

    public function testThrowsIfSiretFilterIsInvalid(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $filters = new SearchSiretFilters();

        $siretFilter = new SearchSiretFiltersSiret();
        $siretFilter->siret = 'invalid siret';

        $filters->siret = $siretFilter;

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'siret must be exactly 14 digits (0-9)'
        );

        $validator->validate(
            null,
            $filters,
        );
    }

    public function testThrowsIfSirenFilterIsInvalid(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $filters = new SearchSiretFilters();

        $sirenFilter = new SearchSirenFiltersSiren();
        $sirenFilter->siren = 'invalid siren';

        $filters->siren = $sirenFilter;

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'siren must be exactly 9 digits (0-9)'
        );

        $validator->validate(
            null,
            $filters,
        );
    }

    public function testThrowsIfPostalCodeFilterIsInvalid(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $filters = new SearchSiretFilters();

        $postalCodeFilter = new SearchSiretFiltersPostalCode();
        $postalCodeFilter->postalCode = 'invalid postalCode';

        $filters->postalCode = $postalCodeFilter;

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'postalCode must be exactly 5 digits (0-9)'
        );

        $validator->validate(
            null,
            $filters,
        );
    }

    public function testThrowsIfNameFilterIsEmpty(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $filters = new SearchSiretFilters();

        $nameFilter = new SearchSiretFiltersName();
        $nameFilter->name = '';

        $filters->name = $nameFilter;

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'name cannot be empty'
        );

        $validator->validate(
            null,
            $filters,
        );
    }

    public function testThrowsIfAddressLinesFilterIsEmpty(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $filters = new SearchSiretFilters();

        $addressLinesFilter = new SearchSiretFiltersAddressLines();
        $addressLinesFilter->addressLines = '';

        $filters->addressLines = $addressLinesFilter;

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'addressLines cannot be empty'
        );

        $validator->validate(
            null,
            $filters,
        );
    }

    public function testThrowsIfCountrySubdivisionFilterIsEmpty(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $filters = new SearchSiretFilters();

        $countrySubdivisionFilter = new SearchSiretFiltersCountrySubdivision();
        $countrySubdivisionFilter->countrySubdivision = '';

        $filters->countrySubdivision = $countrySubdivisionFilter;

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'countrySubdivision cannot be empty'
        );

        $validator->validate(
            null,
            $filters,
        );
    }

    public function testThrowsIfLocalityFilterIsEmpty(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $filters = new SearchSiretFilters();

        $localityFilter = new SearchSiretFiltersLocality();
        $localityFilter->locality = '';

        $filters->locality = $localityFilter;

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'locality cannot be empty'
        );

        $validator->validate(
            null,
            $filters,
        );
    }

    public function testThrowsIfSortingIsInvalid(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $sorting = new SearchSiretSorting();
        $sorting->field = 'invalid field';
        $sorting->order = Order::ascending;

        $this->expectException(InvalidInputException::class);

        $validator->validate(
            null,
            null,
            [$sorting],
        );
    }

    public function testThrowsIfSortingOrderIsNull(): void
    {
        $validator = new SearchFacilityBySiretValidator();

        $sorting = new SearchSiretSorting();
        $sorting->field = 'siret';

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage(
            'Sorting order must exist'
        );

        $validator->validate(
            null,
            null,
            [$sorting],
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Unit\Directory;

use App\Common\Exception\InvalidInputException;
use App\Directory\Validation\GetFacilityByIdValidator;
use PHPUnit\Framework\TestCase;

final class ValidationGetFacilityByIdTest extends TestCase
{
    public function testValidateWithValidIdAndFields(): void
    {
        $validator = new GetFacilityByIdValidator();

        $validator->validate(
            1,
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
            ]
        );

        self::assertTrue(true);
    }

    public function testValidateWithIdAndWithoutFields(): void
    {
        $validator = new GetFacilityByIdValidator();

        $validator->validate(1);

        self::assertTrue(true);
    }

    public function testThrowsIfIdInstanceIsZero(): void
    {
        $validator = new GetFacilityByIdValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('idInstance cannot be 0');

        $validator->validate(0);
    }

    public function testThrowsIfFieldIsNotString(): void
    {
        $validator = new GetFacilityByIdValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('fields must be an array of strings');

        $validator->validate(
            1,
            [123]
        );
    }

    public function testThrowsIfFieldIsNotAllowed(): void
    {
        $validator = new GetFacilityByIdValidator();

        $this->expectException(InvalidInputException::class);

        $validator->validate(
            1,
            ['invalidField']
        );
    }
}

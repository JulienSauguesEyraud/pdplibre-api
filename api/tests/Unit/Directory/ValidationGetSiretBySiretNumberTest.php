<?php

declare(strict_types=1);

namespace App\Tests\Unit\Directory;

use App\Common\Exception\InvalidInputException;
use App\Directory\Validation\GetCompanyByIdValidator;
use App\Directory\Validation\GetSiretBySiretNumberValidator;
use PHPUnit\Framework\TestCase;

final class ValidationGetSiretBySiretNumberTest extends TestCase
{
    public function testValidateWithValidSirenAndFields(): void
    {
        $validator = new GetSiretBySiretNumberValidator();

        $validator->validate(
            '12345678900000',
            [
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
            ]
        );

        self::assertTrue(true);
    }

    public function testValidateWithSiretAndWithoutFields(): void
    {
        $validator = new GetSiretBySiretNumberValidator();

        $validator->validate('12345678900000');

        self::assertTrue(true);
    }

    public function testThrowsIfSiretIsEmpty(): void
    {
        $validator = new GetSiretBySiretNumberValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('siret cannot be empty');

        $validator->validate('');
    }

    public function testThrowsIfSiretIsInvalid(): void
    {
        $validator = new GetSiretBySiretNumberValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('siret must be exactly 14 digits (0-9)');

        $validator->validate('invalid siret');
    }

    public function testThrowsIfFieldIsNotString(): void
    {
        $validator = new GetCompanyByIdValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('fields must be an array of strings');

        $validator->validate(
            1,
            [123]
        );
    }

    public function testThrowsIfFieldIsNotAllowed(): void
    {
        $validator = new GetCompanyByIdValidator();

        $this->expectException(InvalidInputException::class);

        $validator->validate(
            1,
            ['invalidField']
        );
    }
}

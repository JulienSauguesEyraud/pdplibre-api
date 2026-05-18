<?php

declare(strict_types=1);

namespace App\Tests\Unit\Directory;

use App\Common\Exception\InvalidInputException;
use App\Directory\Validation\GetCompanyByIdValidator;
use App\Directory\Validation\GetCompanyBySirenValidator;
use PHPUnit\Framework\TestCase;

final class ValidationGetCompanyBySirenTest extends TestCase
{
    public function testValidateWithValidSirenAndFields(): void
    {
        $validator = new GetCompanyBySirenValidator();

        $validator->validate(
            '123456789',
            [
                'siren',
                'businessName',
                'entityType',
                'administrativeStatus',
                'idInstance',
            ]
        );

        self::assertTrue(true);
    }

    public function testValidateWithSirenAndWithoutFields(): void
    {
        $validator = new GetCompanyBySirenValidator();

        $validator->validate('123456789');

        self::assertTrue(true);
    }

    public function testThrowsIfSirenIsEmpty(): void
    {
        $validator = new GetCompanyBySirenValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('siren cannot be empty');

        $validator->validate('');
    }

    public function testThrowsIfSirenIsInvalid(): void
    {
        $validator = new GetCompanyBySirenValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('siren must be exactly 9 digits (0-9)');

        $validator->validate('invalid siren');
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
        $this->expectExceptionMessage(
            'Invalid field "invalidField". Allowed: siren, businessName, entityType, administrativeStatus, idInstance'
        );

        $validator->validate(
            1,
            ['invalidField']
        );
    }
}

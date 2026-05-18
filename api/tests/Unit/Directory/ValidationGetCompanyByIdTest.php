<?php

declare(strict_types=1);

namespace App\Tests\Unit\Directory;

use App\Common\Exception\InvalidInputException;
use App\Directory\Validation\GetCompanyByIdValidator;
use PHPUnit\Framework\TestCase;

final class ValidationGetCompanyByIdTest extends TestCase
{
    public function testValidateWithValidIdAndFields(): void
    {
        $validator = new GetCompanyByIdValidator();

        $validator->validate(
            1,
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

    public function testValidateWithIdAndWithoutFields(): void
    {
        $validator = new GetCompanyByIdValidator();

        $validator->validate(1);

        self::assertTrue(true);
    }

    public function testThrowsIfIdInstanceIsZero(): void
    {
        $validator = new GetCompanyByIdValidator();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('idInstance cannot be 0');

        $validator->validate(0);
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

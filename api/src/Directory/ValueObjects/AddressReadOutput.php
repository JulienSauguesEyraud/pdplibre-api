<?php

declare(strict_types=1);

namespace App\Directory\ValueObjects;

use App\Directory\Doctrine\Entity\AddressRead;

final readonly class AddressReadOutput
{
    public function __construct(
        public string $addressLine1,
        public string $addressLine2,
        public string $addressLine3,
        public string $postalCode,
        public string $countrySubdivision,
        public string $locality,
        public string $countryCode,
        public string $countryName,
    ) {
    }

    public static function fromEntity(AddressRead $address): self
    {
        return new self(
            addressLine1: $address->getAddressLine1(),
            addressLine2: $address->getAddressLine2(),
            addressLine3: $address->getAddressLine3(),
            postalCode: $address->getPostalCode(),
            countrySubdivision: $address->getCountrySubdivision(),
            locality: $address->getLocality(),
            countryCode: $address->getCountryCode(),
            countryName: $address->getCountryName()
        );
    }
}

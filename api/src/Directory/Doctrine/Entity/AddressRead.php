<?php

declare(strict_types=1);

namespace App\Directory\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AddressRead
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', unique: true)]
    private int $id;

    #[ORM\Column(name: 'address_line1', length: 255)]
    private string $addressLine1;

    #[ORM\Column(name: 'address_line2', length: 255)]
    private ?string $addressLine2;

    #[ORM\Column(name: 'address_line3', length: 255)]
    private ?string $addressLine3;

    #[ORM\Column(name: 'postal_code', length: 5)]
    private string $postalCode;

    #[ORM\Column(name: 'country_subdivision', length: 255)]
    private string $countrySubdivision;

    #[ORM\Column(name: 'locality', length: 100)]
    private string $locality;

    #[ORM\Column(name: 'country_code', length: 2)]
    private string $countryCode;

    #[ORM\Column(name: 'country_name', length: 100)]
    private string $countryName;

    public static function create(string $addressLine1, string $addressLine2, string $addressLine3, string $postalCode, string $countrySubdivision, string $locality, string $countryCode, string $countryName): self
    {
        $self = new self();
        $self->addressLine1 = $addressLine1;
        $self->addressLine2 = $addressLine2;
        $self->addressLine3 = $addressLine3;
        $self->postalCode = $postalCode;
        $self->countrySubdivision = $countrySubdivision;
        $self->locality = $locality;
        $self->countryCode = $countryCode;
        $self->countryName = $countryName;

        return $self;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    public function getAddressLine2(): string
    {
        return $this->addressLine2;
    }

    public function getAddressLine3(): string
    {
        return $this->addressLine3;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountrySubdivision(): string
    {
        return $this->countrySubdivision;
    }

    public function getLocality(): string
    {
        return $this->locality;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getCountryName(): string
    {
        return $this->countryName;
    }
}

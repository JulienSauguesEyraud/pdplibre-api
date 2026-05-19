<?php

declare(strict_types=1);

namespace App\Directory\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class B2gAdditionalData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', unique: true)]
    private int $id;

    #[ORM\Column(name: 'pm')]
    private bool $pm;

    #[ORM\Column(name: 'pm_only')]
    private bool $pmOnly;

    #[ORM\Column(name: 'manages_payment_status')]
    private bool $managesPaymentStatus;

    #[ORM\Column(name: 'manages_legal_commitment_code')]
    private bool $managesLegalCommitmentCode;

    #[ORM\Column(name: 'manages_legal_commitment_or_service_code')]
    private bool $managesLegalCommitmentOrServiceCode;

    #[ORM\Column(name: 'service_code_status')]
    private bool $serviceCodeStatus;

    public static function create(bool $pm, bool $pmOnly, bool $managesPaymentStatus, bool $managesLegalCommitmentCode, bool $managesLegalCommitmentOrServiceCode, bool $serviceCodeStatus): B2gAdditionalData
    {
        $self = new self();
        $self->pm = $pm;
        $self->pmOnly = $pmOnly;
        $self->managesPaymentStatus = $managesPaymentStatus;
        $self->managesLegalCommitmentCode = $managesLegalCommitmentCode;
        $self->managesLegalCommitmentOrServiceCode = $serviceCodeStatus;
        $self->serviceCodeStatus = $serviceCodeStatus;

        return $self;
    }

    public function getPm(): bool
    {
        return $this->pm;
    }

    public function getPmOnly(): bool
    {
        return $this->pmOnly;
    }

    public function getManagesPaymentStatus(): bool
    {
        return $this->managesPaymentStatus;
    }

    public function getManagesLegalCommitmentCode(): bool
    {
        return $this->managesLegalCommitmentCode;
    }

    public function getManagesLegalCommitmentOrServiceCode(): bool
    {
        return $this->managesLegalCommitmentOrServiceCode;
    }

    public function getServiceCodeStatus(): bool
    {
        return $this->serviceCodeStatus;
    }
}

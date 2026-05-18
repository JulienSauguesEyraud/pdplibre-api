<?php

declare(strict_types=1);

namespace App\Tests\Application\Directory;

use App\Directory\Doctrine\Entity\AddressRead;
use App\Directory\Doctrine\Entity\B2gAdditionalData;
use App\Directory\Doctrine\Entity\FacilityPayloadHistory;
use App\Directory\Doctrine\Entity\LegalUnitPayloadHistory;
use App\Directory\Enum\DiffusionStatus;
use App\Directory\Enum\EntityType;
use App\Directory\Enum\FacilityAdministrativeStatus;
use App\Directory\Enum\FacilityType;
use App\Directory\Enum\LegalUnitAdministrativeStatus;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetSiretBySiretNumberTest extends WebTestCase
{
    public function testGetSiretByIdInstance(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        $em = $container->get('doctrine')->getManager();

        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\FacilityPayloadHistory e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\AddressRead e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\B2gAdditionalData e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\LegalUnitPayloadHistory e')->execute();

        $address = AddressRead::create(
            addressLine1: 'address 1',
            addressLine2: 'address 2',
            addressLine3: 'address 3',
            postalCode: '12345',
            countrySubdivision: 'subdivision',
            locality: 'locality',
            countryCode: 'FR',
            countryName: 'France',
        );

        $em->persist($address);

        $b2g = B2gAdditionalData::create(
            pm: true,
            pmOnly: true,
            managesPaymentStatus: true,
            managesLegalCommitmentCode: true,
            managesLegalCommitmentOrServiceCode: true,
            serviceCodeStatus: true,
        );

        $em->persist($b2g);

        $legalUnit = LegalUnitPayloadHistory::create(
            idInstance: 10,
            siren: '123456789',
            businessName: 'test business name',
            entityType: EntityType::Public,
            administrativeStatus: LegalUnitAdministrativeStatus::A,
        );

        $em->persist($legalUnit);

        $entity = FacilityPayloadHistory::create(
            idInstance: 1,
            siret: '12345678900000',
            siren: '123456789',
            name: 'test name',
            facilityType: FacilityType::P,
            diffusible: DiffusionStatus::P,
            administrativeStatus: FacilityAdministrativeStatus::A,
            address: $address,
            b2gAdditionalData: $b2g,
            legalUnit: $legalUnit
        );

        $em->persist($entity);
        $em->flush();

        $client->request('GET', '/v1/siret/code-insee:12345678900000');

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame(1, $response['idInstance']);
        self::assertSame('123456789', $response['siren']);
        self::assertSame('12345678900000', $response['siret']);
        self::assertSame('test name', $response['name']);
        self::assertSame('P', $response['facilityType']);
        self::assertSame('P', $response['diffusible']);
        self::assertSame('A', $response['administrativeStatus']);

        self::assertSame('address 1', $response['address']['addressLine1']);
        self::assertSame('address 2', $response['address']['addressLine2']);
        self::assertSame('address 3', $response['address']['addressLine3']);
        self::assertSame('12345', $response['address']['postalCode']);
        self::assertSame('FR', $response['address']['countryCode']);

        self::assertSame(true, $response['b2gAdditionalData']['pm']);
        self::assertSame(true, $response['b2gAdditionalData']['pmOnly']);
        self::assertSame(true, $response['b2gAdditionalData']['managesPaymentStatus']);
        self::assertSame(true, $response['b2gAdditionalData']['managesLegalCommitmentCode']);
        self::assertSame(true, $response['b2gAdditionalData']['managesLegalCommitmentOrServiceCode']);
        self::assertSame(true, $response['b2gAdditionalData']['serviceCodeStatus']);

        self::assertSame('test business name', $response['legalUnit']['businessName']);
        self::assertSame('Public', $response['legalUnit']['entityType']);
        self::assertSame('A', $response['legalUnit']['administrativeStatus']);
    }

    public function testNotFound(): void
    {
        $client = self::createClient();

        $client->request('GET', '/v1/siret/code-insee:00000000000000');

        self::assertResponseStatusCodeSame(404);
    }

    public function testGetSiretByIdInstanceWithFields(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        $em = $container->get('doctrine')->getManager();

        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\FacilityPayloadHistory e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\AddressRead e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\B2gAdditionalData e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\LegalUnitPayloadHistory e')->execute();

        $address = AddressRead::create(
            addressLine1: 'address 1',
            addressLine2: 'address 2',
            addressLine3: 'address 3',
            postalCode: '12345',
            countrySubdivision: 'subdivision',
            locality: 'locality',
            countryCode: 'FR',
            countryName: 'France',
        );

        $em->persist($address);

        $b2g = B2gAdditionalData::create(
            pm: true,
            pmOnly: true,
            managesPaymentStatus: true,
            managesLegalCommitmentCode: true,
            managesLegalCommitmentOrServiceCode: true,
            serviceCodeStatus: true,
        );

        $em->persist($b2g);

        $legalUnit = LegalUnitPayloadHistory::create(
            idInstance: 10,
            siren: '123456789',
            businessName: 'test business name',
            entityType: EntityType::Public,
            administrativeStatus: LegalUnitAdministrativeStatus::A,
        );

        $em->persist($legalUnit);

        $entity = FacilityPayloadHistory::create(
            idInstance: 1,
            siret: '12345678900000',
            siren: '123456789',
            name: 'test name',
            facilityType: FacilityType::P,
            diffusible: DiffusionStatus::P,
            administrativeStatus: FacilityAdministrativeStatus::A,
            address: $address,
            b2gAdditionalData: $b2g,
            legalUnit: $legalUnit
        );

        $em->persist($entity);
        $em->flush();

        $client->request('GET', '/v1/siret/code-insee:12345678900000',
        [
            'fields' => ['siret', 'name', 'facilityType', 'address', 'pmOnly']
        ]);

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertNull($response['idInstance']);
        self::assertNull($response['siren']);
        self::assertSame('12345678900000', $response['siret']);
        self::assertSame('test name', $response['name']);
        self::assertSame('P', $response['facilityType']);
        self::assertNull($response['diffusible']);
        self::assertNull($response['administrativeStatus']);

        self::assertSame('address 1', $response['address']['addressLine1']);
        self::assertSame('address 2', $response['address']['addressLine2']);
        self::assertSame('address 3', $response['address']['addressLine3']);
        self::assertSame('12345', $response['address']['postalCode']);
        self::assertSame('FR', $response['address']['countryCode']);

        self::assertNull($response['b2gAdditionalData']['pm']);
        self::assertSame(true, $response['b2gAdditionalData']['pmOnly']);
        self::assertNull($response['b2gAdditionalData']['managesPaymentStatus']);
        self::assertNull($response['b2gAdditionalData']['managesLegalCommitmentCode']);
        self::assertNull($response['b2gAdditionalData']['managesLegalCommitmentOrServiceCode']);
        self::assertNull($response['b2gAdditionalData']['serviceCodeStatus']);

        self::assertSame('test business name', $response['legalUnit']['businessName']);
        self::assertSame('Public', $response['legalUnit']['entityType']);
        self::assertSame('A', $response['legalUnit']['administrativeStatus']);
    }
}

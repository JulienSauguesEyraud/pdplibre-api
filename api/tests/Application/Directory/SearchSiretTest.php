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
use App\Directory\Enum\Order;
use App\Directory\Input\SearchSirenFilters;
use App\Directory\Input\SearchSirenFiltersBusinessName;
use App\Directory\Input\SearchSiretFilters;
use App\Directory\Input\SearchSiretFiltersName;
use App\Directory\Input\SearchSiretSorting;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SearchSiretTest extends WebTestCase
{
    public function testSearchSiret(): void
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

        $filters = new SearchSiretFilters();
        $nameFilter = new SearchSiretFiltersName();
        $nameFilter->name = 'test name';
        $filters->name = $nameFilter;

        $client->request(
            'POST',
            '/v1/siret/search',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'filters' => $filters,
            ])
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame(25, $response['limit']);

        self::assertSame($filters->name->name, $response['filters']['name']['name']);
        self::assertSame($filters->name->operator->value, $response['filters']['name']['operator']);

        self::assertNull($response['fields']);
        self::assertNull($response['sorting']);

        self::assertSame(1, $response['results'][0]['idInstance']);
        self::assertSame('123456789', $response['results'][0]['siren']);
        self::assertSame('12345678900000', $response['results'][0]['siret']);
        self::assertSame('test name', $response['results'][0]['name']);
        self::assertSame('P', $response['results'][0]['facilityType']);
        self::assertSame('P', $response['results'][0]['diffusible']);
        self::assertSame('A', $response['results'][0]['administrativeStatus']);

        self::assertSame('address 1', $response['results'][0]['address']['addressLine1']);
        self::assertSame('address 2', $response['results'][0]['address']['addressLine2']);
        self::assertSame('address 3', $response['results'][0]['address']['addressLine3']);
        self::assertSame('12345', $response['results'][0]['address']['postalCode']);
        self::assertSame('FR', $response['results'][0]['address']['countryCode']);

        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['pm']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['pmOnly']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['managesPaymentStatus']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['managesLegalCommitmentCode']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['managesLegalCommitmentOrServiceCode']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['serviceCodeStatus']);

        self::assertSame('test business name', $response['results'][0]['legalUnit']['businessName']);
        self::assertSame('Public', $response['results'][0]['legalUnit']['entityType']);
        self::assertSame('A', $response['results'][0]['legalUnit']['administrativeStatus']);
    }

    public function testSearchSiretWithFields(): void
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

        $filters = new SearchSiretFilters();
        $nameFilter = new SearchSiretFiltersName();
        $nameFilter->name = 'test name';
        $filters->name = $nameFilter;

        $client->request(
            'POST',
            '/v1/siret/search',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'fields' => [
                    'siret',
                    'name',
                    'facilityType',
                    'address',
                    'pmOnly'
                ],
                'filters' => $filters
            ])
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame(25, $response['limit']);

        self::assertSame($filters->name->name, $response['filters']['name']['name']);
        self::assertSame($filters->name->operator->value, $response['filters']['name']['operator']);

        self::assertContains('siret', $response['fields']);
        self::assertContains('name', $response['fields']);
        self::assertContains('facilityType', $response['fields']);
        self::assertContains('address', $response['fields']);
        self::assertContains('pmOnly', $response['fields']);

        self::assertNull($response['sorting']);

        self::assertNull($response['results'][0]['idInstance']);
        self::assertNull($response['results'][0]['siren']);
        self::assertSame('12345678900000', $response['results'][0]['siret']);
        self::assertSame('test name', $response['results'][0]['name']);
        self::assertSame('P', $response['results'][0]['facilityType']);
        self::assertNull($response['results'][0]['diffusible']);
        self::assertNull($response['results'][0]['administrativeStatus']);

        self::assertSame('address 1', $response['results'][0]['address']['addressLine1']);
        self::assertSame('address 2', $response['results'][0]['address']['addressLine2']);
        self::assertSame('address 3', $response['results'][0]['address']['addressLine3']);
        self::assertSame('12345', $response['results'][0]['address']['postalCode']);
        self::assertSame('FR', $response['results'][0]['address']['countryCode']);

        self::assertNull($response['results'][0]['b2gAdditionalData']['pm']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['pmOnly']);
        self::assertNull($response['results'][0]['b2gAdditionalData']['managesPaymentStatus']);
        self::assertNull($response['results'][0]['b2gAdditionalData']['managesLegalCommitmentCode']);
        self::assertNull($response['results'][0]['b2gAdditionalData']['managesLegalCommitmentOrServiceCode']);
        self::assertNull($response['results'][0]['b2gAdditionalData']['serviceCodeStatus']);

        self::assertSame('test business name', $response['results'][0]['legalUnit']['businessName']);
        self::assertSame('Public', $response['results'][0]['legalUnit']['entityType']);
        self::assertSame('A', $response['results'][0]['legalUnit']['administrativeStatus']);
    }

////TODO
//    public function testSearchSiretWithSorting(): void
//    {
//        $client = self::createClient();
//        $container = self::getContainer();
//
//        $em = $container->get('doctrine')->getManager();
//
//        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\FacilityPayloadHistory e')->execute();
//        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\AddressRead e')->execute();
//        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\B2gAdditionalData e')->execute();
//        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\LegalUnitPayloadHistory e')->execute();
//
//        $address = AddressRead::create(
//            addressLine1: 'address 1',
//            addressLine2: 'address 2',
//            addressLine3: 'address 3',
//            postalCode: '12345',
//            countrySubdivision: 'subdivision',
//            locality: 'locality',
//            countryCode: 'FR',
//            countryName: 'France',
//        );
//
//        $em->persist($address);
//
//        $b2g = B2gAdditionalData::create(
//            pm: true,
//            pmOnly: true,
//            managesPaymentStatus: true,
//            managesLegalCommitmentCode: true,
//            managesLegalCommitmentOrServiceCode: true,
//            serviceCodeStatus: true,
//        );
//
//        $em->persist($b2g);
//
//        $legalUnit = LegalUnitPayloadHistory::create(
//            idInstance: 10,
//            siren: '123456789',
//            businessName: 'test business name',
//            entityType: EntityType::Public,
//            administrativeStatus: LegalUnitAdministrativeStatus::A,
//        );
//
//        $em->persist($legalUnit);
//
//        $entity = FacilityPayloadHistory::create(
//            idInstance: 1,
//            siret: '12345678900000',
//            siren: '123456789',
//            name: 'test name',
//            facilityType: FacilityType::P,
//            diffusible: DiffusionStatus::P,
//            administrativeStatus: FacilityAdministrativeStatus::A,
//            address: $address,
//            b2gAdditionalData: $b2g,
//            legalUnit: $legalUnit
//        );
//
//        $em->persist($entity);
//        $em->flush();
//
//        $filters = new SearchSiretFilters();
//        $nameFilter = new SearchSiretFiltersName();
//        $nameFilter->name = 'test name';
//        $filters->name = $nameFilter;
//
//        $sorting = new SearchSiretSorting();
//        $sorting->field = 'siret';
//        $sorting->order = Order::ascending;
//
//        $client->request(
//            'POST',
//            '/v1/siret/search',
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json'],
//            json_encode([
//                'filters' => $filters,
//                'sorting' => [$sorting]
//            ])
//        );
//
//        self::assertResponseIsSuccessful();
//
//        $response = json_decode($client->getResponse()->getContent(), true);
//
//        self::assertSame(25, $response['limit']);
//
//        self::assertSame($filters->name->name, $response['filters']['name']['name']);
//        self::assertSame($filters->name->operator->value, $response['filters']['name']['operator']);
//
//        self::assertNull($response['fields']);
//
//        self::assertSame($sorting->field, $response['sorting'][0]['field']);
//        self::assertSame($sorting->order->value, $response['sorting'][0]['order']);
//
//        self::assertSame(1, $response['results'][0]['idInstance']);
//        self::assertSame('123456789', $response['results'][0]['siren']);
//        self::assertSame('12345678900000', $response['results'][0]['siret']);
//        self::assertSame('test name', $response['results'][0]['name']);
//        self::assertSame('P', $response['results'][0]['facilityType']);
//        self::assertSame('P', $response['results'][0]['diffusible']);
//        self::assertSame('A', $response['results'][0]['administrativeStatus']);
//
//        self::assertSame('address 1', $response['results'][0]['address']['addressLine1']);
//        self::assertSame('address 2', $response['results'][0]['address']['addressLine2']);
//        self::assertSame('address 3', $response['results'][0]['address']['addressLine3']);
//        self::assertSame('12345', $response['results'][0]['address']['postalCode']);
//        self::assertSame('FR', $response['results'][0]['address']['countryCode']);
//
//        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['pm']);
//        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['pmOnly']);
//        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['managesPaymentStatus']);
//        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['managesLegalCommitmentCode']);
//        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['managesLegalCommitmentOrServiceCode']);
//        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['serviceCodeStatus']);
//
//        self::assertSame('test business name', $response['results'][0]['legalUnit']['businessName']);
//        self::assertSame('Public', $response['results'][0]['legalUnit']['entityType']);
//        self::assertSame('A', $response['results'][0]['legalUnit']['administrativeStatus']);
//    }

    public function testSearchSiretWithLimit(): void
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

        $filters = new SearchSiretFilters();
        $nameFilter = new SearchSiretFiltersName();
        $nameFilter->name = 'test name';
        $filters->name = $nameFilter;

        $client->request(
            'POST',
            '/v1/siret/search',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'filters' => $filters,
                'limit' => 10,
            ])
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame(10, $response['limit']);

        self::assertSame($filters->name->name, $response['filters']['name']['name']);
        self::assertSame($filters->name->operator->value, $response['filters']['name']['operator']);

        self::assertNull($response['fields']);
        self::assertNull($response['sorting']);

        self::assertSame(1, $response['results'][0]['idInstance']);
        self::assertSame('123456789', $response['results'][0]['siren']);
        self::assertSame('12345678900000', $response['results'][0]['siret']);
        self::assertSame('test name', $response['results'][0]['name']);
        self::assertSame('P', $response['results'][0]['facilityType']);
        self::assertSame('P', $response['results'][0]['diffusible']);
        self::assertSame('A', $response['results'][0]['administrativeStatus']);

        self::assertSame('address 1', $response['results'][0]['address']['addressLine1']);
        self::assertSame('address 2', $response['results'][0]['address']['addressLine2']);
        self::assertSame('address 3', $response['results'][0]['address']['addressLine3']);
        self::assertSame('12345', $response['results'][0]['address']['postalCode']);
        self::assertSame('FR', $response['results'][0]['address']['countryCode']);

        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['pm']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['pmOnly']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['managesPaymentStatus']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['managesLegalCommitmentCode']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['managesLegalCommitmentOrServiceCode']);
        self::assertSame(true, $response['results'][0]['b2gAdditionalData']['serviceCodeStatus']);

        self::assertSame('test business name', $response['results'][0]['legalUnit']['businessName']);
        self::assertSame('Public', $response['results'][0]['legalUnit']['entityType']);
        self::assertSame('A', $response['results'][0]['legalUnit']['administrativeStatus']);

    }

    public function testNoResult(): void
    {
        $client = self::createClient();

        $filters = new SearchSiretFilters();
        $nameFilter = new SearchSiretFiltersName();
        $nameFilter->name = 'invalid name';
        $filters->name = $nameFilter;

        $client->request(
            'POST',
            '/v1/siret/search',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'filters' => $filters
            ])
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame(25, $response['limit']);

        self::assertSame($filters->name->name, $response['filters']['name']['name']);
        self::assertSame($filters->name->operator->value, $response['filters']['name']['operator']);

        self::assertNull($response['fields']);
        self::assertNull($response['sorting']);

        self::assertCount(0, $response['results']);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Application\Directory;

use App\Directory\Doctrine\Entity\LegalUnitPayloadHistory;
use App\Directory\Enum\EntityType;
use App\Directory\Enum\LegalUnitAdministrativeStatus;
use App\Directory\Enum\Order;
use App\Directory\Input\SearchSirenFilters;
use App\Directory\Input\SearchSirenFiltersBusinessName;
use App\Directory\Input\SearchSirenSorting;
use App\Directory\Input\SearchSiretFilters;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SearchSirenTest extends WebTestCase
{
    public function testSearchSiren(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        $em = $container->get('doctrine')->getManager();

        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\FacilityPayloadHistory e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\AddressRead e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\B2gAdditionalData e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\LegalUnitPayloadHistory e')->execute();

        $entity = LegalUnitPayloadHistory::create(
            idInstance: 1,
            siren: '123456789',
            businessName: 'test business name',
            entityType: EntityType::Public,
            administrativeStatus: LegalUnitAdministrativeStatus::A,
        );

        $em->persist($entity);
        $em->flush();

        $filters = new SearchSirenFilters();
        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = 'test business name';
        $filters->businessName = $businessNameFilter;

        $client->request(
            'POST',
            '/v1/siren/search',
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

        self::assertSame($filters->businessName->businessName, $response['filters']['businessName']['businessName']);
        self::assertSame($filters->businessName->operator->value, $response['filters']['businessName']['operator']);

        self::assertNull($response['fields']);
        self::assertNull($response['sorting']);

        self::assertSame(1, $response['results'][0]['idInstance']);
        self::assertSame('123456789', $response['results'][0]['siren']);
        self::assertSame('test business name', $response['results'][0]['businessName']);
        self::assertSame('Public', $response['results'][0]['entityType']);
        self::assertSame('A', $response['results'][0]['administrativeStatus']);
    }

    public function testSearchSirenWithFields(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        $em = $container->get('doctrine')->getManager();

        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\FacilityPayloadHistory e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\AddressRead e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\B2gAdditionalData e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\LegalUnitPayloadHistory e')->execute();

        $entity = LegalUnitPayloadHistory::create(
            idInstance: 1,
            siren: '123456789',
            businessName: 'test business name',
            entityType: EntityType::Public,
            administrativeStatus: LegalUnitAdministrativeStatus::A,
        );

        $em->persist($entity);
        $em->flush();

        $filters = new SearchSirenFilters();
        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = 'test business name';
        $filters->businessName = $businessNameFilter;

        $client->request(
            'POST',
            '/v1/siren/search',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'fields' => [
                    'siren',
                    'businessName',
                ],
                'filters' => $filters
            ])
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame(25, $response['limit']);

        self::assertSame($filters->businessName->businessName, $response['filters']['businessName']['businessName']);
        self::assertSame($filters->businessName->operator->value, $response['filters']['businessName']['operator']);

        self::assertContains('siren', $response['fields']);
        self::assertContains('businessName', $response['fields']);

        self::assertNull($response['sorting']);

        self::assertArrayNotHasKey('idInstance', $response['results'][0]);
        self::assertSame('123456789', $response['results'][0]['siren']);
        self::assertSame('test business name', $response['results'][0]['businessName']);
        self::assertArrayNotHasKey('entityType', $response['results'][0]);
        self::assertArrayNotHasKey('administrativeStatus', $response['results'][0]);
    }

    public function testSearchSirenWithSorting(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        $em = $container->get('doctrine')->getManager();

        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\FacilityPayloadHistory e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\AddressRead e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\B2gAdditionalData e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\LegalUnitPayloadHistory e')->execute();

        $entity = LegalUnitPayloadHistory::create(
            idInstance: 1,
            siren: '123456789',
            businessName: 'test business name',
            entityType: EntityType::Public,
            administrativeStatus: LegalUnitAdministrativeStatus::A,
        );

        $em->persist($entity);
        $em->flush();

        $filters = new SearchSirenFilters();
        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = 'test business name';
        $filters->businessName = $businessNameFilter;

        $sorting = new SearchSirenSorting();
        $sorting->field = 'siren';
        $sorting->order = Order::ascending;

        $client->request(
            'POST',
            '/v1/siren/search',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'filters' => $filters,
                'sorting' => [$sorting]
            ])
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame(25, $response['limit']);

        self::assertSame($filters->businessName->businessName, $response['filters']['businessName']['businessName']);
        self::assertSame($filters->businessName->operator->value, $response['filters']['businessName']['operator']);

        self::assertNull($response['fields']);

        self::assertSame($sorting->field, $response['sorting'][0]['field']);
        self::assertSame($sorting->order->value, $response['sorting'][0]['order']);

        self::assertSame(1, $response['results'][0]['idInstance']);
        self::assertSame('123456789', $response['results'][0]['siren']);
        self::assertSame('test business name', $response['results'][0]['businessName']);
        self::assertSame('Public', $response['results'][0]['entityType']);
        self::assertSame('A', $response['results'][0]['administrativeStatus']);
    }

    public function testSearchSirenWithLimit(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        $em = $container->get('doctrine')->getManager();

        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\FacilityPayloadHistory e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\AddressRead e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\B2gAdditionalData e')->execute();
        $em->createQuery('DELETE FROM App\Directory\Doctrine\Entity\LegalUnitPayloadHistory e')->execute();

        $entity = LegalUnitPayloadHistory::create(
            idInstance: 1,
            siren: '123456789',
            businessName: 'test business name',
            entityType: EntityType::Public,
            administrativeStatus: LegalUnitAdministrativeStatus::A,
        );

        $em->persist($entity);
        $em->flush();

        $filters = new SearchSirenFilters();
        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = 'test business name';
        $filters->businessName = $businessNameFilter;

        $client->request(
            'POST',
            '/v1/siren/search',
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

        self::assertSame($filters->businessName->businessName, $response['filters']['businessName']['businessName']);
        self::assertSame($filters->businessName->operator->value, $response['filters']['businessName']['operator']);

        self::assertNull($response['fields']);
        self::assertNull($response['sorting']);

        self::assertSame(1, $response['results'][0]['idInstance']);
        self::assertSame('123456789', $response['results'][0]['siren']);
        self::assertSame('test business name', $response['results'][0]['businessName']);
        self::assertSame('Public', $response['results'][0]['entityType']);
        self::assertSame('A', $response['results'][0]['administrativeStatus']);

    }

    public function testNoResult(): void
    {
        $client = self::createClient();


        $filters = new SearchSirenFilters();
        $businessNameFilter = new SearchSirenFiltersBusinessName();
        $businessNameFilter->businessName = 'invalid name';
        $filters->businessName = $businessNameFilter;

        $client->request(
            'POST',
            '/v1/siren/search',
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

        self::assertSame($filters->businessName->businessName, $response['filters']['businessName']['businessName']);
        self::assertSame($filters->businessName->operator->value, $response['filters']['businessName']['operator']);

        self::assertNull($response['fields']);
        self::assertNull($response['sorting']);

        self::assertCount(0, $response['results']);
    }
}

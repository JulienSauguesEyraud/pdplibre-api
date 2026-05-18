<?php

declare(strict_types=1);

namespace App\Tests\Application\Directory;

use App\Directory\Doctrine\Entity\LegalUnitPayloadHistory;
use App\Directory\Enum\EntityType;
use App\Directory\Enum\LegalUnitAdministrativeStatus;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetCompanyBySirenTest extends WebTestCase
{
    public function testGetCompanyBySiren(): void
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

        $client->request('GET', '/v1/siren/code-insee:123456789');

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame(1, $response['idInstance']);
        self::assertSame('123456789', $response['siren']);
        self::assertSame('test business name', $response['businessName']);
        self::assertSame('Public', $response['entityType']);
        self::assertSame('A', $response['administrativeStatus']);
    }

    public function testGetCompanyBySirenWithFields(): void
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

        $client->request('GET', '/v1/siren/code-insee:123456789',
            [
                'fields' => ['siren', 'businessName']
            ]);

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertArrayNotHasKey('idInstance', $response);
        self::assertSame('123456789', $response['siren']);
        self::assertSame('test business name', $response['businessName']);
        self::assertArrayNotHasKey('entityType', $response);
        self::assertArrayNotHasKey('administrativeStatus', $response);
    }

    public function testNotFound(): void
    {
        $client = self::createClient();

        $client->request('GET', '/v1/siren/code-insee:000000000');

        self::assertResponseStatusCodeSame(404);
    }
}

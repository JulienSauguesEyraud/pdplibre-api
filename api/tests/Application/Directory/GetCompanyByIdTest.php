<?php

declare(strict_types=1);

namespace App\Tests\Application\Directory;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetCompanyByIdTest extends WebTestCase
{
    public function testGetCompanyById(): void
    {
        $client = self::createClient();

        $client->request('GET', '/v1/siren/id-instance:1');

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertSame(1, $response['idInstance']);
        self::assertSame('123456789', $response['siren']);
        self::assertSame('test business name', $response['businessName']);
        self::assertSame('Public', $response['entityType']);
        self::assertSame('A', $response['administrativeStatus']);
    }

    public function testGetCompanyByIdWithFields(): void
    {
        $client = self::createClient();

        $client->request('GET', '/v1/siren/id-instance:1',
            [
                'fields' => ['siren', 'businessName'],
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

        $client->request('GET', '/v1/siren/id-instance:999999');

        self::assertResponseStatusCodeSame(404);
    }
}

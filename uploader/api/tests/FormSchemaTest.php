<?php

declare(strict_types=1);

namespace App\Tests;

use Alchemy\RemoteAuthBundle\Security\RemoteAuthenticatorClientTestMock;

class FormSchemaTest extends AbstractTestCase
{
    public function testFormSchemaEditOK(): void
    {
        $response = $this->request(RemoteAuthenticatorClientTestMock::ADMIN_TOKEN, 'GET', '/form-schema');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(json_decode(file_get_contents(__DIR__.'/fixtures/liform-schema.json'))), $response->getContent());

        $response = $this->request(RemoteAuthenticatorClientTestMock::ADMIN_TOKEN, 'POST', '/form-schema/edit', [
            'schema' => [
                'foo' => 'bar',
            ],
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertEquals(true, $json);

        $response = $this->request(RemoteAuthenticatorClientTestMock::ADMIN_TOKEN, 'GET', '/form-schema');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"foo":"bar"}', $response->getContent());
    }

    public function testFormSchemaEditWithANonAdminUser(): void
    {
        $response = $this->request(RemoteAuthenticatorClientTestMock::USER_TOKEN, 'POST', '/form-schema/edit', [
            'schema' => [
                'foo' => 'bar',
            ],
        ]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testFormSchemaEditWithAnonymousUser(): void
    {
        $response = $this->request(null, 'POST', '/form-schema/edit', [
            'schema' => [
                'foo' => 'bar',
            ],
        ]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testFormSchemaGetWithAnonymousUser(): void
    {
        $response = $this->request(null, 'GET', '/form-schema');
        $this->assertEquals(401, $response->getStatusCode());
    }
}

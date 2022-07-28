<?php

use PHPUnit\Framework\TestCase;

class RequestServiceTest extends TestCase
{
    private RequestService $requestService;

    function setUp() : void
    {
        $this->requestService = new RequestService("https://api.github.com", true);
        $this->requestService->setHeaders(["User-Agent: robledocampos"]);
    }

    function testGetWithoutEndpoint()
    {
        $result = $this->requestService->make();
        $this->assertEquals(200, $result['status_code']);
        $this->assertNotEmpty($result['body']);
    }

    function testGetWithEndpoint()
    {
        $this->requestService->setEndpoint("/users/defunkt");
        $result = $this->requestService->make();
        $body = json_decode($result['body']);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals("defunkt", $body->login);
    }

    function testGetWithPayload()
    {
        $this->requestService->setEndpoint("/users/defunkt");
        $this->requestService->setJsonPayload(['name' => "Jhon Doe"]);
        $this->requestService->setMethod("GET");
        $result = $this->requestService->make();
        $body = json_decode($result['body']);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals("defunkt", $body->login);
    }

    function testGetWithQueryString()
    {
        $this->requestService->setEndpoint("/users/defunkt/repos");
        $this->requestService->setQueryString(['page' => 2, 'per_page' => 3]);
        $result = $this->requestService->make();
        $this->assertEquals(200, $result['status_code']);
        $this->assertNotEmpty($result['body']);
    }

    function testPostWithEndpoint()
    {
        $this->requestService->setEndpoint("/users/defunkt");
        $this->requestService->setMethod("POST");
        $result = $this->requestService->make();
        $this->assertEquals(404, $result['status_code']);
        $this->assertNotEmpty($result['body']);
    }

    function testUnknownMethodException()
    {
        $this->expectException("UnknownMethodException");
        $this->requestService->setMethod("DEL");
    }

    function testNonUTF8Payload()
    {
        $this->expectException('JsonEncodeException');
        $this->requestService->setJsonPayload(['name' => "Jhon Doe \xff"]);
    }
}

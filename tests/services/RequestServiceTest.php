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

    function testUnknownMethodException()
    {
        $this->expectException("UnknownMethodException");
        $this->requestService->setMethod("DEL");
    }

    function testCall()
    {
        $this->requestService->setEndpoint("/users/defunkt");
        $result = $this->requestService->call();
        $body = json_decode($result['body']);
        $this->assertEquals(200, $result['httpCode']);
        $this->assertEquals("defunkt", $body->login);
    }

    function testCallWithQueryString()
    {
        $this->requestService->setEndpoint("/users/defunkt/repos");
        $this->requestService->setQueryString(['page' => 2, 'per_page' => 3]);
        $result = $this->requestService->call();
        $this->assertEquals(200, $result['httpCode']);
        $this->assertNotEmpty($result['body']);
    }

    function testCallWithPayload()
    {
        $this->requestService->setEndpoint("/users/defunkt/repos");
        $this->requestService->setPayload();
        $this->requestService->setQueryString(['page' => 2, 'per_page' => 3]);
        $result = $this->requestService->call();
        $this->assertEquals(200, $result['httpCode']);
        $this->assertNotEmpty($result['body']);
    }
}

<?php

namespace Silktide\FreshdeskApi\Test;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Silktide\FreshdeskApi\Client;
use GuzzleHttp\Client as Guzzle;
use Silktide\FreshdeskApi\ResponseFactory;
use PHPUnit_Framework_MockObject_MockObject;

class ClientTest extends BaseTest
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Guzzle
     */
    protected $guzzle;

    /**
     * @var ResponseFactory | PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseFactoryMock;

    /**
     * @var MockHandler
     */
    protected $guzzleMockHandler;

    /**
     * @var string
     */
    protected $username = "ausername";

    /**
     * @var string
     */
    protected $password = "apassword";

    /**
     * @var string
     */
    protected $domain = "http://adomain.freshdesk.com";

    public function setup()
    {
        $this->guzzleMockHandler = new MockHandler();
        $this->guzzle = new Guzzle(['handler' => $this->guzzleMockHandler]);
        $this->responseFactoryMock = $this->getMockBuilder('Silktide\FreshdeskApi\ResponseFactory')->getMock();
        $this->client = new Client($this->guzzle, $this->responseFactoryMock, $this->domain, $this->username, $this->password);
    }

    /**
     * @param $httpcode
     * @param null $headers
     * @param null $body
     */
    protected function addMockResponse($httpcode, $headers = null, $body = null)
    {
        $this->guzzleMockHandler->append(new Response($httpcode, $headers, $body));
    }

    /**
     * Test that only Freshdesk subdomains work
     */
    public function testOnlyFreshDeskDomainsWork()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->setFreshdeskDomain('http://something.invalid.com');
    }

    /**
     * Test submitting a ticket
     */
    public function testSubmitTicket()
    {
        // Our sample data
        $sampleDescription = "Sample description";
        $sampleSubject = "Sample subject";
        $sampleEmail = "sample@email.com";
        $samplePriority = 3;
        $sampleStatus = 4;

        // Mock a response from the response factory
        $mockResponse = $this->getMockBuilder('Silktide\FreshdeskApi\Response')->disableOriginalConstructor()->getMock();
        $this->responseFactoryMock->expects($this->atLeastOnce())->method('generateResponse')->willReturn($mockResponse);

        // Mock a guzzle response and add it to the queue
        $this->addMockResponse(
            200,
            ['Content-Type' => 'application/json'],
            '{"helpdesk_ticket" : {}}'
        );


        // Submit ticket
        $response = $this->client->submitTicket($sampleDescription, $sampleSubject, $sampleEmail, $samplePriority, $sampleStatus);

        // Expected structure of request to Freshdesk
        $expectedRequestStructure = [
            'helpdesk_ticket' => [
                "description" => $sampleDescription,
                "subject" => $sampleSubject,
                "email" => $sampleEmail,
                "priority" => $samplePriority,
                "status" => $sampleStatus
            ]
        ];

        // Get the last request made and check it conforms
        $lastRequest = $this->guzzleMockHandler->getLastRequest();
        $this->assertEquals($this->domain.'/helpdesk/tickets.json', $lastRequest->getUri(), 'API URL called was incorrect');
        $this->assertEquals('POST', $lastRequest->getMethod(), 'Call should have been POST');
        $this->assertArrayMatches($expectedRequestStructure, json_decode($lastRequest->getBody()->__toString(), true), 'request body JSON');
        $this->assertSame($mockResponse, $response, 'Expected response object to be passed back');
    }



}
<?php

namespace Silktide\FreshdeskApi\Test;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use mef\StringInterpolation\PlaceholderInterpolator;
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
     * @var PlaceholderInterpolator | PHPUnit_Framework_MockObject_MockObject
     */
    protected $stringInterpolatorMock;

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
        $this->stringInterpolatorMock = $this->getMockBuilder('mef\StringInterpolation\PlaceholderInterpolator')->disableOriginalConstructor()->getMock();
        $this->client = new Client($this->guzzle, $this->responseFactoryMock, $this->stringInterpolatorMock, $this->domain, $this->username, $this->password);
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
    public function testAddTicket()
    {
        // Mock a response from the response factory
        $mockResponse = $this->getMockBuilder('Silktide\FreshdeskApi\Response')->disableOriginalConstructor()->getMock();
        $this->responseFactoryMock->expects($this->atLeastOnce())->method('generateResponse')->willReturn($mockResponse);
        $this->stringInterpolatorMock->expects($this->atLeastOnce())->method('getInterpolatedString')->willReturn('/helpdesk/tickets.json');

        // Mock a guzzle response and add it to the queue
        $this->addMockResponse(
            200,
            ['Content-Type' => 'application/json'],
            '{"helpdesk_ticket" : {}}'
        );

        // Ticket properties
        $properties = [
            "description" => "Sample description",
            "subject" => "Sample subject",
            "email" => "sample@email.com",
            "priority" => 3,
            "status" => 4
        ];

        // Submit ticket
        $response = $this->client->addTicket($properties);

        // Expected structure of request to Freshdesk
        $expectedRequestStructure = [
            'helpdesk_ticket' => $properties
        ];

        // Get the last request made and check it conforms
        $lastRequest = $this->guzzleMockHandler->getLastRequest();
        $this->assertEquals($this->domain.'/helpdesk/tickets.json', $lastRequest->getUri()->__toString(), 'API URL called was incorrect');
        $this->assertEquals('POST', $lastRequest->getMethod(), 'Call should have been POST');
        $this->assertArrayMatches($expectedRequestStructure, json_decode($lastRequest->getBody()->__toString(), true), 'request body JSON');
        $this->assertSame($mockResponse, $response, 'Expected response object to be passed back');
    }

    /**
     * Test getting a contact
     */
    public function testGetContact()
    {
        // Test properties
        $id = 100;
        $path = '/helpdesk/contacts/'.$id.'.json';

        // Mock a response from the response factory
        $mockResponse = $this->getMockBuilder('Silktide\FreshdeskApi\Response')->disableOriginalConstructor()->getMock();
        $this->responseFactoryMock->expects($this->atLeastOnce())->method('generateResponse')->willReturn($mockResponse);
        $this->stringInterpolatorMock->expects($this->atLeastOnce())->method('getInterpolatedString')->willReturn($path);

        // Mock a guzzle response and add it to the queue
        $this->addMockResponse(
            200,
            ['Content-Type' => 'application/json'],
            '{"user" : {}}'
        );

        // Submit ticket
        $response = $this->client->getContact($id);

        // Get the last request made and check it conforms
        $lastRequest = $this->guzzleMockHandler->getLastRequest();
        $this->assertEquals($this->domain.$path, $lastRequest->getUri(), 'API URL called was incorrect');
        $this->assertEquals('GET', $lastRequest->getMethod(), 'Call should have been GET');
        $this->assertSame($mockResponse, $response, 'Expected response object to be passed back');
    }

    /**
     * Test getting a contact
     */
    public function testAddContact()
    {
        // Mock a response from the response factory
        $mockResponse = $this->getMockBuilder('Silktide\FreshdeskApi\Response')->disableOriginalConstructor()->getMock();
        $this->responseFactoryMock->expects($this->atLeastOnce())->method('generateResponse')->willReturn($mockResponse);
        $this->stringInterpolatorMock->expects($this->atLeastOnce())->method('getInterpolatedString')->willReturn('/helpdesk/contacts.json');

        // Mock a guzzle response and add it to the queue
        $this->addMockResponse(
            200,
            ['Content-Type' => 'application/json'],
            '{"user" : {}}'
        );

        // Ticket properties
        $properties = [
            "name" => "Example user",
            "email" => "sample@email.com"
        ];

        // Submit ticket
        $response = $this->client->addContact($properties);

        // Expected structure of request to Freshdesk
        $expectedRequestStructure = [
            'user' => $properties
        ];

        // Get the last request made and check it conforms
        $lastRequest = $this->guzzleMockHandler->getLastRequest();
        $this->assertEquals($this->domain.'/helpdesk/contacts.json', $lastRequest->getUri()->__toString(), 'API URL called was incorrect');
        $this->assertEquals('POST', $lastRequest->getMethod(), 'Call should have been POST');
        $this->assertArrayMatches($expectedRequestStructure, json_decode($lastRequest->getBody()->__toString(), true), 'request body JSON');
        $this->assertSame($mockResponse, $response, 'Expected response object to be passed back');
    }

}
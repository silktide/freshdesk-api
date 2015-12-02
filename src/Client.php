<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\FreshdeskApi;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use InvalidArgumentException;
use mef\StringInterpolation\PlaceholderInterpolator;

class Client
{
    /**
     * @var string
     */
    protected $usernameOrToken;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var PlaceholderInterpolator
     */
    protected $interpolator;

    /**
     * @var string
     */
    protected $freshdeskDomain;

    /**
     * @var Guzzle
     */
    protected $guzzle;

    /**
     * @var string[]
     */
    protected $endpoints = [
        'tickets' => '/helpdesk/tickets.json',
        'contact' => '/helpdesk/contacts/{user_id}.json',
        'contacts' => '/helpdesk/contacts.json'
    ];


    /**
     * Construct class.  Password is not required if using API token.
     *
     * @param Guzzle $guzzle
     * @param ResponseFactory $responseFactory
     * @param PlaceholderInterpolator $interpolator
     * @param string $freshdeskDomain
     * @param string $usernameOrToken
     * @param string $password
     */
    public function __construct(
        Guzzle $guzzle,
        ResponseFactory $responseFactory,
        PlaceholderInterpolator $interpolator,
        $freshdeskDomain,
        $usernameOrToken,
        $password = "X"
    ) {
        $this->guzzle = $guzzle;
        $this->responseFactory = $responseFactory;
        $this->interpolator = $interpolator;
        $this->setFreshdeskDomain($freshdeskDomain);
        $this->setUsernameOrToken($usernameOrToken);
        $this->setPassword($password);
    }

    /**
     * Set Freshdesk username or API token.
     *
     * @param string $usernameOrToken
     */
    public function setUsernameOrToken($usernameOrToken)
    {
        $this->usernameOrToken = $usernameOrToken;
    }

    /**
     * Set Freshdesk password.  Not required if using API key.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Set the Freshdesk domain, e.g. http://mydomain.freshdesk.com
     *
     * @param string $freshdeskDomain
     */
    public function setFreshdeskDomain($freshdeskDomain)
    {
        $this->validateDomain($freshdeskDomain);
        $this->freshdeskDomain = $freshdeskDomain;
    }

    /**
     * Validate Freshedesk domain
     *
     * @param string $freshdeskDomain
     * @throws InvalidArgumentException
     */
    protected function validateDomain($freshdeskDomain)
    {
        if (!preg_match('#^https?://.*\.freshdesk\.com$#', $freshdeskDomain)) {
            throw new InvalidArgumentException(
                "The domain [{$freshdeskDomain}] was not valid.  ".
                "Expected to be in the format [http://mydomain.freshdesk.com]"
            );
        }
    }

    /**
     * Submit a Freshdesk ticket
     *
     * @param $properties
     * @return Response
     */
    public function addTicket($properties)
    {
        $content = [
            'helpdesk_ticket' => $properties
        ];
        return $this->makeRequest('POST', $this->buildUrl('tickets'), $content);
    }

    /**
     * Get a contact
     *
     * @param string $id
     * @return array
     */
    public function getContact($id)
    {
        return $this->makeRequest('GET', $this->buildUrl('contact', ['user_id' => $id]));
    }

    /**
     * Add a new contact
     *
     * @param $properties
     * @return Response
     */
    public function addContact($properties)
    {
        $content = [
            'user' => $properties
        ];
        return $this->makeRequest('POST', $this->buildUrl('contacts'), $content);
    }

    /**
     * Work out a URL
     *
     * @param $endpoint
     * @param array $props
     * @return string
     */
    public function buildUrl($endpoint, $props = [])
    {
        $endpointUri = $this->endpoints[$endpoint];

        $path = $this->interpolator->getInterpolatedString($endpointUri, $props);
        return $this->freshdeskDomain.$path;
    }


    /**
     * Make a request to the API
     *
     * @param string $method
     * @param string $url
     * @param array $content
     * @return array
     */
    protected function makeRequest($method, $url, $content = null)
    {
        $props = [
            'auth' => [$this->usernameOrToken, $this->password]
        ];

        if (isset($content)) {
            $props['json'] = $content;
        }

        $response = $this->guzzle->request($method, $url, $props);

        return $this->createResponse($response);
    }

    /**
     * Create our response object from the Guzzle response
     *
     * @param GuzzleResponse $response
     * @return Response
     */
    protected function createResponse(GuzzleResponse $response)
    {
        return $this->responseFactory->generateResponse($response->getStatusCode(), $response->getBody()->__toString());
    }
}

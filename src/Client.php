<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\FreshdeskApi;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use InvalidArgumentException;

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
        'tickets' => '/helpdesk/tickets.json'
    ];


    /**
     * Construct class.  Password is not required if using API token.
     *
     * @param Guzzle $guzzle
     * @param ResponseFactory $responseFactory
     * @param string $freshdeskDomain
     * @param string $usernameOrToken
     * @param string $password
     */
    public function __construct($guzzle, $responseFactory, $freshdeskDomain, $usernameOrToken, $password = "X")
    {
        $this->guzzle = $guzzle;
        $this->responseFactory = $responseFactory;
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
     * @param string $description
     * @param string $subject
     * @param string $email
     * @param int $priority
     * @param int $status
     * @return Response
     */
    public function submitTicket($description, $subject, $email,
        $priority = Constant::PRIORITY_LOW,
        $status = Constant::STATUS_OPEN
    )
    {
        $content = [
            'helpdesk_ticket' => [
                "description" => $description,
                "subject" => $subject,
                "email" => $email,
                "priority" => $priority,
                "status" => $status
            ]
        ];
        return $this->makeRequest('POST', 'tickets', $content);
    }

    /**
     * Make a request to the API
     *
     * @param string $method
     * @param string $endpoint
     * @param array $content
     * @return array
     */
    protected function makeRequest($method, $endpoint, $content = null)
    {
        $url = $this->freshdeskDomain.$this->endpoints[$endpoint];
        $response = $this->guzzle->request(
            $method,
            $url,
            [
                'json' => $content,
                'auth' => [$this->usernameOrToken, $this->password]
            ]
        );

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

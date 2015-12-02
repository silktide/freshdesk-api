<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\FreshdeskApi;

use GuzzleHttp\Client as Guzzle;
use mef\Stringifier\Stringifier;
use mef\StringInterpolation\PlaceholderInterpolator;

abstract class ClientFactory
{
    /**
     * Create an instance of Client with default dependencies
     * automatically created (for use by people without DI)
     *
     * @param string $freshdeskDomain
     * @param string $usernameOrToken
     * @param string $password
     * @return Client
     */
    public static function create($freshdeskDomain, $usernameOrToken, $password = "X")
    {
        $guzzle = new Guzzle();
        $responseFactory = new ResponseFactory();
        $interpolator = new PlaceholderInterpolator(new Stringifier());
        return new Client($guzzle, $responseFactory, $interpolator, $freshdeskDomain, $usernameOrToken, $password);
    }
}

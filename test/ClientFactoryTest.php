<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\FreshdeskApi\Test;

use Silktide\FreshdeskApi\ClientFactory;

class ClientFactoryTest extends BaseTest
{
    public function testCreateResponse()
    {
        $client = ClientFactory::create('http://domain.freshdesk.com', 'username', 'password');
        $this->assertInstanceOf('Silktide\FreshdeskApi\Client', $client, 'Object of type Client was not returned from ClientFactory');
    }
}
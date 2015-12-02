<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\FreshdeskApi\Test;

use Silktide\FreshdeskApi\ResponseFactory;

class ResponseFactoryTest extends BaseTest
{
    public function testCreateResponse()
    {
        $data = [
            'some' => 'data'
        ];
        $factory = new ResponseFactory();
        $response = $factory->generateResponse('200', json_encode($data));

        $this->assertInstanceOf('Silktide\FreshdeskApi\Response', $response, 'Object of type Response was not returned from ResponseFactory');
    }
}
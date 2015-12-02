<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\FreshdeskApi\Test;

use Silktide\FreshdeskApi\Response;

class ResponseTest extends BaseTest
{
    public function testWasSuccess()
    {
        $response = new Response('200', json_encode([]));
        $this->assertTrue($response->wasSuccess(), "Response with 200 code should be success.");

        $response = new Response('404', json_encode([]));
        $this->assertFalse($response->wasSuccess(), "Response with 404 code should not be success.");

        $response = new Response('500', json_encode([]));
        $this->assertFalse($response->wasSuccess(), "Response with 500 code should not be success.");
    }

    public function testGetStatusCode()
    {
        $response = new Response('200', json_encode([]));
        $this->assertEquals('200', $response->getStatusCode(), "Response should return same status code as provided");
    }

    public function testGetData()
    {
        $data = [
            'foo' => 'bar',
            'baz' => 'bee',
            'doo' => [
                'bop' => 'wah'
            ]
        ];

        $response = new Response('200', json_encode($data));

        $this->assertArrayMatches($data, $response->getData(), 'data response JSON');
    }
}
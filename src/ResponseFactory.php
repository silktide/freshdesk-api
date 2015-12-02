<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\FreshdeskApi;

class ResponseFactory
{
    /**
     * Generate a response object
     *
     * @param $code
     * @param $data
     * @return Response
     */
    public function generateResponse($code, $data)
    {
        return new Response($code, $data);
    }
}
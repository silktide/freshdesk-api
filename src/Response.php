<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\FreshdeskApi;


class Response
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $data;

    /**
     * @param string $code
     * @param string $data
     */
    public function __construct($code, $data)
    {
        $this->code = $code;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->code;
    }

    /**
     * @return bool
     */
    public function wasSuccess()
    {
        return $this->code === '200';
    }

    /**
     * @return array
     */
    public function getData()
    {
        return json_decode($this->data, true);
    }

}
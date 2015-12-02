<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\FreshdeskApi\Test;

use PHPUnit_Framework_TestCase;

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * Assert that an array structure matches
     *
     * @param array $expected
     * @param array $actual
     * @param string $name
     * @param array $parents
     */
    protected function assertArrayMatches($expected, $actual, $name = '', $parents = [])
    {
        foreach ($expected as $key => $item) {
            $levels = array_merge($parents, [$key]);
            $levelsString = implode(" => ", $levels);

            $this->assertArrayHasKey($key, $actual, "Item missing from {$name}: ".$levelsString);

            if (is_array($item)) {
                $this->assertArrayMatches($expected[$key], $actual[$key], $name, $levels);
            } else {
                $this->assertEquals($item, $actual[$key], "Item has wrong value in {$name}: ".$levelsString);
            }
        }
    }
}
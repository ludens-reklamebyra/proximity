<?php

namespace Proximity\Tools;

class DistancesTest extends \PHPUnit_Framework_TestCase {
    public function testCalculate() {
        $this->assertEquals(
            10548.050763187448,
            Distances::calculate(2, 100, 50, 200, 'K')
        );
    }
}

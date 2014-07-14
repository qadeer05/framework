<?php

namespace Pagekit\Component\Filter\Tests;

use Pagekit\Component\Filter\Boolean;

class BooleanTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new Boolean;

        $values = [
            0   => false,
            ""  => false,
            "1" => true

        ];
        foreach ($values as $in => $out) {
            $this->assertSame($filter->filter($in), $out);
        }

    }

}

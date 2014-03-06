<?php

namespace Pagekit\Component\Filter\Tests;

use Pagekit\Component\Filter\Digits;

class DigitsTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new Digits;

        $values = array(
            /* here are the ones the filter should not change */
            "123"   => "123",
            /* now the ones the filter has to fix */
            "abc"   => "", 
            "äöü"   => "", // unicode support please
            "?"     => "",
            "     " => "",
            "!§$%&/()="   => "",
            "abc123!?) abc" => "123"
        );
        foreach ($values as $in => $out) {
            $this->assertEquals($filter->filter($in), $out);
        }

    }

}

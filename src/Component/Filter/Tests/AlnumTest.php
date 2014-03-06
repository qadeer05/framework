<?php

namespace Pagekit\Component\Filter\Tests;

use Pagekit\Component\Filter\Alnum;

class AlnumTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new Alnum;

        $values = array(
            /* here are the ones the filter should not change */
            "abc"   => "abc", 
            "123"   => "123",
            "äöü"   => "äöü", // unicode support please
            /* now the ones the filter has to fix */
            "?"     => "",
            "abc!"  => "abc", 
            "     " => "",
            "!§$%&/()="   => "",
            "abc123!?) abc" => "abc123abc"
        );
        foreach ($values as $in => $out) {
            $this->assertEquals($filter->filter($in), $out);
        }

    }

}

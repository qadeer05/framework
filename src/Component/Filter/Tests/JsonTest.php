<?php

namespace Pagekit\Component\Filter\Tests;

use Pagekit\Component\Filter\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new Json;

        $values = array(
            '"23"'              => "23",
            '{"foo": "bar"}'    => array("foo" => "bar"),
            '{"foo": "23"}'     => array("foo" => "23"),
            '"äöü"'   => "äöü" // unicode support please
        );
        foreach ($values as $in => $out) {
            $this->assertSame($filter->filter($in), $out);
        }

    }

}

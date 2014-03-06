<?php

namespace Pagekit\Component\Filter\Tests;

use Pagekit\Component\Filter\String;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new String;

        $values = array(
            23                  => "23",
            "23"                => "23",
            '"23"'              => '"23"',
            '{"foo": "23"}'     => '{"foo": "23"}',
            "whateverthisis"    => "whateverthisis",
            "!'#+*§$%&/()=?"    => "!'#+*§$%&/()=?",
            'äöü'               => "äöü" // unicode support please
        );
        foreach ($values as $in => $out) {
            $this->assertSame($filter->filter($in), $out);
        }

    }

}

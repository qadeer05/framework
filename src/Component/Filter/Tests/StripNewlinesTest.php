<?php

namespace Pagekit\Component\Filter\Tests;

class StripNewlinesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideNewLineStrings
     */
    public function testFilter($input, $output)
    {
        $filter = new \Pagekit\Component\Filter\StripNewlines;

        $this->assertEquals($output, $filter->filter($input));
    }

    /**
     * @return array
     */
    public function provideNewLineStrings()
    {
        return array(
            array('', ''),
            array("\n", ''),
            array("\r", ''),
            array("\r\n", ''),
            array('\n', '\n'),
            array('\r', '\r'),
            array('\r\n', '\r\n'),
            array("These newlines should\nbe removed by\r\nthe filter", 'These newlines shouldbe removed bythe filter')
        );
    }
}

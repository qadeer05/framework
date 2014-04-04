<?php

namespace Pagekit\Component\Filter\Tests;

use Pagekit\Component\Filter\PregReplace;

class PregReplaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PregReplace
     **/
    protected $filter;

    public function setUp()
    {
        $this->filter = new PregReplace;
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRuntimeException()
    {
        $this->filter->filter('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testModifierE()
    {
        $this->filter->setPattern('/foo/e');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPatternArgument()
    {
        $this->filter->setPattern(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReplacementArgument()
    {
        $this->filter->setReplacement(null);
    }

    /**
     * @dataProvider provider
     */
    public function testFilter($pattern, $replacement, $in, $out)
    {
        $this->filter->setPattern($pattern);
        $this->assertSame($this->filter->getPattern(), $pattern);

        $this->filter->setReplacement($replacement);
        $this->assertSame($this->filter->getReplacement(), $replacement);

        $this->assertSame($this->filter->filter($in), $out);
    }

    public function provider()
    {
        return array(
            array('/foo/i', '', 'Foobar', 'bar'),
            array(array('/foo/', '/bar/'), array('FOO', 'BAR'), 'foobar', 'FOOBAR')
        );
    }

}

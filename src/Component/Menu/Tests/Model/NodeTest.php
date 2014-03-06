<?php

namespace Pagekit\Component\Menu\Model\Tests;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->node = $this->getMockForAbstractClass('Pagekit\Component\Menu\Model\Node');
        $this->item = $this->getMockForAbstractClass('Pagekit\Component\Menu\Entity\Item');
    }

    public function testSetGetItem()
    {
        $this->node->setItem($this->item);
        $this->assertEquals($this->item, $this->node->getItem());
    }

    public function testAttributeUrl()
    {
        $this->item->set('foo', 'bar');
        $this->item->setUrl('http://foobar.com');
        $this->node->setItem($this->item);
        $this->assertEquals('http://foobar.com', $this->node->getUrl());
        $this->assertEquals('bar', $this->node->getAttribute('foo'));
        $this->assertEquals((string) $this->item, (string) $this->node);

    }

}
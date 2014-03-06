<?php

namespace Pagekit\Component\Mail\Tests;

use Pagekit\Component\Mail\Mailer;
use Pagekit\Component\Mail\Message;
use Swift_ByteStream_FileByteStream;
use Swift_Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{

    protected $swift;
    protected $queue;
    protected $mailer;
    protected $swift_message;
    protected $message;

    public function setUp()
    {
    	$this->swift = $this->getMockBuilder('Swift_Mailer')->disableOriginalConstructor()->getMock();
        $this->queue = $this->getMockBuilder('Swift_SpoolTransport')->disableOriginalConstructor()->getMock();
        $this->mailer = new Mailer($this->swift, $this->queue);
        $this->swift_message = new Swift_Message;
    	$this->message = new Message($this->mailer, $this->swift_message);
    }

    public function testSubject()
    {
    	$this->assertEquals('', $this->message->getSubject());
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->subject('some subject'));
    	$this->assertEquals('some subject', $this->message->getSubject());
    }

    public function testDate()
    {
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->date(20131212));
    	$this->assertEquals(20131212, $this->message->getDate());
    }

    public function testBody()
    {
    	$this->assertEquals('', $this->message->getBody());
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->body('some subject'));
    	$this->assertEquals('some subject', $this->message->getBody());
    }

    public function testReturnPath()
    {
    	$this->assertEquals('', $this->message->getReturnPath());
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->returnPath('test@mail.com'));
    	$this->assertEquals('test@mail.com', $this->message->getReturnPath());
    }

    public function testSender()
    {
    	$this->assertEquals('', $this->message->getSender());
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->sender('test@mail.com', 'test'));
    	$this->assertEquals(array('test@mail.com' => 'test'), $this->message->getSender());
    }

    public function testFrom()
    {
    	$this->assertEquals(array(), $this->message->getFrom());
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->from('test@mail.com', 'test'));
    	$this->assertEquals(array('test@mail.com' => 'test'), $this->message->getFrom());
    }

    public function testReplyTo()
    {
    	$this->assertEquals('', $this->message->getReplyTo());
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->replyTo('test@mail.com', 'test'));
    	$this->assertEquals(array('test@mail.com' => 'test'), $this->message->getReplyTo());
    }

    public function testTo()
    {
    	$this->assertEquals('', $this->message->getTo());
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->to('test@mail.com', 'test'));
    	$this->assertEquals(array('test@mail.com' => 'test'), $this->message->getTo());
    }

     public function testCc()
    {
    	$this->assertEquals('', $this->message->getCc());
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->cc('test@mail.com', 'test'));
    	$this->assertEquals(array('test@mail.com' => 'test'), $this->message->getCc());
    }

    public function testBcc()
    {
    	$this->assertEquals('', $this->message->getBcc());
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->bcc('test@mail.com', 'test'));
    	$this->assertEquals(array('test@mail.com' => 'test'), $this->message->getBcc());
    }

    public function testAttach()
    {
    	$this->assertEquals(0, count($this->swift_message->getChildren()));
    	$this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->message->attach('./Fixtures/foo.txt', 'Foo', 'text/plain'));
    	$this->assertEquals(1, count($this->swift_message->getChildren()));

    	$this->message->attachData('some plain text', 'Bar', 'text/plain');
    	$this->assertEquals(2, count($this->swift_message->getChildren()));
    }

    public function testEmbed()
    {
    	$this->assertRegExp('/cid:[0-9a-z]*@swift.generated/', $this->message->embed('./Fixtures/image.gif'));
    	$this->assertEquals(1, count($this->swift_message->getChildren()));

    	$this->message->embedData(new Swift_ByteStream_FileByteStream('./Fixtures/image.gif'), 'Image', 'image/gif');
    	$this->assertEquals(2, count($this->swift_message->getChildren()));
    }
}

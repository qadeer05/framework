<?php

namespace Pagekit\Component\Mail\Tests;

use Pagekit\Component\Mail\Mailer;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->swift = $this->getMockBuilder('Swift_Mailer')->disableOriginalConstructor()->getMock();
        $this->queue = $this->getMockBuilder('Swift_SpoolTransport')->disableOriginalConstructor()->getMock();
        $this->mailer = new Mailer($this->swift, $this->queue);
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Pagekit\Component\Mail\Message', $this->mailer->create());
    }

    public function testSend()
    {
        $message = $this->getMock('Swift_Mime_Message');
        $errors = array();
        $this->swift->expects($this->once())
                    ->method('send')
                    ->with($message, $errors);

        $this->mailer->send($message, $errors);
    }

    public function testQueue()
    {
        $message = $this->getMock('Swift_Mime_Message');
        $errors = array();
        $this->queue->expects($this->once())
                    ->method('send')
                    ->with($message, $errors);

        $this->mailer->queue($message, $errors);
    }
}

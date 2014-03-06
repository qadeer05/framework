<?php

namespace Pagekit\Framework\Event;

use Pagekit\Component\Event\EventSubscriberInterface;
use Pagekit\Framework\ApplicationAware;

abstract class EventSubscriber extends ApplicationAware implements EventSubscriberInterface
{
}

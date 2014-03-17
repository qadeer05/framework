<?php

namespace Pagekit\Framework\Event;

use Pagekit\Framework\ApplicationAware;
use Pagekit\Framework\Event\EventSubscriberInterface;

abstract class EventSubscriber extends ApplicationAware implements EventSubscriberInterface
{
}

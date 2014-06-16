<?php

namespace Pagekit\Component\Routing\Event;

use Pagekit\Component\Routing\Link;
use Symfony\Component\EventDispatcher\Event;

class GenerateRouteEvent extends Event
{
    protected $url;
    protected $link;
    protected $referenceType;

    /**
     * Constructor.
     *
     * @param Link $link
     * @param bool $referenceType
     */
    public function __construct(Link $link, $referenceType = false)
    {
        $this->link = $link;
        $this->referenceType = $referenceType;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        $this->stopPropagation();
    }

    /**
     * @param Link $link
     */
    public function setLink ($link)
    {
        $this->link = $link;
    }

    /**
     * @return Link
     */
    public function getLink ()
    {
        return $this->link;
    }

    /**
     * @return bool|mixed
     */
    public function getReferenceType()
    {
        return $this->referenceType;
    }

    /**
     * @param bool|mixed $referenceType
     */
    public function setReferenceType($referenceType)
    {
        $this->referenceType = $referenceType;
    }
}

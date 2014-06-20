<?php

namespace Pagekit\Razr\Extension;

use Pagekit\Razr\Engine;

interface ExtensionInterface
{
    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Initializes the extension.
     */
    public function initialize(Engine $engine);
}

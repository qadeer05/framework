<?php

namespace Pagekit\Component\Menu\Filter;

use FilterIterator as BaseFilterIterator;

abstract class FilterIterator extends BaseFilterIterator
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param \Iterator $iterator
     * @param array     $options
     */
    public function __construct(\Iterator $iterator, array $options = array())
    {
        parent::__construct($iterator);

        $this->options = $options;
    }
}

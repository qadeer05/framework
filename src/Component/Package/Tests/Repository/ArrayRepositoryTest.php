<?php

namespace Pagekit\Component\Package\Tests\Repository;

use Pagekit\Component\Package\Repository\ArrayRepository;

class ArrayRepositoryTest extends RepositoryTest
{
    protected function getRepository()
    {
        return new ArrayRepository;
    }
}
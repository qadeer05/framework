<?php

namespace Pagekit\Component\Package\Repository;

interface WritableRepositoryInterface extends RepositoryInterface
{
    /**
     * Writes repository.
     */
    public function write();

    /**
     * Forces a reload of all packages.
     */
    public function reload();
}
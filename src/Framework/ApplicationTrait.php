<?php

namespace Pagekit\Framework;

trait ApplicationTrait
{
    /**
     * @var Application
     */
    protected static $app;

    /**
     * Gets the application.
     *
     * @return Application
     */
    public static function getApplication()
    {
        return self::$app;
    }

    /**
     * Sets the application.
     *
     * @param Application $app
     */
    public static function setApplication(Application $app)
    {
        self::$app = $app;
    }

    /**
     * Gets an application parameter or an object.
     *
     * @param  string $id
     * @return mixed
     */
    public function __invoke($id)
    {
        return self::$app[$id];
    }
}

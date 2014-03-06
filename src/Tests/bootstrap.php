<?php

$loader = require __DIR__.'/../../../../autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
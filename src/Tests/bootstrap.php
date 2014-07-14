<?php

$loader = require __DIR__.'/../../../../autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);
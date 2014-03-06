<?php

namespace Pagekit\Component\Routing\Request;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;

class ParamReader implements ParamReaderInterface
{
    protected $reader;
    protected $annotation;

    /**
     * Constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader = null)
    {
        $this->reader = $reader;
        $this->annotation = 'Pagekit\Component\Routing\Request\Annotation\Request';
    }

     /**
     * {@inheritdoc}
     */
    public function read(\ReflectionMethod $method)
    {
        $params = array();

        if ($annotation = $this->getAnnotationReader()->getMethodAnnotation($method, $this->annotation)) {
            foreach ($annotation->getParameters() as $name => $type) {

                if (is_numeric($name)) {
                    list($name, $type) = array($type, 'string');
                }

                $options = $annotation->getOptions($name);
                $params[] = compact('name', 'type', 'options');
            }
        }

        return $params;
    }

    /**
     * Get the annotation reader instance.
     *
     * @return Reader
     */
    protected function getAnnotationReader()
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader;
            $this->reader->addNamespace('Pagekit\Component\Routing\Request\Annotation');
        }

        return $this->reader;
    }
}

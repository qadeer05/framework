<?php

namespace Pagekit\Component\Routing\Controller;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Component\Routing\Annotation\Method;
use Pagekit\Component\Routing\Annotation\Route as RouteAnnotation;
use Pagekit\Component\Routing\Event\ConfigureRouteEvent;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ControllerReader implements ControllerReaderInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var int
     */
    protected $routeIndex;

    /**
     * @var string
     */
    protected $routeAnnotation = 'Pagekit\Component\Routing\Annotation\Route';

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param Reader                   $reader
     */
    public function __construct(EventDispatcherInterface $events, Reader $reader = null)
    {
        $this->events = $events;
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function read(ReflectionClass $class, array $options = array())
    {
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class));
        }

        $options = array_replace(array(
            'path'         => '',
            'name'         => '',
            'requirements' => array(),
            'options'      => array(),
            'defaults'     => array(),
        ), $options);

        if ($annotation = $this->getAnnotationReader()->getClassAnnotation($class, $this->routeAnnotation)) {

            if ($annotation->getPath() !== null) {
                $options['path'] = $annotation->getPath();
            }

            if ($annotation->getName() !== null) {
                $options['name'] = $annotation->getName();
            }

            if ($annotation->getRequirements() !== null) {
                $options['requirements'] = $annotation->getRequirements();
            }

            if ($annotation->getOptions() !== null) {
                $options['options'] = $annotation->getOptions();
            }

            if ($annotation->getDefaults() !== null) {
                $options['defaults'] = $annotation->getDefaults();
            }
        }

        $this->routes = new RouteCollection;

        foreach ($class->getMethods() as $method) {

            $this->routeIndex = 0;

            if ($method->isPublic() && 'Action' == substr($method->name, -6)) {

                $count = $this->routes->count();

                foreach ($this->getAnnotationReader()->getMethodAnnotations($method) as $annotation) {
                    if ($annotation instanceof $this->routeAnnotation) {
                        $this->addRoute($class, $method, $options, $annotation);
                    }
                }

                if ($count == $this->routes->count()) {
                    $this->addRoute($class, $method, $options);
                }
            }
        }

        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ReflectionClass $class)
    {
        return true;
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
            $this->reader->addNamespace('Pagekit\Component\Routing\Annotation');
        }

        return $this->reader;
    }

    /**
     * Creates a new route.
     *
     * @param ReflectionClass  $class
     * @param ReflectionMethod $method
     * @param array            $options
     * @param RouteAnnotation  $annotation
     */
    protected function addRoute(ReflectionClass $class, ReflectionMethod $method, array $options, $annotation = null)
    {
        if ($annotation) {

            $name = $annotation->getName();
            $path = $annotation->getPath();

            $options['requirements'] = array_merge($options['requirements'], $annotation->getRequirements());
            $options['options'] = array_merge($options['options'], $annotation->getOptions());
            $options['defaults'] = array_merge($options['defaults'], $annotation->getDefaults());
        }

        if (empty($name)) {
            $name = $this->getDefaultRouteName($class, $method, $options);
        }

        if (empty($path)) {
            $path = $this->getDefaultRoutePath($class, $method, $options);
        }

        $route = new Route(rtrim($options['path'].$path, '/'), $options['defaults'], $options['requirements'], $options['options']);

        $this->configureRoute($route, $class, $method, $options);

        $this->routes->add($name, $route);
    }

    /**
     * Configure the route, should be overridden in subclasses.
     *
     * @param Route            $route
     * @param ReflectionClass  $class
     * @param ReflectionMethod $method
     * @param array            $options
     */
    protected function configureRoute(Route $route, ReflectionClass $class, ReflectionMethod $method, array $options)
    {
        $route->setDefault('_controller', $class->name.'::'.$method->name);

        foreach ($this->getAnnotationReader()->getMethodAnnotations($method) as $annotation) {
            if ($annotation instanceof Method) {
                $route->setRequirement('_method', implode('|', $annotation->getMethods()));
            }
        }

        $this->events->dispatch('route.configure', new ConfigureRouteEvent($route, $class, $method, $options));
    }

    /**
     * Gets the default route path for a class method.
     *
     * @param  ReflectionClass  $class
     * @param  ReflectionMethod $method
     * @param  array            $options
     * @return string
     */
    protected function getDefaultRoutePath(ReflectionClass $class, ReflectionMethod $method, array $options)
    {
        extract($this->parseControllerAndActionName($class, $method));

        return strtolower('/'.($action == 'index' ? '' : $action));
    }

    /**
     * Gets the default route name for a class method.
     *
     * @param  ReflectionClass  $class
     * @param  ReflectionMethod $method
     * @param  array            $options
     * @return string
     */
    protected function getDefaultRouteName(ReflectionClass $class, ReflectionMethod $method, array $options)
    {
        extract($this->parseControllerAndActionName($class, $method));

        $name = '@'.$options['name'].'/'.$controller.($action == 'index' ? '' : '/'.$action);

        if ($this->routeIndex > 0) {
            $name .= '_'.$this->routeIndex;
        }

        $this->routeIndex++;

        return strtolower($name);
    }

    /**
     * Parses the controller and action name.
     *
     * @param  ReflectionClass $class
     * @param  ReflectionMethod $method
     * @throws \LogicException
     * @return array
     */
    protected function parseControllerAndActionName(ReflectionClass $class, ReflectionMethod $method)
    {
        if (!preg_match('/([a-zA-Z0-9]+)Controller$/', $class->name, $matches)) {
            throw new \LogicException(sprintf('Unable to retrieve controller name. The controller class %s does not follow the naming convention. (e.g. PostController)', $class->name));
        }

        $controller = $matches[1];

        if (!preg_match('/([a-zA-Z0-9]+)Action$/', $method->name, $matches)) {
            throw new \LogicException(sprintf('Unable to retrieve action name. The controller class method %s does not follow the naming convention. (e.g. indexAction)', $method->name));
        }

        $action = $matches[1];

        return compact('controller', 'action');
    }
}

<?php

namespace Pagekit\Framework\Templating;

use Razr\Environment;
use Razr\Exception\RuntimeException;
use Razr\Template;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class RazrEngine implements EngineInterface
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var TemplateNameParserInterface
     */
    protected $parser;

    /**
     * @param TemplateNameParserInterface $parser
     * @param Environment                 $environment
     */
    public function __construct(TemplateNameParserInterface $parser, Environment $environment)
    {
        $this->environment = $environment;
        $this->parser = $parser;
    }

    /**
     * Gets the environment.
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $parameters = array())
    {
        return $this->load($name)->render($parameters);
    }

    /**
     * {@inheritdoc}
     *
     * It also supports Template as name parameter.
     */
    public function exists($name)
    {
        if ($name instanceof Template) {
            return true;
        }

        try {
            $this->environment->getLoader()->getSource($name);
        } catch (RuntimeException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        if ($name instanceof Template) {
            return true;
        }

        $template = $this->parser->parse($name);

        return 'razr' === $template->get('engine');
    }

    /**
     * Loads a template.
     *
     * @param  string $name
     * @return Template
     */
    protected function load($name)
    {
        if ($name instanceof Template) {
            return $name;
        }

        $template = $this->parser->parse($name);

        return $this->environment->loadTemplate($template->getPath());
    }
}

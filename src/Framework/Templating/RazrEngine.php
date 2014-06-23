<?php

namespace Pagekit\Framework\Templating;

use Pagekit\Razr\Engine;
use Pagekit\Razr\Exception\InvalidArgumentException;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class RazrEngine extends Engine implements EngineInterface
{
    /**
     * @var TemplateNameParserInterface
     */
    protected $nameParser;

    /**
     * @param TemplateNameParserInterface $parser
     * @param Environment                 $environment
     */
    public function __construct(TemplateNameParserInterface $nameParser, $cachePath = null)
    {
        parent::__construct($cachePath);

        $this->nameParser = $nameParser;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        try {
            $this->load($name);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        $template = $this->nameParser->parse($name);

        return 'razr' === $template->get('engine');
    }

    /**
     * {@inheritdoc}
     */
    protected function load($name)
    {
        $template = $this->nameParser->parse($name);

        if (!file_exists($path = $template->getPath())) {
            throw new InvalidArgumentException(sprintf('The template "%s" does not exist.', $name));
        }

        return parent::load($path);
    }
}

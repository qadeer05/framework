<?php

namespace Pagekit\Framework\Templating;

use Pagekit\Razr\Engine;
use Pagekit\Razr\Exception\InvalidArgumentException;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class RazrEngine2 extends Engine implements EngineInterface
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

        return 'razr2' === $template->get('engine');
    }

    /**
     * {@inheritdoc}
     */
    protected function load($name)
    {
        $template = $this->nameParser->parse($name);

        return parent::load($template->getPath());
    }
}

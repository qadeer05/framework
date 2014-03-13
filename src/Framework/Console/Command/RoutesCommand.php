<?php

namespace Pagekit\Framework\Console\Command;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RoutesCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered routes';

    /**
     * Execute the console command.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $routes = $this->pagekit['router']->getRoutes();

        if (count($routes) == 0) {
            return $this->error("Your application doesn't have any routes.");
        }

        $rows = array();
        foreach ($routes as $name => $route) {
            $rows[$name] = array(
                'name' => $name,
                'uri' => $route->getPath(),
                'action' => is_string($ctrl = $route->getDefault('_controller')) ? $ctrl : 'Closure'
            );

            if ($this->option('verbose')) {
                $rows[$name]['access'] = json_encode($route->getOption('access'));
                $rows[$name]['csrf'] = $route->getOption('_csrf_name') ? '1' : '';
            }
        }

        $headers = array('Name', 'URI', 'Action');
        if ($this->option('verbose')) {
            $headers[] = 'Access';
            $headers[] = 'Csrf';
        }

        $table = $this->getHelperSet()->get('table');
        $table->setHeaders($headers);
        $table->setRows($rows)->render($this->output);
    }
}

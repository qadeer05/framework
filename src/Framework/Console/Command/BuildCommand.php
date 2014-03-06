<?php

namespace Pagekit\Framework\Console\Command;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class BuildCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds a Pagekit release';

    /**
     * Builds a .zip release file.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $vers = $this->getApplication()->getVersion();
        $path = $this->pagekit['path'];

        $zip = new \ZipArchive;
        $zip->open($zipFile = "$path/pagekit-$vers.zip", \ZipArchive::OVERWRITE);

        $finder = Finder::create()
            ->files()
            ->in($path)
            ->ignoreVCS(true)
            ->filter(function ($file) {

                $exclude = array(
                    '^(app\/cache|app\/logs|app\/sessions|app\/temp|storage|config\.php|pagekit.+\.zip)',
                    '^extensions\/(?!(installer|page|system)\/).*',
                    '^extensions\/.+\/languages\/.+\.(po|pot)',
                    '^framework\/.+\/(tests\/|phpunit\.xml)',
                    '^vendor\/doctrine\/(annotations|cache|collections|common|dbal|inflector|lexer)\/(bin|docs|tests|build|phpunit|run|upgrade)',
                    '^vendor\/erusev\/parsedown\/(tests\/|phpunit\.xml)',
                    '^vendor\/ircmaxell\/.+\/(test|phpunit|version-test)',
                    '^vendor\/nikic\/php-parser\/(doc|grammar|test|phpunit)',
                    '^vendor\/pagekit\/.+\/(tests\/|phpunit\.xml)',
                    '^vendor\/pimple\/pimple\/(tests|phpunit)',
                    '^vendor\/psr\/.+\/(test\/|phpunit\.xml)',
                    '^vendor\/swiftmailer\/swiftmailer\/(doc|notes|tests|test-suite|build)',
                    '^vendor\/symfony\/.+\/(tests\/|phpunit\.xml)',
                    '\/node_modules'
                );

                return !preg_match('/' . implode('|', $exclude) . '/i', $file->getRelativePathname());
            });

        foreach ($finder as $file) {
            $zip->addFile($file->getPathname(), $file->getRelativePathname());
        }

        $zip->addEmptyDir('app/cache');
        $zip->addEmptyDir('app/logs');
        $zip->addEmptyDir('app/sessions');
        $zip->addEmptyDir('app/temp');
        $zip->addEmptyDir('storage');
        $zip->addFile($path . '/.htaccess', '.htaccess');

        $zip->close();

        $name = basename($zipFile);
        $size = filesize($zipFile) / 1024 / 1024;

        $this->line(sprintf('Building: %s (%.2f MB)', $name, $size));
    }
}

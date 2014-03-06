<?php

namespace Pagekit\Component\Cache;

use Doctrine\Common\Cache\FilesystemCache as BaseFilesystemCache;

class FilesystemCache extends BaseFilesystemCache
{
    /**
     * {@inheritdoc}
     */
    protected $extension = '.cache';

    /**
     * {@inheritdoc}
     */
    protected function getFilename($id)
    {
        $hash = sha1($id);
        $path = implode(array_slice(str_split($hash, 2), 0, 2), DIRECTORY_SEPARATOR);
        $path = $this->directory . DIRECTORY_SEPARATOR . $path;

        return $path . DIRECTORY_SEPARATOR . $hash . $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        $file = $this->getFilename($id);

        if ($unlink = @unlink($file)) {
            $this->deleteEmptyDirectory(dirname($file));
        }

        return $unlink;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        $dirs = array();

        foreach ($this->getFileIterator() as $name => $file) {
            @unlink($name);
            $dirs[] = dirname($name);
        }

        foreach (array_unique($dirs) as $dir) {
            $this->deleteEmptyDirectory($dir);
        }

        return true;
    }

    /**
     * @return \Iterator
     */
    protected function getFileIterator()
    {
        $pattern = '/^.+\\' . $this->extension . '$/i';
        $iterator = new \RecursiveDirectoryIterator($this->directory);
        $iterator = new \RecursiveIteratorIterator($iterator);
        return new \RegexIterator($iterator, $pattern);
    }

    /**
     * @param string
     */
    protected function deleteEmptyDirectory($dir)
    {
        if (strpos($dir, $this->directory) !== 0 || !is_readable($dir) || !$scan = scandir($dir) or count($scan) != 2) {
            return;
        }

        if (@rmdir($dir) && $this->directory != dirname($dir)) {
            $this->deleteEmptyDirectory(dirname($dir));
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 08.06.16
 * Time: 21:34
 */

namespace AppBundle\Service;


class FileFinder implements FileFinderInterface
{
    /**
     * @var bool
     */
    private $isFile = false;

    /**
     * @var bool
     */
    private $isDir = false;

    /**
     * @var array
     */
    private $paths = [];

    /**
     * @var array
     */
    private $regularExpressions = [];

    /**
     * @var array
     */
    private $result = [];

    /**
     * Find only files
     *
     * @return FileFinder
     */
    public function isFile()
    {
        $this->isFile = true;
        $this->isDir = false;

        return $this;
    }

    /**
     * Find only directories
     *
     * @return FileFinder
     */
    public function isDir()
    {
        $this->isDir = true;
        $this->isFile = false;

        return $this;
    }

    /**
     * Search in directory $dir
     * @param string $dir
     * @return FileFinder
     */
    public function inDir($dir)
    {
        $this->paths[] = $dir;

        return $this;
    }

    /**
     * Filter by regular expression on path
     * @param string $regularExpression
     *
     * @return FileFinder
     */
    public function match($regularExpression)
    {
        $this->regularExpressions[] = $regularExpression;

        return $this;
    }

    /**
     * Returns array of all found files/dirs (full path)
     * @return string[]
     *
     * @throws \UnexpectedValueException|\RuntimeException
     */
    public function getList()
    {
        if (!count($this->paths)) {
            throw new \RuntimeException('no dirs were provided');
        }
        foreach ($this->paths as $path) {
            $directory = new \DirectoryIterator($path);
            if ($this->isFile) {
                $this->fillFiles($directory);
            }

            if ($this->isDir) {
                $this->fillDirectories($directory);
            }
        }

        return $this->result;
    }

    /**
     * @param \DirectoryIterator $directory
     */
    private function fillFiles(\DirectoryIterator $directory)
    {
        if (count($this->regularExpressions)) {
            foreach ($this->regularExpressions as $regularExpressions) {
                $regexIterator = new \RegexIterator($directory, $regularExpressions);
                foreach ($regexIterator as $file) {
                    $this->result[] = $file->getPathname();
                }
            }
        } else {
            foreach ($directory as $file) {
                if ($file->isFile()) {
                    $this->result[] = $file->getPathname();
                }
            }
        }
    }

    /**
     * @param \DirectoryIterator $directory
     */
    private function fillDirectories(\DirectoryIterator $directory)
    {
        foreach ($directory as $file) {
            if ($file->isDir() and !$file->isDot()) {
                $this->result[] = $file->getPathname();
            }
        }
    }
}
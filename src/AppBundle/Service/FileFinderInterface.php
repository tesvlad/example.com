<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 08.06.16
 * Time: 21:35
 */

namespace AppBundle\Service;


interface FileFinderInterface {

    /**
     * Find only files
     * @return FileFinder
     */
    public function isFile();


    /**
     * Find only directories
     * @return FileFinder
     */
    public function isDir();


    /**
     * Search in directory $dir
     * @param string $dir
     * @return FileFinder
     */
    public function inDir($dir);


    /**
     * Filter by regular expression on path
     * @param string $regularExpression
     * @return FileFinder
     */
    public function match($regularExpression);


    /**
     * Returns array of all found files/dirs (full path)
     * @return string[]
     */
    public function getList();

}
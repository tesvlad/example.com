<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 07.06.16
 * Time: 20:31
 */

namespace AppBundle\Entity;


class ListContent
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    public static function f1()
    {
        return new self('', '', '');
    }
    /**
     * Olx constructor.
     * @param $content
     * @param $remoteId
     * @param $url
     */
    private function __construct($content, $remoteId, $url)
    {
        $this->content = $content;
        $this->remoteId = $remoteId;
        $this->url = $url;
    }
}
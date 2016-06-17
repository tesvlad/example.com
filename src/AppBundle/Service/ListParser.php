<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 17.05.16
 * Time: 20:42
 */

namespace AppBundle\Service;

use Psr\Http\Message\ResponseInterface;

class ListParser
{

    /**
     * @param ResponseInterface $response
     * @return array
     */
    public function parse(ResponseInterface $response)
    {
        $response = $response->getBody();
        $input = $response;
        preg_match_all("!<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" class=\"fixed breakword(.*?)</table>!si", $input, $matches);

        $data = [];
        foreach ($matches[0] as $x) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadHTML($x);
            $table = $dom->getElementsByTagName('table');
            $a = $table->item(0)->attributes->getNamedItem('data-id');
            $id = $a->textContent;

            $finder = new \DomXPath($dom);
            $classname = "detailsLink";
            $nodeDetailLink = $finder->query("//*[contains(@class, '$classname')]");
            $href = $nodeDetailLink->item(0)->attributes->getNamedItem('href')->textContent;
            $data[] = ['id' => $id, 'href' => $href,];
        }

        return $data;
    }
}
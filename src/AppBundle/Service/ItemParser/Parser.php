<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 07.06.16
 * Time: 20:46
 */

namespace AppBundle\Service\ItemParser;


use AppBundle\Entity\OlxAveo;
use AppBundle\Entity\OlxContent;

class Parser
{

    public function get(OlxContent $aveo)
    {
        $price = $this->getPrice($aveo);
        $year = $this->getYear($aveo);
        $mark = $this->getMark($aveo);
        $model = $this->getModel($aveo);

        $aveo->setPrice($price);
        $aveo->setYear($year);
        $aveo->setCurrency($this->getCurrency($aveo));
        $aveo->setBrand($mark);
        $aveo->setModel($model);
    }
    private function getCurrency(OlxContent $olx)
    {

        preg_match_all("!<strong class=\"xxxx-large margintop7 block(.*?)</strong>!si", $olx->getContent(), $matches);
        try {
            $price = $matches[1][0];
            $pos = mb_strpos($price, '">');
            $price = mb_substr($price, 2 + $pos);
            $price = str_replace(' ', '', $price);
            $price = str_replace('', '', $price);
            if(false !==strpos($price, '$')){
                return 'usd';
            }

            return 'uah';
        } catch (\Exception $e) {
            return null;
        }
    }
    private function getPrice(OlxContent $olx)
    {

        preg_match_all("!<strong class=\"xxxx-large margintop7 block(.*?)</strong>!si", $olx->getContent(), $matches);
        try {
            $price = $matches[1][0];
            $pos = mb_strpos($price, '">');
            $price = mb_substr($price, 2 + $pos);
            $price = str_replace(' ', '', $price);
            $price = str_replace('', '', $price);

            $price = (float)$price;
            if ($price == 0) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return $price;
    }

    private function getYear(OlxContent $olx)
    {
        preg_match_all("!<table class=\"details fixed marginbott20 margintop5 full\" cellpadding=\"0\" cellspacing=\"0\">(.*?)<div class=\"clr\" id=\"textContent\">!si", $olx->getContent(), $matches);
        $data = [];
        foreach ($matches[0] as $x) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->encoding = 'UTF-8';
//            $dom->actualEncoding = 'UTF-8';
            $r = $dom->loadHTML(mb_convert_encoding($x, 'HTML-ENTITIES', 'UTF-8'));
            $dom->encoding = 'UTF-8';
            $a = $dom->textContent;
//            $a =  str_replace('	', '|',  $a);
          $a = str_replace('	', '', $a);

            $a = str_replace('\n', '', $a);
            $a = str_replace('\r', '', $a);
            $a = str_replace('\r\n', '', $a);
            $a = str_replace(PHP_EOL, '', $a);
            $a = trim($a);

            $pos = mb_strpos($a, 'Год выпуска');

            $b = mb_substr($a, 11 + $pos, 4);
            return $year = (int) $b;
        }
    }

    private function getMark(OlxContent $olx)
    {
        preg_match_all("!<table class=\"details fixed marginbott20 margintop5 full\" cellpadding=\"0\" cellspacing=\"0\">(.*?)<div class=\"clr\" id=\"textContent\">!si", $olx->getContent(), $matches);
        $data = [];
        foreach ($matches[0] as $x) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->encoding = 'UTF-8';
//            $dom->actualEncoding = 'UTF-8';
            $r = $dom->loadHTML(mb_convert_encoding($x, 'HTML-ENTITIES', 'UTF-8'));
            $dom->encoding = 'UTF-8';
            $a = $dom->textContent;
//            $a =  str_replace('	', '|',  $a);
            $r = explode('	', $a);
            foreach ($r as $a1 => $a2) {
                trim($r[$a1]);
                if (empty($a2) or $a2 == '	' or $a2 == '	' or trim($a2) == '') {
                    unset($r[$a1]);
                }

            }
            $r = array_values($r);
            foreach ($r as $a1 => $a2) {
                if (false !== strpos($a2, 'Марка')) {
                    $key = $a1 + 1;
                    return $r[$key];
                }
            }

        }
    }

    private function getModel( $olx)
    {
        preg_match_all("!<table class=\"details fixed marginbott20 margintop5 full\" cellpadding=\"0\" cellspacing=\"0\">(.*?)<div class=\"clr\" id=\"textContent\">!si", $olx->getContent(), $matches);
        $data = [];
        foreach ($matches[0] as $x) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->encoding = 'UTF-8';
//            $dom->actualEncoding = 'UTF-8';
            $r = $dom->loadHTML(mb_convert_encoding($x, 'HTML-ENTITIES', 'UTF-8'));
            $dom->encoding = 'UTF-8';
            $a = $dom->textContent;
            //$a =  str_replace('	', '|',  $a);
            //  $a = str_replace('	', '', $a);
            $r = explode('	', $a);
            foreach ($r as $a1 => $a2) {
                trim($r[$a1]);
                if (empty($a2) or $a2 == '	' or $a2 == '	' or trim($a2) == '') {
                    unset($r[$a1]);
                }

            }
            $r = array_values($r);
            foreach ($r as $a1 => $a2) {
                if (false !== strpos($a2, 'Модель')) {
                    $key = $a1 + 1;
                    return $r[$key];
                }
            }

        }
    }
}
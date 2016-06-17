<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 17.05.16
 * Time: 20:42
 */

namespace AppBundle\Service;


class Requestor
{

    /**
     * @param $url
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function request($url, $proxy = null)
    {
        $client = new \GuzzleHttp\Client();
        $options = [];
        if ($proxy !== null) {
            $options = ['proxy' => $proxy];
        }

        return $client->request('GET', $url, $options);
    }
}
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Olx;
use AppBundle\Entity\OlxAveo;
use AppBundle\Entity\OlxContent;
use AppBundle\Service\FileFinder;
use AppBundle\Service\ItemParser\Parser;
use AppBundle\Service\ListParser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\Requestor;

class DefaultController extends Controller
{

    /**
     * @Route("/vld3", name="homepage3")
     */
    public function index3Action(Request $request)
    {


        $f = new FileFinder();
        $list = $f->isFile()->inDir('asdasd')->getList();

        var_dump(iterator_to_array($list));exit;














        $total = 5;
        $brand = 'daewoo';
        $urlBase = 'http://olx.ua/transport/legkovye-avtomobili/';

        $urls = [$urlBase];
        for($i = 2; $i<=$total; $i++){
            $urls[] = $urlBase.'?page='.$i;
        }

        foreach($urls as $url) {
            $this->run($brand, $url);
            $this->get('logger')->info('OK: '.$url, ['url' => $url, 'memory' => memory_get_peak_usage(1)]);
            gc_collect_cycles();
//            sleep(1);
        }


        var_dump('OK');exit;
    }

    private function run($brand, $url)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $requestor = new Requestor();
        $listParser = new ListParser();

        $proxy = [
            'http' => 'tcp://213.129.39.227:3128', // Use this proxy with "http"
            'https' => 'tcp://213.129.39.227:3128', // Use this proxy with "https",
        ];

        try {
            $response = $requestor->request($url);
        }catch (\Exception $e){
            $this->get('logger')->addCritical('proxy return exception code'.$url, ['url' => $url, 'proxy' => $proxy, $e]);
//            $response = $requestor->request($url);
            return;
        }

        $data = $listParser->parse($response);
        foreach ($data as $item) {
            $remoteId = $item['id'];
            $url = $item['href'];
            $dbOlx = $this->get('doctrine.orm.default_entity_manager')->getRepository(OlxContent::class)->findOneBy(['remoteId' => $remoteId]);
            if (null !== $dbOlx) {
                continue;
            }

            $olxData = new OlxContent($brand, $remoteId, $url);
            $olxData->setContent(file_get_contents($url));
            $em->persist($olxData);
            $em->flush();
        }

        $em->clear();

        return true;
    }

    /**
     * @Route("/vld", name="vld")
     */
    public function vldAction(Request $request)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $batchSize = 20;
        $i = 0;
        $q = $em->createQuery('select u from AppBundle\Entity\OlxContent u WHERE  u.id >= 4 ORDER by u.id');
        $iterableResult = $q->iterate();
        foreach ($iterableResult as $row) {
            /** @var  $dbOlx  OlxContent */
            $dbOlx = $row[0];

//            $dbOlx->setContent(file_get_contents($dbOlx->getUrl()));
            $p  = new Parser();
            $p->get($dbOlx);
//            $dbOlx->setPrice($this->getPrice($dbOlx))->setYear($this->getYear($dbOlx));
            if (($i % $batchSize) === 0) {
                $em->flush(); // Executes all updates.
                $em->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }
        $em->flush();

    }

}

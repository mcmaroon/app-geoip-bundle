<?php

namespace App\GeoIpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller {

    public function indexAction(Request $request) {

        $geoIp = $this->container->get('app.geoip')->lookup("89.229.9.119"); // sample ip
        
        dump($geoIp->getLatitude());
        dump($geoIp->getLongitude());
        //dump($geoIp->findClosestPoints('MaroonWeddingBundle:TaxonomyCity', 50));
        
        return $this->render('AppGeoIpBundle:Test:index.html.twig', array('geo' => $geoIp));
    }

}

<?php

namespace App\GeoIpBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeoIpTest extends WebTestCase {

    private $container;

    public function testIndex() {
        $client = static::createClient();

        $this->container = $client->getContainer();

        $geoIp = $this->container->get('app.geoip')->lookup("89.229.9.119");

        $this->assertTrue(is_float($geoIp->getLatitude()));
        $this->assertTrue(is_float($geoIp->getLongitude()));        
        /**
         * @todo Przesłać do testu Namespace za pomoca parametru
         */
        //$this->assertGreaterThan(0, count($geoIp->findClosestPoints('WeddingMainBundle:TaxonomyCity', 50)));
    }

}

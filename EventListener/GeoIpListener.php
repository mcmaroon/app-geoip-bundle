<?php

//https://github.com/aferrandini/Maxmind-GeoIp/blob/master/src/Maxmind/Bundle/GeoipBundle/Service/GeoipManager.php

namespace App\GeoIpBundle\EventListener;

use App\GeoIpBundle\Lib\GeoIp;
use App\GeoIpBundle\Lib\GeoIpRegionVars;
use App\GeoIpBundle\Helper\GeoIpCoords;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GeoIpListener {

    protected $geoip = null;
    protected $record = null;
    protected $container;
    protected $em;

    public function __construct($kernel, ContainerInterface $container) {
        $path = $kernel->locateResource('@AppGeoIpBundle/Lib/GeoIP.dat');
        $this->geoip = new GeoIP($path);
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->lookup();
    }

    // ~

    public function lookup($ip = null) {

        if ($ip === null) {
            $request = $this->container->get('request_stack')->getCurrentRequest();
            $ip = $request ? $request->getClientIp() : null;
        }

        if (\in_array($ip, ['127.0.0.1', 'fe80::1', '::1'])) {
            $ip = "89.229.0.226";
        }

        $this->record = $this->geoip->geoip_record_by_addr($ip);
        if ($this->record) {
            return $this;
        }
        return false;
    }

    // ~

    public function getLatitude() {
        if ($this->record) {
            return \floatval($this->record->latitude);
        }
        return $this->record;
    }

    // ~

    public function getLongitude() {
        if ($this->record) {
            return \floatval($this->record->longitude);
        }
        return $this->record;
    }

    // ~

    public final function getCoords($byUser = false) {

        if ($byUser) {
            $securityContext = $this->container->get('security.authorization_checker');
            if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user = $this->container->get('security.token_storage')->getToken()->getUser();
                if (method_exists($user, 'isLocated') && $user->isLocated()) {
                    return new GeoIpCoords($user->getLatitude(), $user->getLongitude());
                }
            }
        }

        if ($this->record) {
            return new GeoIpCoords($this->getLatitude(), $this->getLongitude());
        }

        return $this->record;
    }

    // ~

    public function getCity() {
        if ($this->record) {
            return $this->record->city;
        }
        return $this->record;
    }

    // ~

    public function getPostalCode() {
        if ($this->record) {
            return $this->record->postal_code;
        }
        return $this->record;
    }

    // ~

    public function getCountryCode() {
        if ($this->record) {
            return $this->record->country_code;
        }
        return $this->record;
    }

    // ~

    public function getCountryCode3() {
        if ($this->record) {
            return $this->record->country_code3;
        }
        return $this->record;
    }

    // ~

    public function findClosestPoints($repositoryNamespace, $distance = 30, $active = null) {

        $log = $this->container->get('app.log');

        try {
            $repository = $this->em->getRepository((string) $repositoryNamespace);

            $queryBuilder = $repository->createQueryBuilder('r');
            $queryBuilder->select(array(
                'r AS obj',
                'r.id AS id',
                'r.name AS name',
                'r.latitude AS latitude',
                'r.longitude AS longitude'
            ));
            $queryBuilder->addSelect('round(6371 * acos(cos(radians(:latitude)) * cos(radians(r.latitude)) * cos(radians(r.longitude) - radians(:longitude)) + sin(radians(:latitude)) * sin(radians(r.latitude))), 3) as distance');

            $queryBuilder->setParameter('latitude', $this->getLatitude());
            $queryBuilder->setParameter('longitude', $this->getLongitude());

            if ((int) $distance > 0) {
                $queryBuilder->having($queryBuilder->expr()->lt('distance', ':distance'));
                $queryBuilder->setParameter('distance', $distance);
            }

            if ($active !== null) {
                $queryBuilder->andWhere('r.active = :active');
                $queryBuilder->setParameter('active', true);
            }

            $queryBuilder->orderBy('distance', 'ASC');

            return $queryBuilder->getQuery()->getResult();
        } catch (\Exception $exc) {
            $log->error('GeoIpListener:findClosestPoints', array(
                'code' => $exc->getCode(),
                'message' => $exc->getMessage()
            ));
        }
        return false;
    }

}

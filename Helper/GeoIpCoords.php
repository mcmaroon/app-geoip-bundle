<?php

namespace App\GeoIpBundle\Helper;

use Doctrine\ORM\QueryBuilder;

class GeoIpCoords {

    private $latitude = null;
    private $longitude = null;

    public final function __construct($latitude, $longitude) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public final function getLatitude() {
        return floatval($this->latitude);
    }

    public final function getLongitude() {
        return floatval($this->longitude);
    }

    public final function addQueryBuilderDistance(QueryBuilder $queryBuilder, $distance = 0, $order = null) {

        $queryBuilder->addSelect('round(6371 * acos(cos(radians(:latitude)) * cos(radians(r.latitude)) * cos(radians(r.longitude) - radians(:longitude)) + sin(radians(:latitude)) * sin(radians(r.latitude))), 3) as distance');
        $queryBuilder->setParameter('latitude', $this->getLatitude());
        $queryBuilder->setParameter('longitude', $this->getLongitude());

        if ((int) $distance > 0) {
            $queryBuilder->having($queryBuilder->expr()->lt('distance', ':distance'));
            $queryBuilder->setParameter('distance', $distance);
        }

        if (is_string($order) && in_array($order, array('asc', 'desc'))) {
            $queryBuilder->addOrderBy('distance', $order);
        }

        return $queryBuilder;
    }

}

<?php

namespace App\GeoIpBundle\Helper\Traits;

use Doctrine\ORM\Mapping as ORM;

trait GeoIpSimpleTrait {

    /**
     * @ORM\Column(name="latitude", type="decimal", precision=11, scale=8, nullable=true)
     */
    protected $latitude;

    /**
     * @ORM\Column(name="longitude", type="decimal", precision=11, scale=8, nullable=true)
     */
    protected $longitude;

    /**
     * Set Latitude
     *
     * @param string $lat
     */
    public function setLatitude($latitude = null) {
        $this->latitude = $latitude;

        return $this;
    }

    public final function getLatitude() {
        return floatval($this->latitude);
    }

    /**
     * Set lng
     *
     * @param string $lng
     */
    public function setLongitude($longitude = null) {
        $this->longitude = $longitude;

        return $this;
    }

    public final function getLongitude() {
        return floatval($this->longitude);
    }

    /**
     * Is located?
     *
     * @return bool
     */
    public function isLocated() {
        return null !== $this->latitude && null !== $this->longitude;
    }

}

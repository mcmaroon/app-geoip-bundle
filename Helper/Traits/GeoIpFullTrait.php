<?php

namespace App\GeoIpBundle\Helper\Traits;

use Doctrine\ORM\Mapping as ORM;
use App\GeoIpBundle\Helper\Traits\GeoIpSimpleTrait;

trait GeoIpFullTrait {

    use \App\GeoIpBundle\Helper\Traits\GeoIpSimpleTrait;

    /**
     * @ORM\Column(name="postal", type="string", length=10, nullable=true)
     */
    protected $postal;

    /**
     * Set postal
     *
     * @param string $postal
     */
    public function setPostal($postal) {
        $this->postal = str_replace(array(' '), '-', $postal);

        return $this;
    }

    /**
     * Get postal
     *
     * @return string
     */
    public function getPostal() {
        return $this->postal;
    }

}

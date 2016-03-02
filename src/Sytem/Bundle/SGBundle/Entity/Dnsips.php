<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sytem\Bundle\SGBundle\Validator\Constraints as SytemAssert;

/**
 * Dnsips
 * @ORM\Entity
 * @UniqueEntity(
 *     fields="ipdns",
 *     errorPath="ipdns",
 *     message="O ipdns especificado ja existe!!!") 
 *
 * 
 *
 * 
 */
class Dnsips
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @SytemAssert\ContainsIntervaloIpVerdadeiro
     */
    private $ipdns;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ipdns
     *
     * @param string $ipdns
     * @return Dnsips
     */
    public function setIpdns($ipdns)
    {
        $this->ipdns = $ipdns;

        return $this;
    }

    /**
     * Get ipdns
     *
     * @return string 
     */
    public function getIpdns()
    {
        return $this->ipdns;
    }
}

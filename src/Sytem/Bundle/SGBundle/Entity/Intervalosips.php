<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sytem\Bundle\SGBundle\Validator\Constraints as SytemAssert;
//use Sytem\Bundle\SGBundle\Entity\Intervalosips;
/**
 * Intervalosips
 * @ORM\Entity
 * @UniqueEntity(
 *     fields="intervalo",
 *     errorPath="intervalo",
 *     message="O intervalo especificado ja existe!!!") 
 * 
 */
class Intervalosips
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string      
     * @SytemAssert\ContainsIntervaloIp
     */
    private $intervalo;


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
     * Set intervalo
     *
     * @param string $intervalo
     * @return Intervalosips
     */
    public function setIntervalo($intervalo)
    {
        $this->intervalo = $intervalo;

        return $this;
    }

    /**
     * Get intervalo
     *
     * @return string 
     */
    public function getIntervalo()
    {
        return $this->intervalo;
    }
}

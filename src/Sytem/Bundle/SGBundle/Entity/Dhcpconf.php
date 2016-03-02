<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dhcpconf
 */
class Dhcpconf
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $optionrouters;


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
     * Set optionrouters
     *
     * @param string $optionrouters
     * @return Dhcpconf
     */
    public function setOptionrouters($optionrouters)
    {
        $this->optionrouters = $optionrouters;

        return $this;
    }

    /**
     * Get optionrouters
     *
     * @return string 
     */
    public function getOptionrouters()
    {
        return $this->optionrouters;
    }
}

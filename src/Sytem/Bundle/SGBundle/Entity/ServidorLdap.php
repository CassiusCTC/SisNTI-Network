<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sytem\Bundle\SGBundle\Validator\Constraints as SytemAssert;
/**
 * ServidorLdap
 */
class ServidorLdap
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @SytemAssert\ContainsIntervaloIpVerdadeiro
     * 
     */
    private $ipLdap;

    /**
     * @var string
     */
    private $complemento;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;


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
     * Set ipLdap
     *
     * @param string $ipLdap
     * @return ServidorLdap
     */
    public function setIpLdap($ipLdap)
    {
        $this->ipLdap = $ipLdap;

        return $this;
    }

    /**
     * Get ipLdap
     *
     * @return string 
     */
    public function getIpLdap()
    {
        return $this->ipLdap;
    }

    /**
     * Set complemento
     *
     * @param string $complemento
     * @return ServidorLdap
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;

        return $this;
    }

    /**
     * Get complemento
     *
     * @return string 
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return ServidorLdap
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return ServidorLdap
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
}

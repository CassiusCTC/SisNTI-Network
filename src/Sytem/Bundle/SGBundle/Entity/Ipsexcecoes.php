<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sytem\Bundle\SGBundle\Validator\Constraints as SytemAssert;

/**
 * Ipsexcecoes
 *  @ORM\Entity
 * @UniqueEntity(     
 *     fields="ipexcecao",
 *     errorPath="ipexcecao",
 *     message="O IP excessão especificado já existe no banco de dados") 
 */
class Ipsexcecoes
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
    private $ipexcecao;


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
     * Set ipexcecao
     *
     * @param string $ipexcecao
     * @return Ipsexcecoes
     */
    public function setIpexcecao($ipexcecao)
    {
        $this->ipexcecao = $ipexcecao;

        return $this;
    }

    /**
     * Get ipexcecao
     *
     * @return string 
     */
    public function getIpexcecao()
    {
        return $this->ipexcecao;
    }
}

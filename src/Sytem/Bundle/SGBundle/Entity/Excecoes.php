<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sytem\Bundle\SGBundle\Validator\Constraints as SytemAssert;

/**
 * Excecoes
 * @ORM\Entity
 * @UniqueEntity(
 *     fields="cpf",
 *     errorPath="cpf",
 *     message="O cpf especificado já existe no banco de dados") 
 */
class Excecoes
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @SytemAssert\ContainsCpf
     */
    private $cpf;


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
     * Set cpf
     *
     * @param string $cpf
     * @return Excecoes
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get cpf
     *
     * @return string 
     */
    public function getCpf()
    {
        return $this->cpf;
    }
}

<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Laboratorios
 * @ORM\Entity
 * @UniqueEntity(
 *     fields="nomeLaboratorio",
 *     errorPath="nomeLaboratorio",
 *     message="O nome do Laboratorio especificado ja existe no banco de dados") 
 * 
 */
class Laboratorios
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nomeLaboratorio;


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
     * Set nomeLaboratorio
     *
     * @param string $nomeLaboratorio
     * @return Laboratorios
     */
    public function setNomeLaboratorio($nomeLaboratorio)
    {
        $this->nomeLaboratorio = $nomeLaboratorio;

        return $this;
    }

    /**
     * Get nomeLaboratorio
     *
     * @return string 
     */
    public function getNomeLaboratorio()
    {
        return $this->nomeLaboratorio;
    }
    
     public function __toString(){

        return $this->nomeLaboratorio;
    }
    /**
     * @var string
     */
    private $bloco;

    /**
     * @var string
     */
    private $sala;


    /**
     * Set bloco
     *
     * @param string $bloco
     * @return Laboratorios
     */
    public function setBloco($bloco)
    {
        $this->bloco = $bloco;

        return $this;
    }

    /**
     * Get bloco
     *
     * @return string 
     */
    public function getBloco()
    {
        return $this->bloco;
    }

    /**
     * Set sala
     *
     * @param string $sala
     * @return Laboratorios
     */
    public function setSala($sala)
    {
        $this->sala = $sala;

        return $this;
    }

    /**
     * Get sala
     *
     * @return string 
     */
    public function getSala()
    {
        return $this->sala;
    }
}

<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//use Sytem\Bundle\SGBundle\Validator\Constraints as SytemAssert;


/**
 * ArquivosRemotos
 */
class ArquivosRemotos
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     * 
     * 
     */
    private $serverip;

    /**
     * @var string
     */
    private $nomeArquivo;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $caminhoDestino;

    /**
     * @var string
     */
    private $caminhoOrigem;


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
     * Set serverip
     *
     * @param string $serverip
     * @return ArquivosRemotos
     */
    public function setServerip($serverip)
    {
        $this->serverip = $serverip;

        return $this;
    }

    /**
     * Get serverip
     *
     * @return string 
     */
    public function getServerip()
    {
        return $this->serverip;
    }

    /**
     * Set nomeArquivo
     *
     * @param string $nomeArquivo
     * @return ArquivosRemotos
     */
    public function setNomeArquivo($nomeArquivo)
    {
        $this->nomeArquivo = $nomeArquivo;

        return $this;
    }

    /**
     * Get nomeArquivo
     *
     * @return string 
     */
    public function getNomeArquivo()
    {
        return $this->nomeArquivo;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return ArquivosRemotos
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
     * @return ArquivosRemotos
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

    /**
     * Set caminhoDestino
     *
     * @param string $caminhoDestino
     * @return ArquivosRemotos
     */
    public function setCaminhoDestino($caminhoDestino)
    {
        $this->caminhoDestino = $caminhoDestino;

        return $this;
    }

    /**
     * Get caminhoDestino
     *
     * @return string 
     */
    public function getCaminhoDestino()
    {
        return $this->caminhoDestino;
    }

    /**
     * Set caminhoOrigem
     *
     * @param string $caminhoOrigem
     * @return ArquivosRemotos
     */
    public function setCaminhoOrigem($caminhoOrigem)
    {
        $this->caminhoOrigem = $caminhoOrigem;

        return $this;
    }

    /**
     * Get caminhoOrigem
     *
     * @return string 
     */
    public function getCaminhoOrigem()
    {
        return $this->caminhoOrigem;
    }
}

<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sytem\Bundle\SGBundle\Validator\Constraints as SytemAssert;
use Sytem\Bundle\SGBundle\Entity\Laboratorios;
use Sytem\Bundle\SGBundle\Entity\Usuariosystem;

/**
 * DadosArquivos
 * @ORM\Entity
 * @UniqueEntity(     
 *     fields="ip",
 *     errorPath="ip",
 *     message="O IP adress especificado ja existe no banco de dados") 
 * @UniqueEntity(     
 *     fields="mac",
 *     errorPath="mac",
 *     message="O Mac adress especificado ja existe no banco de dados") 
 */
class DadosArquivos
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
    private $ip;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @SytemAssert\ContainsMacAddress
     */
    private $mac;

    /**
     * @var string
     */
    private $patrimonioLocal;

    /**
     * @var \DateTime
     */
    private $dataCadastro;

    /**
     * @var string
     */
    private $statusdhcp;

    /**
     * @var \DateTime
     */
    private $datadhcplog;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $descricao;

    /**
     * @var string  
     */
    private $file;

     /**
     * @var string 
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Os uploads validos são somente os de extenção pdf."
     * )    
     */
    private $filemove;

    /**
     * @var \Sytem\Bundle\SGBundle\Entity\Usuariosystem
     */
    private $responsavel;

    /**
     * @var \Sytem\Bundle\SGBundle\Entity\Usuariosystem
     */
    private $usuario;

    /**
     * @var \Sytem\Bundle\SGBundle\Entity\Tipos
     */
    private $tipo;

    /**
     * @var \Sytem\Bundle\SGBundle\Entity\Laboratorios
     */
    private $laboratorio;


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
     * Set ip
     *
     * @param string $ip
     * @return DadosArquivos
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set mac
     *
     * @param string $mac
     * @return DadosArquivos
     */
    public function setMac($mac)
    {
        $this->mac = $mac;

        return $this;
    }

    /**
     * Get mac
     *
     * @return string 
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * Set patrimonioLocal
     *
     * @param string $patrimonioLocal
     * @return DadosArquivos
     */
    public function setPatrimonioLocal($patrimonioLocal)
    {
        $this->patrimonioLocal = $patrimonioLocal;

        return $this;
    }

    /**
     * Get patrimonioLocal
     *
     * @return string 
     */
    public function getPatrimonioLocal()
    {
        return $this->patrimonioLocal;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return DadosArquivos
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;

        return $this;
    }

    /**
     * Get dataCadastro
     *
     * @return \DateTime 
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * Set statusdhcp
     *
     * @param string $statusdhcp
     * @return DadosArquivos
     */
    public function setStatusdhcp($statusdhcp)
    {
        $this->statusdhcp = $statusdhcp;

        return $this;
    }

    /**
     * Get statusdhcp
     *
     * @return string 
     */
    public function getStatusdhcp()
    {
        return $this->statusdhcp;
    }

    /**
     * Set datadhcplog
     *
     * @param \DateTime $datadhcplog
     * @return DadosArquivos
     */
    public function setDatadhcplog($datadhcplog)
    {
        $this->datadhcplog = $datadhcplog;

        return $this;
    }

    /**
     * Get datadhcplog
     *
     * @return \DateTime 
     */
    public function getDatadhcplog()
    {
        return $this->datadhcplog;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return DadosArquivos
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return DadosArquivos
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao
     *
     * @return string 
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return DadosArquivos
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }



    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set filemove
     *
     * @param string $filemove
     * @return DadosArquivos
     */
    public function setFilemove($filemove)
    {
        $this->filemove = $filemove;

        return $this;
    }



    /**
     * Get filemove
     *
     * @return string 
     */
    public function getFilemove()
    {
        return $this->filemove;
    }

    /**
     * Set responsavel
     *
     * @param \Sytem\Bundle\SGBundle\Entity\Usuariosystem $responsavel
     * @return DadosArquivos
     */
    public function setResponsavel(\Sytem\Bundle\SGBundle\Entity\Usuariosystem $responsavel = null)
    {
        $this->responsavel = $responsavel;

        return $this;
    }

    /**
     * Get responsavel
     *
     * @return \Sytem\Bundle\SGBundle\Entity\Usuariosystem 
     */
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * Set usuario
     *
     * @param \Sytem\Bundle\SGBundle\Entity\Usuariosystem $usuario
     * @return DadosArquivos
     */
    public function setUsuario(\Sytem\Bundle\SGBundle\Entity\Usuariosystem $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Sytem\Bundle\SGBundle\Entity\Usuariosystem 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set tipo
     *
     * @param \Sytem\Bundle\SGBundle\Entity\Tipos $tipo
     * @return DadosArquivos
     */
    public function setTipo(\Sytem\Bundle\SGBundle\Entity\Tipos $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \Sytem\Bundle\SGBundle\Entity\Tipos 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set laboratorio
     *
     * @param \Sytem\Bundle\SGBundle\Entity\Laboratorios $laboratorio
     * @return DadosArquivos
     */
    public function setLaboratorio(\Sytem\Bundle\SGBundle\Entity\Laboratorios $laboratorio = null)
    {
        $this->laboratorio = $laboratorio;

        return $this;
    }

    /**
     * Get laboratorio
     *
     * @return \Sytem\Bundle\SGBundle\Entity\Laboratorios 
     */
    public function getLaboratorio()
    {
        return $this->laboratorio;
    }
}

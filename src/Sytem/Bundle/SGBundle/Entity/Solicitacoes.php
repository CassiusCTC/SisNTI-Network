<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sytem\Bundle\SGBundle\Validator\Constraints as SytemAssert;
use Sytem\Bundle\SGBundle\Entity\Usuariosystem;

/**
 * Solicitacoes
 */
class Solicitacoes
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var \DateTime
     */
    private $dataSolicitacao;

    /**
     * @var string
     */
    private $avaliacao;

    /**
     * @var string
     */
    private $motivo;

    /**
     * @var string
     */
    private $respaval;

    /**
     * @var string
     */
    private $justificativa;

    /**
     * @var string
     */
    private $descricaoequip;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @SytemAssert\ContainsMacAddress
     */
    private $mac;

    /**
     * @var \Sytem\Bundle\SGBundle\Entity\Usuariosystem
     */
    private $usuario;

    /**
     * @var \Sytem\Bundle\SGBundle\Entity\Laboratorios
     */
    private $laboratorio;

    /**
     * @var \Sytem\Bundle\SGBundle\Entity\Usuariosystem
     */
    private $responsavel;


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
     * Set tipo
     *
     * @param string $tipo
     * @return Solicitacoes
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set dataSolicitacao
     *
     * @param \DateTime $dataSolicitacao
     * @return Solicitacoes
     */
    public function setDataSolicitacao($dataSolicitacao)
    {
        $this->dataSolicitacao = $dataSolicitacao;

        return $this;
    }

    /**
     * Get dataSolicitacao
     *
     * @return \DateTime 
     */
    public function getDataSolicitacao()
    {
        return $this->dataSolicitacao;
    }

    /**
     * Set avaliacao
     *
     * @param string $avaliacao
     * @return Solicitacoes
     */
    public function setAvaliacao($avaliacao)
    {
        $this->avaliacao = $avaliacao;

        return $this;
    }

    /**
     * Get avaliacao
     *
     * @return string 
     */
    public function getAvaliacao()
    {
        return $this->avaliacao;
    }

    /**
     * Set motivo
     *
     * @param string $motivo
     * @return Solicitacoes
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string 
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set respaval
     *
     * @param string $respaval
     * @return Solicitacoes
     */
    public function setRespaval($respaval)
    {
        $this->respaval = $respaval;

        return $this;
    }

    /**
     * Get respaval
     *
     * @return string 
     */
    public function getRespaval()
    {
        return $this->respaval;
    }

    /**
     * Set justificativa
     *
     * @param string $justificativa
     * @return Solicitacoes
     */
    public function setJustificativa($justificativa)
    {
        $this->justificativa = $justificativa;

        return $this;
    }

    /**
     * Get justificativa
     *
     * @return string 
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * Set descricaoequip
     *
     * @param string $descricaoequip
     * @return Solicitacoes
     */
    public function setDescricaoequip($descricaoequip)
    {
        $this->descricaoequip = $descricaoequip;

        return $this;
    }

    /**
     * Get descricaoequip
     *
     * @return string 
     */
    public function getDescricaoequip()
    {
        return $this->descricaoequip;
    }

    /**
     * Set mac
     *
     * @param string $mac
     * @return Solicitacoes
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
     * Set usuario
     *
     * @param \Sytem\Bundle\SGBundle\Entity\Usuariosystem $usuario
     * @return Solicitacoes
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
     * Set laboratorio
     *
     * @param \Sytem\Bundle\SGBundle\Entity\Laboratorios $laboratorio
     * @return Solicitacoes
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

    /**
     * Set responsavel
     *
     * @param \Sytem\Bundle\SGBundle\Entity\Usuariosystem $responsavel
     * @return Solicitacoes
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
     * @var string
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     */
    private $file;


    /**
     * Set file
     *
     * @param string $file
     * @return Solicitacoes
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
}

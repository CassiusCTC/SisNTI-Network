<?php

namespace Sytem\Bundle\SGBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sytem\Bundle\SGBundle\Validator\Constraints as SytemAssert;

/**
 * Usuariosystem
 * @UniqueEntity(     
 *     fields="cpf",
 *     errorPath="cpf",
 *     message="O Cpf especificado ja existe no banco de dados") 
 * 
 */
class Usuariosystem
{
    /**
     * @var integer
     */
    private $id;


    
    /**
     * @var string
     * 
     */
    private $cpf;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var string
     */
    private $telefone;

    /**
     * @var string
     */
    private $email;

   
   public function __construct($cpf= null,$nome= null,$telefone= null,$email= null){
        $this->cpf = $cpf;
        $this->nome = $nome;
        $this->telefone = $telefone;
        $this->email = $email;
       
   }
    
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
     * @return Usuariosystem
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

    /**
     * Set nome
     *
     * @param string $nome
     * @return Usuariosystem
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string 
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set telefone
     *
     * @param string $telefone
     * @return Usuariosystem
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;

        return $this;
    }

    /**
     * Get telefone
     *
     * @return string 
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Usuariosystem
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

   
    public function __toString(){
        return $this->cpf;
    }
    
}

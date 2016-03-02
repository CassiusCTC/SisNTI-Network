<?php

namespace Sytem\Bundle\SGBundle\Entity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;


class UserLDAP implements UserInterface, EquatableInterface {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var password
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $roles;

    /**
     * @var string
     */
    private $telefone;

     /**
     * @var string
     */
    private $grupo;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    public function __construct($id = null, $nome = null, $email = null,$telefone=null,array $roles=null,$grupo=null, $password = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->telefone=$telefone;
        $this->roles=$roles;
        $this->grupo=$grupo;
        $this->password = $password;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return Usuario
     */
    public function setNome($nome) {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string 
     */
    public function getNome() {
        return $this->nome;
    }

 /**
     * Set grupo
     *
     * @param string $grupo
     * @return Usuario
     */
    public function setGrupo($grupo) {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo
     *
     * @return string 
     */
    public function getGrupo() {
        return $this->grupo;
    }

     /**
     * Set telefone
     *
     * @param string $telefone
     * @return Usuario
     */
    public function setTelefone($telefone) {
        $this->telefone = $telefone;

        return $this;
    }

    /**
     * Get telefone
     *
     * @return string 
     */
    public function getTelefone() {
        return $this->telefone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Usuario
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Usuario
     */
    public function setRoles(array $roles) {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials() {
        
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getSalt() {
        return null;
    }

    public function getUsername() {
        return $this->getId();
    }

    public function isEqualTo(UserInterface $user) {
        if (!$user instanceof userLDAP) {
            return false;
        }

        if ($this->id !== $user->getUsername()) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }
        return true;
    }

}
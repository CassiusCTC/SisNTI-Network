<?php

namespace Sytem\Bundle\SGBundle\Services;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Sytem\Bundle\SGBundle\Entity\UserROOT;

class UserROOTProvider implements UserProviderInterface{
    
    public function loadUserByUsername($username) {
        $file=__DIR__.'/file/file_password.txt';
        join('/', array(trim(" ", '/'), trim($file, '/')));
        //echo $file;
        //exit();
        if(file_exists($file)){
            $row=file($file);           
            $tipo=$row[4];
            $nome=$row[2];
            $email=$row[0];
            $pass=$row[1];  
            $password = trim($row[1]);
            $role=array('ROLE_ADMIN');
            $telefone=$row[3];
            //echo $password;exit();
            $userroot = new UserROOT($email, $tipo, $email,$telefone,$role,$password);
           
            return $userroot;     
          
           
        }else{
            $userroot = new UserROOT(null, null, null,null,null, null);
            return $userroot;  
        }
          
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof UserROOT) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Sytem\Bundle\SGBundle\Entity\UserROOT';
    }
    
}

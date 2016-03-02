<?php

namespace Sytem\Bundle\SGBundle\Services;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Sytem\Bundle\SGBundle\Entity\UserLDAP;
use Sytem\Bundle\SGBundle\Entity\ServidorLdap;
use Doctrine\ORM\EntityManager;

class UserLDAPProvider implements UserProviderInterface{
    /**
     *
     * @var EntityManager 
     */
    private $em;

    public function __construct(EntityManager $entityManager) {
       $this->em = $entityManager;
    }

    
    public function loadUserByUsername($username) {

      
      //$file=__DIR__.'/file/filecpf.txt';
      $file2=__DIR__.'/file/file_password.txt';
      //$file3=__DIR__.'/file/config_ldap.txt';
      
      join('/', array(trim(" ", '/'), trim($file2, '/')));
      $query2=$this->em->getRepository('SytemSGBundle:ServidorLdap')->findAll();
      if($query2/*file_exists($file3*/){
         // $row1=file($file3); 
        foreach ($query2 as $key => $value) {
          $ipservidor=$value->getIpLdap();//trim($row1[0]);
          $complemento=$value->getComplemento();//trim($row1[1]);
          $login=$value->getUsername();//trim($row1[2]);
          $senha=$value->getPassword();//trim($row1[3]);
        }
         

      }
      
      //echo $query; exit();
      $ldapconn = @ldap_connect($ipservidor/*"200.239.152.140"*/);
      if($ldapconn){

             $bind = @ldap_bind($ldapconn, $login,$senha/*"cn=arp,dc=ufop,dc=br","th0123"*/);
            
            if($bind){

                    $filter = "(uid=".$username.")"; // this command requires some filter
                    $justthese = array("uid", "cn", "sn", "mail", "gidNumber","telephonenumber" ,"userPassword"); //the attributes to pull, which is much more efficient than pulling all attributes if you don't do this
                    $sr = ldap_search($ldapconn,$complemento/*"dc=ufop,dc=br"*/, $filter, $justthese);
                    $entry = ldap_get_entries($ldapconn, $sr);

                    if($entry['count']>0){
                        $filter2 = "(gidNumber=".$entry[0]['gidnumber'][0].")";
                        $gindnumber=$entry[0]['gidnumber'][0];
                        $justthese2 = array("cn"); //the attributes to pull, which is much more efficient than pulling all attributes if you don't do this
                        $sr2 = ldap_search($ldapconn,"ou=Grupos,".$complemento/*"dc=ufop,dc=br"*/, $filter2, $justthese2);
                        $entry2 = ldap_get_entries($ldapconn, $sr2);
                        $namegrupo=$entry2[0]['cn'][0];
                        //echo $entry[0]['telephonenumber'][0]."<br>";
                           
                        $pwd = substr($entry[0]['userpassword'][0],5);
                        $password = $this->strToHex(base64_decode($pwd));                 
                       
                        $query=$this->em->getRepository('SytemSGBundle:Admins')->findOneBy(array('cpf'=>$username));
                        $query2=$this->em->getRepository('SytemSGBundle:Grupos')->findOneBy(array('grupo'=>$gindnumber));
                        $query3=$this->em->getRepository('SytemSGBundle:Excecoes')->findOneBy(array('cpf'=>$username));
                        
                        if($query){
                            $roles=array('ROLE_ADMIN');
                        }else if($query2 || $query3){
                            $roles=array('ROLE_USER'); 
                        }else{
                          $userLdap = new UserLDAP(null,null,null,null,null,null);
                          return $userLdap;
                        }
                        $userLdap = new UserLDAP($entry[0]['uid'][0], $entry[0]['cn'][0] . " " . $entry[0]['sn'][0], $entry[0]['mail'][0],$entry[0]['telephonenumber'][0],$roles, $namegrupo,$password);
                        return $userLdap;
                    }
                    else {
                       return $userLdap = $this->valdafile($file2);
                    }
                    $userLdap = new UserLDAP(null,null,null,null,null,null);
                    return $userLdap;
               @ldap_close($ldapconn);
            }
        }
        return $userLdap = $this->valdafile( $file2);
        /*$userLdap = new UserLDAP(null,null,null,null,null);
        return $userLdap;*/
        
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof UserLDAP) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Sytem\Bundle\SGBundle\Entity\UserLDAP';
    }

    private function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return $hex;
}
    
public function valdafile( $file2){
      
      if(file_exists($file2)){
                      
        $row=file($file2);           
        $tipo=$row[4];                      
        $nome=$row[2];
        $email=$row[0];
        $pass=$row[1];  
        $password = trim($row[1]);
        $role=array('ROLE_SUPER_ADMIN');
        //$role=array('ROLE_USER');
        $telefone=$row[3];
                        //echo $password;exit();
        $userLdap = new UserLDAP('10010010000', $tipo, $email,$telefone,$role,'Master',$password);
        //print_r($userLdap); exit();
        return $userLdap; 

      }
       $userLdap = new UserLDAP(null,null,null,null,null,null);
       return $userLdap;
   }
//put your code here
}

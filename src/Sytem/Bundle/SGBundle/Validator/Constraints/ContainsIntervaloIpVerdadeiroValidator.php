<?php

namespace Sytem\Bundle\SGBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use Symfony\Component\Validator\ConstraintValidator;
//use Doctrine\ORM\EntityManager;
//use Symfony\Component\DependencyInjection\ContainerInterface;

//use Sytem\Bundle\SGBundle\Entity\IntervalosIps;
//use Sytem\Bundle\SGBundle\Form\IntervalosIpsType;

//use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareComand;




/**
* @Annotation
* 
*/


class ContainsIntervaloIpVerdadeiroValidator extends ConstraintValidator {	

    /*protected $container;

    public function __construct(ContainerInterface $container) { // i guess it's EntityManager the type
           $this->$container = $container;
    } 
    */

   	public function validate ($intIp , Constraint $constraint)
	{

	 //$repositorio=$this->container->get('doctrine')->getEntityManager('default');
	 //$result=$repositorio->getRepository('')->findAll();
	 	
	 if (!preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',$intIp, $matches )) {
	  	
				$this->context->addViolation($constraint->message, array('%mac_address%'=> $intIp)); 
	 }
	 else if (preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',$intIp,$matches)) {
           
                     
          	$ipValidate=explode(".",$intIp);

			$ipValidate[0];
			$ipValidate[1];
			$ipValidate[2];
			$ipValidate[3];
			$ipfaixa=$ipValidate[0].".".$ipValidate[1].".".$ipValidate[2];
            
			if(!strcmp($ipValidate[0],"000")||!strcmp($ipValidate[0],"00")||!strcmp($ipValidate[0],"0")){
				$this->context->addViolation($constraint->message, array('%mac_address%'=> $intIp)); 
			}else if((!strcmp($ipValidate[0],"000")||!strcmp($ipValidate[0],"00")||!strcmp($ipValidate[0],"0"))&&($ipValidate[3]>254 || $ipValidate[3]<1)){
                $this->context->addViolation($constraint->message, array('%mac_address%'=> $intIp));
			}else if($ipValidate[3]>254 || $ipValidate[3]<1){
				$this->context->addViolation($constraint->message, array('%mac_address%'=> $intIp));
			}
			/*else if(!$result){
				$this->context->addViolation($constraint->message, array('%mac_address%'=> $intIp));
			}*/
		}

	}

	/*public function getTargets(){
		return self:: CLASS_CONSTRAINT;
	}
    public function validateBy(){
		return 'previus_value';
    
	}*/
  
	
}
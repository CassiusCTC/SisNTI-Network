<?php

namespace Sytem\Bundle\SGBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


/**
* @Annotation
*
*/


class ContainsIntervaloIpValidator extends ConstraintValidator
{
	
	public function validate ($intIp , Constraint $constraint)
	{
	 if (!preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9]|[01]?[0-9][0-9]?)\.){2}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',$intIp, $matches )) {
	  //codigo
				$this->context->addViolation($constraint->message, array('%mac_address%'=> $intIp)); 
		}
		else if (preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9]|[01]?[0-9][0-9]?)\.){2}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',$intIp,$matches)) {

			$ipValidate=explode(".",$intIp);
			$ipValidate[0];
			$ipValidate[1];
			$ipValidate[2];
			if(!strcmp($ipValidate[0],"000")||!strcmp($ipValidate[0],"00")||!strcmp($ipValidate[0],"0")){
				$this->context->addViolation($constraint->message, array('%mac_address%'=> $intIp)); 
			}
		}

	}
}
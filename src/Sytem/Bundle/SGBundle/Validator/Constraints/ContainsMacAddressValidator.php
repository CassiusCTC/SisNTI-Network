<?php

namespace Sytem\Bundle\SGBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
* @Annotation
*
*/


class ContainsMacAddressValidator extends ConstraintValidator
{
	
	public function validate ($mac_value , Constraint $constraint)
	{
		if (!preg_match('/^([0-9a-fA-F]{2}[:-]){5}([0-9a-fA-F]{2})$/',$mac_value, $matches )) {
			# code..
				$this->context->addViolation($constraint->message, array('%mac_address%'=> $mac_value)); 
		}

	}


}
<?php

namespace Sytem\Bundle\SGBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
* @Annotation
*
*/


class ContainsCpfValidator extends ConstraintValidator
{
	///^(([0-9]{3}.[0-9]{3}.[0-9]{3}-[0-9]{2})|([0-9]{11}))$/ valida com caracters especiais e sem
	public function validate ($cpf , Constraint $constraint)
	{
		if (!preg_match('/^([0-9]{11})$/',$cpf, $matches )) {
			# code..
				$this->context->addViolation($constraint->message, array('%mac_address%'=> $cpf)); 
		}

	}


}
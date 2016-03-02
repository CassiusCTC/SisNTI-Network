<?php

namespace Sytem\Bundle\SGBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */

class ContainsIntervaloIpVerdadeiro extends Constraint
{
	public $message = 'O valor especificado como IP "%mac_address%" esta incorreto ou nao pertence a faixa de IP cadatrado.';
    
   
}
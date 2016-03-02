<?php
namespace Sytem\Bundle\SGBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */

class ContainsSubnet extends Constraint
{
	public $message = 'O valor especificado como IP para a subnet ou netmask "%mac_address%" esta incorreto!!!';
    
   
}

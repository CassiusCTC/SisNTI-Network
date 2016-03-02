<?php

namespace Sytem\Bundle\SGBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */

class ContainsIntervaloIp extends Constraint
{
	public $message = 'O Intervalo "%mac_address%" informado não obedesse ao padrão. Provavelmente foram informados caracteres invalidos.';

}
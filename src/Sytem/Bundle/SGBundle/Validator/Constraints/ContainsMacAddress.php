<?php

namespace Sytem\Bundle\SGBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */

class ContainsMacAddress extends Constraint
{
	public $message = 'O Mac address "%mac_address%" informado não obedesse ao padrão. Provavelmente foram informados caracteres fora do intervalo de (A-F/a-f).';

}
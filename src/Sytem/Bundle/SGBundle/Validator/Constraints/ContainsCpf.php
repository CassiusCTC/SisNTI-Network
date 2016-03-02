<?php

namespace Sytem\Bundle\SGBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */

class ContainsCpf extends Constraint
{
	public $message = 'O Cpf "%mac_address%" informado não obedesse ao padrão (99999999999) com 11 números. Provavelmente foram informados caracteres especiais como no exemplo (. e -) 999.999.999-99 .';

}
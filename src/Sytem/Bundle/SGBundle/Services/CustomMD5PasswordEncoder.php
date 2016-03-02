<?php

namespace Sytem\Bundle\SGBundle\Services;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class CustomMD5PasswordEncoder implements PasswordEncoderInterface
{
    public function __construct() {
    }

    public function encodePassword($raw, $salt) {

        return md5($raw);

    }

    public function isPasswordValid($encoded, $raw, $salt) {
    	//echo md5($raw);
    	//echo $encoded;exit();
        return md5($raw) == $encoded;

    }
}

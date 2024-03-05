<?php

namespace App\Security; 

use Symfony\Component\Security\Core\Exception\LockedException;

class CustomUserBlockedException extends LockedException
{
    public function __construct($message = 'Your account is temporarily blocked.', int $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
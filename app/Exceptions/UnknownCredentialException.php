<?php

namespace App\Exceptions;

use Exception;

class UnknownCredentialException extends Exception
{
    protected $message = 'Badge non reconnu.';
}

<?php
namespace Payments;

/**
 * Validation Exception
 */
class TurnkeyTokenException extends \Exception{
    protected $message = 'A token error occurred';
    protected $code = '-2000';
}
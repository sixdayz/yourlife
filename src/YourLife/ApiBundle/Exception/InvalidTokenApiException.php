<?php

namespace YourLife\ApiBundle\Exception;

use YourLife\ApiBundle\Enum\ApiExceptionType;

class InvalidTokenApiException extends ApiException
{
    public function __construct($httpCode = 400, $type = ApiExceptionType::INVALID_TOKEN,
                                $message = 'Неверный токен', array $details = [])
    {
        parent::__construct($httpCode, $type, $message, $details);
    }
}
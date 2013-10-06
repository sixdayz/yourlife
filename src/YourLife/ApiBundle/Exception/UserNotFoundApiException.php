<?php

namespace YourLife\ApiBundle\Exception;

use YourLife\ApiBundle\Enum\ApiExceptionType;

class UserNotFoundApiException extends ApiException
{
    public function __construct($httpCode = 404, $type = ApiExceptionType::USER_NOT_FOUND,
                                $message = 'Пользователь не найден', array $details = [])
    {
        parent::__construct($httpCode, $type, $message, $details);
    }
}
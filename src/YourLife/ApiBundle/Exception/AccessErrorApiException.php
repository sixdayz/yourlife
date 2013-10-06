<?php

namespace YourLife\ApiBundle\Exception;

use YourLife\ApiBundle\Enum\ApiExceptionType;

class AccessErrorApiException extends ApiException
{
    public function __construct($httpCode = 403, $type = ApiExceptionType::ACCESS_ERROR,
                                $message = 'Недостаточно прав для выполнения операции', array $details = [])
    {
        parent::__construct($httpCode, $type, $message, $details);
    }
}
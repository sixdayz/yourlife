<?php

namespace YourLife\ApiBundle\Exception;

use YourLife\ApiBundle\Enum\ApiExceptionType;

class MissionNotFoundApiException extends ApiException
{
    public function __construct($httpCode = 404, $type = ApiExceptionType::MISSION_NOT_FOUND,
                                $message = 'Миссия не найдена', array $details = [])
    {
        parent::__construct($httpCode, $type, $message, $details);
    }
}
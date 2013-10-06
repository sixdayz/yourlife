<?php

namespace YourLife\ApiBundle\Exception;

use YourLife\ApiBundle\Enum\ApiExceptionType;

class MissionNotFoundApiException extends ApiException
{
    protected $httpCode;

    /** @var ApiExceptionDetail[] */
    protected $details;

    protected $type;

    public function __construct($httpCode = 404, $type = ApiExceptionType::MISSION_NOT_FOUND,
                                $message = 'Миссия не найдена', array $details = [])
    {
        parent::__construct($message);
        $this->httpCode = $httpCode;
        $this->details = $details;
        $this->type = $type;
    }
}
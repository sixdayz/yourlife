<?php

namespace YourLife\ApiBundle\Exception;

use YourLife\ApiBundle\Enum\ApiExceptionType;

class InvalidTokenApiException extends ApiException
{
    protected $httpCode;

    /** @var ApiExceptionDetail[] */
    protected $details;

    protected $type;

    public function __construct($httpCode = 400, $type = ApiExceptionType::INVALID_TOKEN,
                                $message = 'Неверный токен', array $details = [])
    {
        parent::__construct($message);
        $this->httpCode = $httpCode;
        $this->details = $details;
        $this->type = $type;
    }
}
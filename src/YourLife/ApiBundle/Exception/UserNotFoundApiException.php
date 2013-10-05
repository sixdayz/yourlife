<?php

namespace YourLife\ApiBundle\Exception;

use YourLife\ApiBundle\Helper\ApiExceptionType;

class UserNotFoundApiException extends \Exception
{
    protected $httpCode;

    /** @var ApiExceptionDetail[] */
    protected $details;

    protected $type;

    public function __construct($httpCode = 404, $type = ApiExceptionType::USER_NOT_FOUND,
                                $message = 'Пользователь не найден', array $details = [])
    {
        parent::__construct($message);
        $this->httpCode = $httpCode;
        $this->details = $details;
        $this->type = $type;
    }
}
<?php

namespace YourLife\ApiBundle\Exception;

use YourLife\ApiBundle\Enum\ApiExceptionType;

class AccessErrorApiException extends ApiException
{
    protected $httpCode;

    /** @var ApiExceptionDetail[] */
    protected $details;

    protected $type;

    public function __construct($httpCode = 403, $type = ApiExceptionType::ACCESS_ERROR,
                                $message = 'Недостаточно прав для выполнения операции', array $details = [])
    {
        parent::__construct($message);
        $this->httpCode = $httpCode;
        $this->details = $details;
        $this->type = $type;
    }
}
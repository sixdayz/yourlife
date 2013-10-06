<?php

namespace YourLife\ApiBundle\Exception;

use YourLife\ApiBundle\Enum\ApiExceptionType;

class PhotoUploadApiException extends ApiException
{
    protected $httpCode;

    /** @var ApiExceptionDetail[] */
    protected $details;

    protected $type;

    public function __construct($httpCode = 500, $type = ApiExceptionType::ERROR_PHOTO_UPLOAD,
                                $message = 'Ошибка при загрузке фотографии', array $details = [])
    {
        parent::__construct($message);
        $this->httpCode = $httpCode;
        $this->details = $details;
        $this->type = $type;
    }
}
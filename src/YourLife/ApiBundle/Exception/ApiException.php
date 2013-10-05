<?php

namespace YourLife\ApiBundle\Exception;

class ApiException extends \Exception
{
    protected $httpCode;

    /** @var ApiExceptionDetail[] */
    protected $details;

    protected $type;

    public function __construct($httpCode = 500, $type = '', $message = '', array $details = [])
    {
        parent::__construct($message);
        $this->httpCode = $httpCode;
        $this->details = $details;
        $this->type = $type;
    }

    private function validateDetails()
    {
        foreach ($this->details as $d) {
            if ( ! ($d instanceof ApiExceptionDetail)) {
                throw new \InvalidArgumentException('$details должно быть массивом экземпляров ApiExceptionDetail');
            }
        }
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @return ApiExceptionDetail[]
     */
    public function getDetails()
    {
        $this->validateDetails();
        return $this->details;
    }

    public function addDetail(ApiExceptionDetail $detail)
    {
        $this->details[] = $detail;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }
} 
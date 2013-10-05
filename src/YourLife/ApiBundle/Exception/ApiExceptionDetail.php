<?php

namespace YourLife\ApiBundle\Exception;

class ApiExceptionDetail
{
    protected $code;

    protected $message;

    protected $param;

    public function __construct($code, $message, $param = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->param = $param;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getParam()
    {
        return $this->param;
    }

    /**
     * Возвращает ассоциативный массив ключ => значение,
     * где ключ - поле объекта, значение - значение поля объекта
     * @return array
     */
    public function toArray()
    {
        return [
            'code'      => $this->getCode(),
            'message'   => $this->getMessage(),
            'param'     => $this->getParam()
        ];
    }
} 
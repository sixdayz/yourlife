<?php

namespace YourLife\ApiBundle\Helper;

class ApiExceptionType {

    const INVALID_TOKEN = 'your_life_api_invalid_token';
    const ERROR_TOKEN_CREATE = 'your_life_api_error_token_create';

    const USER_NOT_FOUND = 'your_life_api_user_not_found';
    const ERROR_USER_CREATE = 'your_life_api_error_user_create';
    const USER_ALREADY_EXISTS = 'your_life_api_user_already_exists';
}
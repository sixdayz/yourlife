<?php

namespace YourLife\ApiBundle\Enum;

class ApiExceptionType {

    const INVALID_TOKEN = 'your_life_api_invalid_token';
    const ERROR_TOKEN_CREATE = 'your_life_api_error_token_create';

    const USER_NOT_FOUND = 'your_life_api_user_not_found';
    const ERROR_USER_CREATE = 'your_life_api_error_user_create';
    const USER_ALREADY_EXISTS = 'your_life_api_user_already_exists';

    const ACCESS_ERROR = 'your_life_api_access_error';

    const MISSION_NOT_FOUND = 'your_life_api_mission_not_found';
    const ERROR_MISSION_RESULT_CREATE = 'your_life_api_error_create_mission_result';

    const ERROR = 'your_life_api_internal_error';
    const ERROR_PHOTO_UPLOAD = 'your_life_api_error_photo_upload';
    const ERROR_MISSION_RESULT_UPDATE = 'your_life_api_error_mission_result_update';
}
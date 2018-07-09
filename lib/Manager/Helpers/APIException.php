<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/29/18
 * Time: 8:17 PM
 */

namespace Manager\Helpers;


class APIException extends \Exception
{
    const AUTHENTICATION_ERROR          = 401;
    const NOT_FOUND                     = 404;
    const UNSUPPORTED_MEDIA_TYPE        = 415;
    const VALIDATION_ERROR              = 429;

    const EMAIL_PASSWORD_NOT_FOUND      = 1;
    const EMAIL_PASSWORD_WRONG          = 2;
    const USERID_NOT_FOUND              = 3;
    const USER_NOT_CONFIRMED            = 4;


}
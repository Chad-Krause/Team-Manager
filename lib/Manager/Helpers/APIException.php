<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/29/18
 * Time: 8:17 PM
 */

namespace Helpers;


class APIException extends \Exception
{
    const AUTHENTICATION_ERROR          = 401;
    const NOT_FOUND                     = 404;
    const UNSUPPORTED_MEDIA_TYPE        = 415;
    const VALIDATION_ERROR              = 429;

}
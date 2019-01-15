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
    const NOT_FOUND_MSG                 = "The endpoint you are trying to reach does not exist.";
    const UNSUPPORTED_MEDIA_TYPE        = 415;
    const VALIDATION_ERROR              = 429;

    const EMAIL_PASSWORD_NOT_FOUND      = 1;
    const EMAIL_PASSWORD_WRONG          = 2;
    const USERID_NOT_FOUND              = 3;
    const USER_NOT_CONFIRMED            = 4;
    const PUNCH_FAILED                  = 5;
    const INVALID_PIN                   = 6;
    const INVALID_PIN_MSG               = 'Invalid Pin';
    const UNABLE_TO_PUNCH_IN            = 7;
    const UNABLE_TO_PUNCH_IN_MSG        = 'Unable to punch in. Most likely an error in the database (such as already clocked in).';
    const UNABLE_TO_PUNCH               = 8;
    const UNABLE_TO_PUNCH_MSG           = 'Unable to punch in or out.';
    const USER_ALREADY_EXISTS           = 9;
    const USER_ALREADY_EXISTS_MSG       = 'User already exists';
    const PASSWORD_CONFIRMATION_FAIL    = 10;
    const PASSWORD_CONFIRMATION_FAIL_MSG= 'Password confirmation failed';
    const EMAIL_NOT_FOUND               = 11;
    const EMAIL_NOT_FOUND_MSG           = 'Email not found';
    const VALIDATOR_NOT_FOUND           = 12;
    const VALIDATOR_NOT_FOUND_MSG       = 'Validator not found';
    const NOT_LOGGED_IN                 = 401;
    const NOT_LOGGED_IN_MSG             = 'Not logged in';
    const LOGIN_FOR_ADMIN_MENTOR        = 13;
    const LOGIN_FOR_ADMIN_MENTOR_MSG    = 'This login is for Admins and Mentors only. The attempted user is not an Admin or Mentor';
    const PIN_NOT_SET                   = 14;
    const PIN_NOT_SET_MSG               = 'User has not set their pin yet';


    const EMAIL_PASSWORD_NOT_SET = 'Email or password not set.';
    const INCORRECT_LOGIN = 'Email or Password is incorrect';
    const NO_USERID = 'No UserId Supplied';
    const INVALID_REQUEST = 'Invalid API Request';
    const INELIGIBLE_USER = 'Not eligible to receive that user\'s information';
    const UNCONFIRMED_USER = 'This user has not been confirmed by an admin yet!';
    const REQUIRED_KEYS_ERROR_MSG = 'Required keys are missing';
    const IMAGE_NOT_FOUND = 'Image not found. Either the image requested doesn\'t exist or the current user doesn\'t have permission to view it.';


}
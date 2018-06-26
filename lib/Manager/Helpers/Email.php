<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 4/7/18
 * Time: 4:29 PM
 */

namespace Manager\Helpers;

/**
 * Email adapter class
 */
class Email
{
    public function mail($to, $subject, $message, $headers) {
        mail($to, $subject, $message, $headers);
    }

}

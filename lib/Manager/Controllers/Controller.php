<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 7/2/18
 * Time: 12:45 AM
 */

namespace Controllers;

use Manager\Models\User;

class Controller
{

    private $hasAccess; ///< Array of roleids

    public function __construct($roles = [0]) // 0 = no permissions required
    {
        $this->hasAccess = $roles;
    }

    /**
     * Determines if the user has access;
     * @param User $user
     * @return bool
     */
    public function hasPermission(User $user = null)
    {
        if($user !== null)
        {
            return in_array($user->getRole(), $this->hasAccess) || in_array(0, $this->hasAccess);
        }
    }
}
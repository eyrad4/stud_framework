<?php

namespace Mindk\Framework\Auth;

/**
 * Class AuthService
 * @package Mindk\Framework\Auth
 */
class AuthService
{
    /**
     * @var null Current user instance
     */
    protected static $user = null;
    protected static $userRole = null;

    /**
     * Set current user
     */
    public static function setUser($user) {

        self::$user = $user;
    }

    /**
     * Get current user instance
     *
     * @return mixed
     */
    public static function getUser() {

        return self::$user;
    }

    /**
     * Get current user instance
     *
     * @return mixed
     */
    public static function getUserRoleName() {

        return self::$userRole;
    }

    /**
     * Get current user instance
     *
     * @return mixed
     */
    public static function setUserRoleName($role) {

        self::$userRole = $role;
    }

    /**
     * Check if current user has requested roles
     *
     * @return bool
     */
    public static function checkRoles($roles) {
        $roles = (array)$roles;
        //$user = AuthService::getUser();
        $role = AuthService::getUserRoleName();
        $userRole = empty($role->name) ? 'guest' : $role->name;

        return in_array($userRole, $roles);
    }
}
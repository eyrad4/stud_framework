<?php

namespace Mindk\Framework\Controllers;

use Mindk\Framework\Exceptions\AuthRequiredException;
use Mindk\Framework\Http\Request\Request;
use Mindk\Framework\Models\UserModel;
use Mindk\Framework\Models\UserRole;
use Mindk\Framework\DB\DBOConnectorInterface;
use Mindk\Framework\Http\Response\JsonResponse;

/**
 * Class UserController
 * @package Mindk\Framework\Controllers
 */
class RoleController
{
    /**
     * Reset password
     *
     * @param Request $request
     * @param UserModel $model
     *
     * @return mixed
     * @throws AuthRequiredException
     */
    public function roleName(Request $request, UserRole $model, DBOConnectorInterface $dbo) {
        if($token = $request->getHeader('X-Auth')){
            if($user = $model->findByToken($token)){
                if($role = $this->roleModel->findByRoleId($user->role)){
                    AuthService::setUserRoleName($role);
                }
                AuthService::setUser($user);
            }
        }
    }
}
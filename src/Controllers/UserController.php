<?php

namespace Mindk\Framework\Controllers;

use Mindk\Framework\Exceptions\AuthRequiredException;
use Mindk\Framework\Http\Request\Request;
use Mindk\Framework\Models\UserModel;
use Mindk\Framework\Models\RoleModel;
use Mindk\Framework\DB\DBOConnectorInterface;

/**
 * Class UserController
 * @package Mindk\Framework\Controllers
 */
class UserController
{
    /**
     * Sign up
     *
     * @param Request $request
     * @param UserModel $model
     *
     * @return mixed
     * @throws AuthRequiredException
     */
    public function register(Request $request, UserModel $model, DBOConnectorInterface $dbo) {
        if( !empty($request->get('login', '', 'string'))
            AND !empty($request->get('password', '', 'string'))
            AND !empty($request->get('name', '', 'string'))){
            if($login = $request->get('login', '', 'string')) {
                $user = $model->findByEmailBeforeSignUp($login);
            }
            if(!empty($user)) {
                throw new AuthRequiredException('User already exist');
            }
            $params = [
                'email' => $login,
                'password' => md5($request->get('password', '', 'string')),
                'name' => $request->get('name', '', 'string')
            ];

            return $model->save($params);
        }else{
            throw new AuthRequiredException('Not all required fields are filled in');
        }
    }

    /**
     * Login through action
     *
     * @param Request $request
     * @param UserModel $model
     *
     * @return mixed
     * @throws AuthRequiredException
     */
    public function login(Request $request, UserModel $model, RoleModel $roleModel, DBOConnectorInterface $dbo) {

        if( !empty($request->get('login', '', 'string'))
            AND !empty($request->get('password', '', 'string'))) {
            if($login = $request->get('login', '', 'string')) {
                $user = $model->findByCredentials($login, $request->get('password', ''));
            }
            if (empty($user)) {
                throw new AuthRequiredException('Bad access credentials provided');
            }
            // Generate new access token and save:
            $user->token = md5(uniqid());
            //$user->tokenExpire = strtotime("+1 day");
            $params = [
                'token' => $user->token,
                //'token_expire' => $user->tokenExpire
            ];

            $model->save($params, '`id`=' . (int)$user->id . '');
            return ['token' => $user->token, 'userId' => $user->id, 'userLogin' => $login, 'userRole' => $roleModel->findByRoleId($user->role)];
        }else{
            throw new AuthRequiredException('Not all required fields are filled in');
        }

    }

    /**
     * Logout
     *
     * @param Request $request
     * @param UserModel $model
     *
     * @return mixed
     * @throws AuthRequiredException
     */
    public function logout(Request $request, UserModel $model, DBOConnectorInterface $dbo) {
        if($userid = $request->get('id', '', 'integer')) {
            $params = [
                'token' => ''
            ];
            return $model->save($params, '`id`=' . (int)$userid . '');
        }else{
            throw new AuthRequiredException('User not exist');
        }
    }

    /**
     * Reset password
     *
     * @param Request $request
     * @param UserModel $model
     *
     * @return mixed
     * @throws AuthRequiredException
     */
    public function resetpassword(Request $request, UserModel $model, DBOConnectorInterface $dbo) {
        if($login = $request->get('login', '', 'string')) {
            $user = $model->findByEmailBeforeSignUp($login);
        }
        if(empty($user)) {
            throw new AuthRequiredException('User not exist');
        }
        $params = [
            'password' => md5($request->get('password', '', 'string'))
        ];
        return $model->save($params);
    }

    /**
     * Get user info by token
     *
     * @param Request $request
     * @param UserModel $model
     *
     * @return mixed
     * @throws AuthRequiredException
     */
    public function userinfo(Request $request, UserModel $model, RoleModel $roleModel, DBOConnectorInterface $dbo) {
        if($token = $request->getHeader('X-Auth')){
            if($user = $model->findByToken($token)){
                return ['token' => $user->token, 'userId' => $user->id, 'login' => $user->email, 'userRole' => $roleModel->findByRoleId($user->role)];
            }else{
                throw new AuthRequiredException('User not exist');
            }

        }else{
            throw new AuthRequiredException('User not exist');
        }
    }
}
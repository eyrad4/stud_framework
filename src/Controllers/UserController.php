<?php

namespace Mindk\Framework\Controllers;

use Mindk\Framework\Exceptions\AuthRequiredException;
use Mindk\Framework\Http\Request\Request;
use Mindk\Framework\Models\UserModel;
use Mindk\Framework\DB\DBOConnectorInterface;

/**
 * Class UserController
 * @package Mindk\Framework\Controllers
 */
class UserController
{
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
    public function login(Request $request, UserModel $model, DBOConnectorInterface $dbo) {

        if($login = $request->get('login', '', 'string')) {

            $user = $model->findByCredentials($login, $request->get('password', ''));
        }

        if(empty($user)) {
            throw new AuthRequiredException('Bad access credentials provided');
        }

        // Generate new access token and save:
        $user->token = md5(uniqid());
        //$user->save();
        //@TODO: REMOVE THIS when UserModel::save() implemented
        $dbo->setQuery("UPDATE `users` SET `token`='".$user->token."' WHERE `id`=".(int)$user->id);

        return $user->token;
    }

    public function logout(Request $request) {
        //@TODO: Implement
    }
}
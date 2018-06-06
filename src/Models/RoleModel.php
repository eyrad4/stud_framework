<?php
/**
 * Created by PhpStorm.
 * User: eyrad4
 * Date: 01.06.2018
 * Time: 15:35
 */

namespace Mindk\Framework\Models;

/**
 * Class UserModel
 * @package Mindk\Framework\Models
 */
class RoleModel extends Model
{
    /**
     * @var string  DB Table name
     */
    protected $tableName = 'users_role';

    /**
     * Find user role by id
     *
     * @param $roleId
     *
     * @return mixed
     */
    public function findByRoleId($roleId){
        $roleId = (int)$roleId;
        $sql = sprintf("SELECT * FROM `%s` WHERE `id`='%s'", $this->tableName, $roleId);

        return $this->dbo->setQuery($sql)->getResult($this);
    }

}
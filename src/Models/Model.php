<?php

namespace Mindk\Framework\Models;

use Mindk\Framework\DB\DBOConnectorInterface;

/**
 * Basic Model Class
 * @package Mindk\Framework\Models
 */
abstract class Model
{
    /**
     * @var string  DB Table name
     */
    protected $tableName = '';

    /**
     * @var string  DB Table primary key
     */
    protected $primaryKey = 'id';

    /**
     * @var null
     */
    protected $dbo = null;

    /**
     * Model constructor.
     * @param DBOConnectorInterface $db
     */
    public function __construct(DBOConnectorInterface $db)
    {
        $this->dbo = $db;
    }

    /**
     * Create new record
     */
    public function create( $data ) {
        //@TODO: Implement this
    }

    /**
     * Read record
     *
     * @param   int Record ID
     *
     * @return  object
     */
    public function load( $id ) {
        $sql = 'SELECT * FROM `' . $this->tableName .' ` WHERE `'.$this->primaryKey.'`='.(int)$id; //!

        return $this->dbo->setQuery($sql)->getResult($this);
    }

    /**
     * Save record state to db
     *
     * @return bool
     */
    public function save($params) {
        $sql = 'INSERT INTO `'.$this->tableName.'`
        (`'.implode('`, `', array_keys($params)).'`)
        VALUES("'.implode('", "', array_values($params)).'")';

        return $this->dbo->setQuery($sql);
    }

    /**
     * Update record state to db
     *
     * @return bool
     */
    public function update($params, $where) {

        if(!empty($params)){
            $updateParams = '';
            foreach($params as $name => $param){
                $updateParams .= '`'.$name.'` = "'.$param.'",';
            }

            $sql = 'UPDATE  `'.$this->tableName.'` SET
            '.rtrim($updateParams, ',').'
            WHERE '.$where.' ';

            return $this->dbo->setQuery($sql);
        }
    }

    /**
     * Delete record from DB
     */
    public function delete( $id ) {
        //@TODO: Implement this
        $sql = 'DELETE FROM `'.$this->tableName.'` WHERE ` '.$this->primaryKey.'`='.(int)$id;
        return $this->dbo->setQuery($sql);
    }

    /**
     * Get list of records
     *
     * @return array
     */
    public function getList() {
        $sql = 'SELECT * FROM `' . $this->tableName . '`';

        return $this->dbo->setQuery($sql)->getList(get_class($this));
    }
}
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
     * @param string|array $data
     * @param bool $ignoreAlias
     * @return string
     */
    protected function prepareFields($data){
        if (\is_array($data)) {
            $tmp = [];
            foreach ($data as $alias => $field) {
                $tmp[] = ($alias !== $field && ! \is_int($alias)) ?
                    ($field . ' as `' . $alias . '`') : $field;
            }
            $data = implode(',', $tmp);
        }
        if (empty($data)) {
            $data = '*';
        }
        return $data;
    }

    /**
     * @param array|string $data
     * @return string
     */
    protected function prepareWhere($data) {
        if (\is_array($data)) {
            if ($this->arrayOnlyNumeric(array_keys($data)) === true) {
                $data = implode(' ', $data);
            }
        }
        $data = trim($data);
        if (!empty($data) && stripos($data, 'WHERE') !== 0) {
            $data = "WHERE {$data}";
        }
        return $data;
    }
    /**
     * @param string $data
     * @return string
     */
    protected function prepareOrder($data) {
        $data = trim($data);
        if (! empty($data) && stripos($data, 'ORDER') !== 0) {
            $data = "ORDER BY {$data}";
        }
        return $data;
    }
    /**
     * @param string $data
     * @return string
     */
    protected function prepareLimit($data) {
        $data = trim($data);
        if (! empty($data) && stripos($data, 'LIMIT') !== 0) {
            $data = "LIMIT {$data}";
        }
        return $data;
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
        $sql = 'SELECT * FROM `' . $this->tableName .'` WHERE `'.$this->primaryKey.'`='.(int)$id; //!

        return $this->dbo->setQuery($sql)->getResult($this);
    }

    /**
     * @param string|array $fields
     * @param string $table
     * @param array|string $where
     * @return mixed
     */
    public function save($params, $where = '')
    {
        if ($where === '') {
            $mode = 'insert';
        } else {
            if (empty($this->select('*', $where))) {
                $mode = 'insert';
            } else {
                $mode = 'update';
            }
        }

        return ($mode === 'insert') ? $this->insert($params) : $this->update($params, $where);
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
        $sql = 'SELECT * FROM `' . $this->tableName . '` ';

        return $this->dbo->setQuery($sql)->getList(get_class($this));
    }

    /**
     * Get list of records witch same sql commands
     *
     * @return array
     */
    public function select($fields = '*', $where = '', $orderBy = '', $limit = ''){

        $fields = $this->prepareFields($fields);
        $where = $this->prepareWhere($where);
        $orderBy = $this->prepareOrder($orderBy);
        $limit = $this->prepareLimit($limit);

        $sql = 'SELECT '.$fields.' FROM `' . $this->tableName . '` '.$where.' '.$orderBy.' '.$limit.' ';

        return $this->dbo->setQuery($sql)->getList(get_class($this));
    }
}
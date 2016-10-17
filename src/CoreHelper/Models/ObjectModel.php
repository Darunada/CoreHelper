<?php
/**
 * Created by PhpStorm.
 * User: Lea
 * Date: 10/16/2016
 * Time: 7:03 PM
 */

namespace CoreHelper\Models;

use CoreHelper\Exceptions\RecordNotFoundException;

/**
 * Base model for creating classes
 * @author Lea
 */
class ObjectModel extends TableModel
{
    protected $_class_name = null;

    public function __construct()
    {
        parent::__construct();
        $this->_fetch_class();
    }

    /**
     * Fetches the class from the pluralised model name.
     *
     * @author Lea Fairbanks
     */
    private function _fetch_class()
    {
        if ($this->_class_name == NULL) {
            $class             = preg_replace('/(_model)?$/', '', get_class($this));
            $this->_class_name = str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($class))));
        }
    }

    public function set_class($class)
    {
        $this->_class_name = $class;
    }

    /**
     * find($primary) retrieve the object corresponding
     * to the specified primary keys.
     *
     * SELECT * FROM table WHERE (table.id = key) LIMIT 1
     *
     * @param array|core_id $keys A primary key value, or an array of values.
     * @returns Object
     * @throws RecordNotFoundException unless a matching record is found for all of the supplied primary keys.
     */
    public function find($keys)
    {
        $return_array = is_array($keys);

        if (!is_array($keys)) {
            $keys = array(0 => $keys);
        }

        $results = array();

        foreach ($keys as &$key) {
            $row = $this->get($key);

            if (empty($row)) {
                throw new RecordNotFoundException("Unable to find primary key value '$key' in {$this->_table}");
            }

            $results[] = new $this->_class_name((array) $row);
        } unset($key);


        if (count($results) > 1 || $return_array) {
            return $results;
        } else return reset($results);
    }

    /**
     * all() returns an array of object instances of each row in the table.
     * This is terribly inefficient
     *
     * SELECT * FROM table
     *
     * @returns Array[Objects]
     */
    public function all()
    {

        $results = array();

        $all = $this->get_all();
        foreach ($all as $one) {
            $results[] = new $this->_class_name((array) $one);
        } unset($one);

        return $results;
    }

    /**
     * take() retrieves a record without any implicit ordering.
     *
     * SELECT * FROM table LIMIT $limit
     *
     * @param int $limit The number of results to return
     * @returns Object|Array[Object]|null
     */
    public function take($limit = null)
    {
        $query_result = $this->db->limit($limit? : 1)
            ->get($this->_table)->result();

        $results = array();
        foreach ($query_result as &$row) {
            $results[] = new $this->_class_name((array) $row);
        }

        if ($limit != null) {
            return $results;
        } else return reset($results);
    }

    /**
     * _take() retrieves a record without any implicit ordering.
     * It will throw a RecordNotFound exception if no record is found.
     *
     * SELECT * FROM table LIMIT $limit
     *
     * @returns Object|Array[Object]
     * @throws RecordNotFoundException
     */
    public function _take()
    {
        $row = $this->db->limit(1)->get($this->_table)->row();

        if (empty($row)) {
            throw new RecordNotFoundException("take() failed on {$this->_table}.  Is the table empty?");
        }

        return new $this->_class_name((array) $row);
    }

    /**
     * first() retrieves the first record ordered by the primary key
     *
     * SELECT * FROM table ORDER BY table.id ASC LIMIT 1
     *
     * @param int $limit The number of results to return
     * @returns Object|null
     */
    public function first($limit = null)
    {
        $query_result = $this->db->limit($limit? : 1)
            ->order_by($this->primary_key, 'ASC')
            ->get($this->_table)->result();

        $results = array();
        foreach ($query_result as &$row) {
            $results[] = new $this->_class_name((array) $row);
        }

        if (empty($results)) return null;

        if ($limit != null) {
            return $results;
        } else return reset($results);
    }

    /**
     * _first() retrieves the first record ordered by the primary key
     * It will throw a RecordNotFound exception if no record is found.
     *
     * SELECT * FROM table ORDER BY table.id ASC LIMIT 1
     *
     * @returns Object|Array[Object]
     * @throws RecordNotFoundException
     */
    public function _first()
    {
        $row = $this->db->limit(1)
            ->order_by($this->primary_key, 'ASC')
            ->get($this->_table)->row();

        if (empty($row)) {
            throw new RecordNotFoundException("_first() failed on {$this->_table}.  Is the table empty?");
        }

        return new $this->_class_name((array) $row);
    }

    /**
     * last() retrieves the last record ordered by the primary key
     *
     * SELECT * FROM table ORDER BY table.id DESC LIMIT 1
     *
     * @param int $limit The number of results to return
     * @returns Object|Array[Object]|null
     */
    public function last($limit = null)
    {
        $query_result = $this->db->limit($limit? : 1)
            ->order_by($this->primary_key, 'DESC')
            ->get($this->_table)->result();

        $results = array();
        foreach ($query_result as &$row) {
            $results[] = new $this->_class_name((array) $row);
        }

        if (empty($results)) return null;

        if ($limit != null) {
            return $results;
        } else return reset($results);
    }

    /**
     * _last() retrieves the last record ordered by the primary key
     * It will throw a RecordNotFound exception if no record is found.
     *
     * SELECT * FROM table ORDER BY table.id DESC LIMIT 1
     *
     * @returns Object|Array[Object]
     * @throws RecordNotFoundException
     */
    public function _last()
    {
        $row = $this->db->limit(1)
            ->order_by($this->primary_key, 'DESC')
            ->get($this->_table)->row();

        if (empty($row)) {
            throw new RecordNotFoundException("_last() failed on {$this->_table}.  Is the table empty?");
        }

        return new $this->_class_name((array) $row);
    }

    /**
     * find_many_by retrieves all records matching some conditions
     *
     * @params Array $conditions $key=>$value pair of conditios
     * @returns Array
     */
    public function find_many_by()
    {
        $results = array();
        $query   = call_user_func_array(array(&$this, 'get_many_by'), func_get_args());
        //$query = $this->get_many_by($conditions);

        foreach ($query as &$obj) {
            $results[] = new $this->_class_name((array) $obj);
        } unset($obj);


        return $results;
    }

    public function find_by()
    {
        $results = array();
        $query   = call_user_func_array(array(&$this, 'get_by'), func_get_args());
        if (!empty($query)) {
            return new $this->_class_name((array) $query);
        }

        throw new RecordNotFoundException();
    }

    /**
     * find_by retrieves the first record matching some conditions
     * It will throw a RecordNotFound exception if no record is found.
     *
     * @params Array $conditions $key=>$value pair of conditios
     * @returns Object
     * @throws RecordNotFound
     */
    //public function _find_by();


    public function get_empty()
    {
        return new $this->_class_name();
    }

    public function save(&$entity)
    {
        if ($entity->id == null) {
            $entity->id = $this->insert($entity);
        } else {
            $this->update($entity->id, $entity);
        }
    }
}
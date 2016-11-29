<?php
class jModel {
    protected $table;
    protected $count = false;
    protected $eachCount = 0;
    protected $relations = array();

    private $reldata = array();

    public function __construct($id = 0) {
	$child = get_class($this);
        # FIXME this will cause problems if a table name comes
        # with underscore(_), explode should be replaced by a 
        # better logic considering underscores in table names
	$tableName = explode('_', $child)[1];
	if ($id) {
	    $this->table = ORM::for_table($tableName)->find_one($id);
            $this->loadRelations($id);
        } else {
            $this->table = ORM::for_table($tableName)->create();
            //$this->table = ORM::for_table($tableName)->create();
	}
    }

    /**
     * Loads ORM models if relations are defined.
     *
     * Loads ORM objects based on configuration. There can have two types of
     * directions :
     * 1. p2c (parent to child): The primary key of the current row is used as a foriegn
     * key in another table. p2c can be of two types:
     * 1-1 (one to one)  : loads only one column of destination table. 
     * 1-M (one to many ): loads all matching rows in destination column.
     * 2. c2p (child to parent): This table is using the primary key of of another table.
     * No type field required for this direction.
     *
     * @param int $id id of column
     */
    protected function loadRelations($id) {
        if (empty($this->relations)) {
            return;
        }
 
        foreach ($this->relations as $key => $info) {
            $direction = isset($info['direction']) ? $info['direction'] : 'p2c';
            if ($direction == 'p2c') {
                $table = ORM::for_table($info['table'])
                    ->where($info['column'], $id);
            } elseif ($direction == 'c2p') {
                $this->reldata[$key] = ORM::for_table($info['table'])
                    ->find_one($this->table->$info['column']);
                continue;
            } else {
                continue;
            }

            if ($info['type'] == '1-1') {
                $data = $table->find_one();
            } elseif ($info['type'] == '1-M') {
                $data = $table->find_many();
            }
            $this->reldata[$key] = $data;
        }
    }

    public function __get($column) {
	if (isset($this->table->$column)) {
	    return $this->table->$column;
	} elseif (isset($this->reldata[$column])) {
            return $this->reldata[$column];
        }
	return false;
    }

    public function __set($column, $value) {
	//if (isset($this->table->$column)) {
	//    $this->table->$column = $value;
	//}
	$this->table->$column = $value;
    }

    public function __isset($column) {
        return isset($this->table->$column);
    }

    public static function instance($table, $id = 0) {
        $class = 'Model_'.$table;
        if (!class_exists($class)) {
            throw new Exception("No model for the table $table found");
        }
        if ($id) {
            $model = new $class($id);
        } else {
            $model = new $class();
        }
        return $model;
    }

    public function loadBy($col, $value) {
	$row = $this->table->where($col, $value)->find_one();
        if(is_object($row)){
            $this->count = 1;
            $this->table = $row;
            $this->loadRelations($row->id);
        } else {
            $this->count = 0;
        }
        return $this;
    }

    public function get($col) {
	return $this->table->$col;
    }
    
    // FIXME this probably wont give right result
    // need to fix it
    public function count() {
        if($this->count === false) {
            if (is_object($this->table)) {
                $this->count = 1;
            } elseif (is_array($this->table)) {
                $this->count = count($this->table);
            } else {
                $this->count = 0;
            }
        }
        return $this->count;
    }

    public function loaded() {
        if ($this->count()) {
            return true;
        }
        return false;
    }

    public function each() {
        if ($this->count() && is_array($this->table)) {
            $col = each($this->table);
            //print_r($col);
            return $col[1];
        } elseif (
            $this->count() 
            && is_object($this->table)
            && ($this->count - $this->eachCount++)
        ) {
            return $this->table;
        }
        return false; 
    }

    public function reset() {
        if( is_array($this->table)) {
            reset($this->table);
        }
    }
  
    public function add($data) {
        if(!is_array($data)){
            throw new Exception('Data must be supplied as array');
        }
        $this->table->create();
        foreach($data as $column => $value){
            $this->table->$column = $value;
        }
	$this->table->save();
    }

    public function delete() {
       $this->table->delete(); 
    }

    public function save() {
       $this->table->save();
       return $this->table->id();
    }

    public function resetCounter() {
        $this->count = false;
    }
}

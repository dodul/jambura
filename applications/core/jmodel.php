<?php
class jModel
{
    protected $table = null;
    protected $count = false;
    protected $eachCount = 0;

    public function __construct($id = 0)
    {
	    $child = get_class($this);

        # FIXME this will cause problems if a table name comes
        # with underscore(_), explode should be replaced by a 
        # better logic considering underscores in table names

	    $tableName = explode('_', $child)[1];
	    if ($id) {
	        $this->table = ORM::for_table($tableName)->find_one($id);
        } else {
            $this->table = ORM::for_table($tableName)->create();
            //$this->table = ORM::for_table($tableName)->create();
	    }
    }

    public function __get($column)
    {
	    if (isset($this->table->$column)) {
	        return $this->table->$column;
	    }
	    return false;
    }

    public function __set($column, $value)
    {
	    //if (isset($this->table->$column)) {
	    //    $this->table->$column = $value;
	    //}
	    $this->table->$column = $value;
    }

    public static function instance($table, $id = 0)
    {
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

    public function loadBy($col, $value)
    {
	    $row = $this->table->where($col, $value)->find_one();
        if (is_object($row)) {
            $this->count = 1;
            $this->table = $row;
        } else {
            $this->count = 0;
        }
        return $this;
    }

    public function get($col)
    {
	    return $this->table->$col;
    }
    
    // FIXME this probably wont give right result
    // need to fix it

    public function count()
    {
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

    public function loaded()
    {
        if ($this->count()) {
            return true;
        }
        return false;
    }

    public function each()
    {
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

    public function reset()
    {
        if (is_array($this->table)) {
            reset($this->table);
        }
    }
  
    public function add($data)
    {
        if (!is_array($data)) {
            throw new Exception('Data must be supplied as array');
        }
        $this->table->create();
        foreach ($data as $column => $value) {
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

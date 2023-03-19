<?php
require_once 'abstractmodel.php';
class User extends AbstractModel
{
    private $id;
    private $name;
    private $age;
    private $address;
    private $tax;
    private $salary;

    protected static $tableName = 'users';
    protected static $tableScheema = array(
        'name'              =>  self::SQL_DATA_STR,
        'age'               =>  self::SQL_DATA_INT,
        'address'           =>  self::SQL_DATA_STR,
        'salary'            =>  self::SQL_DATA_INT,
        'tax'               =>  self::SQL_DATA_DECIMAL
    );


    public function __construct($name, $age, $address, $tax, $salary)
    {
        global $connection;
        $this->name = $name;
        $this->age  = $age;
        $this->address = $address;
        $this->tax = $tax;
        $this->salary = $salary;
    }

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function salaryCalc(){
        return $this->salary - ($this->salary * $this->tax / 100);
    }

    public function getTableName(){
        return self::$tableName;
    }
}
<?php

class AbstractModel
{
    const SQL_DATA_BOOL = PDO::PARAM_BOOL;
    const SQL_DATA_STR = PDO::PARAM_STR;
    const SQL_DATA_INT = PDO::PARAM_INT;
    const SQL_DATA_DECIMAL = 4;

    protected function prepareValue()
    {

    }

    private static function buildNameParamsSQL()
    {
        $namedParams = '';
        foreach (static::$tableScheema as $columnName => $type){
            $namedParams .= $columnName . ' = :' . $columnName . ', ';
        }
        return trim($namedParams, ', ');
    }


    public  function create(){
        global $connection;
        $sql = 'INSERT INTO ' . static::$tableName . ' SET ' . self::buildNameParamsSQL();
        $stmt = $connection->prepare($sql);
        foreach (static::$tableScheema as $columnName => $type){
            if ($type == 4){
                $sanitizedVal = filter_var($this->$columnName, FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                $stmt->bindValue(":{$columnName}", $this->$columnName, $sanitizedVal);

            }else{
                $stmt->bindValue(":{$columnName}", $this->$columnName, $type);
            }
        }

        return $stmt->execute();

    }

}
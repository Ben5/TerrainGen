<?php

abstract class ModelBase
{
    protected static $modelName = "bum";

    public final static function
    GetAll()
    {
        $sql = "SELECT * FROM " . static::$modelName;
        $query = DbInterface::NewQuery($sql);

        return $query->TryReadRowArray();
    }

    public final static function
    GetOneById($id)
    {
        $sql = "SELECT * FROM ? WHERE id = ?";
        $query = DbInterface::NewQuery($sql);

        $query->AddStringParam(self::$modelName);
        $query->AddIntegerParam($id);

        return $query->TryReadSingleRow();
    }

    public final static function
    GetEnumValues($columnName)
    {
        $sql = "SELECT COLUMN_TYPE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = ?
                AND COLUMN_NAME = ?";
        $query = DbInterface::NewQuery($sql);
        $query->AddStringParam(static::$modelName);
        $query->AddStringParam($columnName);

        $result = $query->TryReadSingleValue();

        $trimmedResult = str_replace(array('enum(', '\'', ')'), '', $result);
        $resultArray = explode(',', $trimmedResult);
        return $resultArray;
    }

    private static function
    ParamOrDefault($params, $index, $default)
    {
        if( isset( $params[$index] ) )
        { 
            return $params[$index]; 
        }
        else
        {
            if( $default == "error" )
            {
                trigger_error("Param value $index not found!");
            }
            else
            {
                return $default;
            }
        }
    }

}

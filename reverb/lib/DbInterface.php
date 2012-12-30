<?php

class DbInterface
{
    private static $connection;

    public static function
    Connect()
    {
        $conn = new mysqli( SiteConfig::DB_HOST, SiteConfig::DB_USER, SiteConfig::DB_PASS, SiteConfig::DB_DB);
    
        if($conn === FALSE)
        {
            trigger_error("no db connection!");
        }

        return $conn;
    }
   

    public static function
    NewQuery($sql)
    {
        if(!self::$connection)
        {
            self::$connection = self::Connect();
        }

        return new DbQuery($sql, self::$connection); 
    }
}

class DbQuery
{
    private $stmt;
    private $result;
    private $params = array();

    public function
    __construct($sql, $conn)
    {
       $this->stmt = $conn->Prepare($sql); 
       if($this->stmt === FALSE)
       {
           trigger_error("failed to prepare stmt for sql: $sql, error was: ".$conn->error);
       }
    }

    private function
    GetParamTypes()
    {
        $typeString = "";

        foreach($this->params as $param)
        {
            $typeString .= $param['type'];
        }

        return $typeString;
    }

    public function
    AddIntegerParam($param)
    {
        $this->params[] = array( "type" => "i", "value" => $param );
    }
    
    public function
    AddStringParam($param)
    {
        $this->params[] = array( "type" => "s", "value" => $param );
    }

    public function
    AddDecimalParam($param)
    {
        $this->params[] = array( "type" => "d", "value" => $param );
    }

    public function
    GetLastError()
    {
        return $this->stmt->error;
    }

    private function
    TryQuery()
    {
        // bind all params
        $paramsArray = array();
        $paramsArray[] = $this->GetParamTypes();

        $params = $this->params;
            
        if( count($params) > 0 )
        {
            for($i = 0; $i < count($params); $i++)
            {
                $bindName = "param".$i;
                $$bindName = $params[$i]['value'];
                $paramsArray[] =& $$bindName;
            }

            call_user_func_array( array( $this->stmt, "bind_param" ), $paramsArray );
        }

        // execute the query
        $success = $this->stmt->execute();
        return $success;
    }

    public function
    ExecuteInsert($errorMsg)
    {
        if(!$this->TryExecuteInsert())
        {
            trigger_error($errorMsg . " - mysql error: " .$this->GetLastError());
        }
    }

    public function
    TryExecuteInsert()
    {
        if($this->TryQuery())
        {
            return $this->stmt->insert_id;
        }
        return FALSE;
    }

    public function
    TryReadSingleValue()
    {
        if( $this->TryQuery() )
        {
            $this->result = $this->stmt->get_result();
            $row = $this->result->fetch_array(MYSQLI_NUM);

            return $row[0];
        }

        return FALSE;
    } 

    public function
    TryReadSingleRow()
    {
        if( $this->TryQuery() )
        {
            $this->result = $this->stmt->get_result();
            $row = $this->result->fetch_assoc();

            return $row;
        }

        return FALSE;
    } 

    public function
    TryReadRowArray()
    {
        if( $this->TryQuery() )
        {
            $this->result = $this->stmt->get_result();

            $rowArray = $this->result->fetch_all(MYSQLI_ASSOC);

            return $rowArray;
        }

        return FALSE;
    } 
}

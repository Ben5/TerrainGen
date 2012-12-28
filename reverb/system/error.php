<?php

class Error
{
    public static function 
    ErrorHandler(
        $errNo,
        $errStr,
        $errFile,
        $errLine,
        $errContext)
    {
        if(!(error_reporting() & $errNo))
        {
            // this error code is not included in error_reporting
            return;
        }

        echo "<h1>".$errStr."</h1>";
        echo "<br>";
        echo "CallStack:<br>";
        $callstack = debug_backtrace();
        $outputCallstack = "";
        for($i = 1; $i < count($callstack); $i++)
        {
            $level = $callstack[$i];
            $outputCallstack .=
                $level['file'].":".     // file path
                $level['line']." ".     // line number
                $level['function'];     // function name
            $outputCallstack .= "<br>";

        }
        echo $outputCallstack;
        echo "<br>";
        echo "PrettyLocals:<br>";

        foreach($errContext as $var => $value)
        {
            self::PrettyPrintVars($var, $value);
        }

        die();
    }

    private static function
    PrettyPrintVars(
        $varName,
        $value,
        $indentLevel = 0)
    {
        $numSpaces = 4;
        $indentString = "";
        for($i = 0; $i < $indentLevel; $i++)
        {
            for($j = 0; $j < $numSpaces; $j++)
            {
                $indentString .= "&nbsp;";
            }
        }


        if(is_array($value))
        {
            if(count($value) == 0)
            {
                $value = "[]";
            }
            echo $indentString."$".$varName." = [<br>";
            foreach($value as $varNameInner => $valueInner)
            {
                self::PrettyPrintVars($varNameInner, $valueInner, $indentLevel+1);
            }
            echo $indentString."]";
        }
        elseif(is_object($value))
        {
            // handle printing objects
            echo $indentString.print_r($value, TRUE);
        }
        else
        {
            echo $indentString."$".$varName." = ".$value."<br>";
        }
    }


}

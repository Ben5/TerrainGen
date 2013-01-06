<?php

class ComponentBase
{
    public function
    Prepare($action, $params)
    {
        if( !method_exists($this, $action) )
        {
            trigger_error("unknown action: $action");
            exit();
        }

        $this->$action($params);
    }

    protected function 
    ExposeVariable(
        $name,
        $value )
    {
        if( isset($this->outputVars[$name]) )
        {
            trigger_error("duplicate outout variable: $name");
        }

        $this->outputVars[$name] = $value;
    }

    public function
    GetExposedVariables()
    {
        return $this->outputVars;
    }

    private $outputVars = array();
}

<?php

class ComponentBase
{
    private $headVars   = array();
    private $outputVars = array();

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
        $value,
        $isHeadVar = false )
    {
        if ($isHeadVar)
        {
            if (isset($this->headVars[$name]))
            {
                trigger_error("duplicate outout variable: $name");
            }

            $this->headVars[$name] = $value;
        }
        else
        {
            if (isset($this->outputVars[$name]))
            {
                trigger_error("duplicate outout variable: $name");
            }

            $this->outputVars[$name] = $value;
        }

    }

    public function
    GetHeadVariables()
    {
        $headVarString = '';

        if( !isset($this->headVars['title']) )
        {
            $this->headVars['title'] = SiteConfig::DEFAULT_HEAD_TITLE;
        }

        foreach($this->headVars as $name => $value)
        {
            $headVarString .= '<'.$name.'>'.$value.'</'.$name.'>';
        }

        return $headVarString;
    }

    public function
    GetExposedVariables()
    {
        return $this->outputVars;
    }
}

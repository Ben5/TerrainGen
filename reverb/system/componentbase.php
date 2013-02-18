<?php

use TerrainGen\SiteConfig;

class ComponentBase
{
    private $headVars   = array();
    private $outputVars = array();
    private $viewName   = null;
    private $onlyTemplate = false;

    public function
    Prepare($action, $params)
    {
        if (!method_exists($this, $action))
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
                trigger_error("duplicate output variable: $name");
            }

            $this->headVars[$name] = $value;
        }
        else
        {
            if (isset($this->outputVars[$name]))
            {
                trigger_error("duplicate output variable: $name");
            }

            $this->outputVars[$name] = $value;
        }

    }

    public function
    GetHeadVariables()
    {
        $headVarString = '';

        if (!isset($this->headVars['title']))
        {
            $this->headVars['title'] = SiteConfig::DEFAULT_HEAD_TITLE;
        }

        foreach($this->headVars as $name => $value)
        {
            $headVarString .= '<'.$name.'>'.$value.'</'.$name.">\n";
        }

        return $headVarString;
    }

    public function
    GetExposedVariables()
    {
        return $this->outputVars;
    }

    public function
    SetViewName($viewName)
    {
        $this->viewName = $viewName;
    }

    public function
    GetViewName()
    {
        return $this->viewName;
    }

    public function
    SetOnlyTemplate($onlyTemplate)
    {
        $this->onlyTemplate = $onlyTemplate;
    }

    public function
    GetOnlyTemplate()
    {
        return $this->onlyTemplate;
    }
}

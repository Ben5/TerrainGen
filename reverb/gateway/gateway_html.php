<?php
require_once(__DIR__."/gateway_base.php");

class GatewayHtml extends GatewayBase
{
    public function
    ConstructOutput()
    {      
        if( !is_readable($this->siteRoot."/views/$this->componentName.php") )
        {
            trigger_error("cannot find specified view: $this->componentName");
        }

        $outputVars = $this->componentInstance->GetExposedVariables();
        foreach($outputVars as $name => $value)
        {
            $$name = $value;
        }

        $headVarString = $this->componentInstance->GetHeadVariables();

        include $this->siteRoot."/views/default_header.php";

        if(file_exists($this->siteRoot."/views/$this->componentName.css"))
        {
            $css = file_get_contents($this->siteRoot."/views/$this->componentName.css");
            
            echo "<style type='text/css'>".$css."</style>";
        }
    
        include $this->siteRoot."/views/$this->componentName.php";

        include $this->siteRoot."/views/default_footer.php";

    }

}


$gateway = new GatewayHtml;
$gateway->Prepare();
$gateway->ConstructOutput();

<?php
require_once("/opt/site/reverb/gateway/gateway_base.php");

class GatewayHtml extends GatewayBase
{
    public function
    ConstructOutput()
    {      
        if( !is_readable(SiteConfig::SITE_ROOT."/views/$this->componentName.php") )
        {
            trigger_error("cannot find specified view: $this->componentName");
        }

        $outputVars = $this->componentInstance->GetExposedVariables();
        foreach($outputVars as $name => $value)
        {
            $$name = $value;
        }

        if( !isset($headTitle) )
        {
            $headTitle = SiteConfig::DEFAULT_HEAD_TITLE;
        }

        include SiteConfig::SITE_ROOT."/views/default_header.php";
        if(file_exists(SiteConfig::SITE_ROOT."/views/$this->componentName.css"))
        {
            $css = file_get_contents(SiteConfig::SITE_ROOT."/views/$this->componentName.css");
            
            echo "<style type='text/css'>".$css."</style>";
        }
    
        include SiteConfig::SITE_ROOT."/views/$this->componentName.php";

    }

}


$gateway = new GatewayHtml;
$gateway->Prepare();
$gateway->ConstructOutput();

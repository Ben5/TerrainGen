<?php
require_once(__DIR__."/gateway_base.php");

class GatewayHtml extends GatewayBase
{
    public function
    ConstructOutput()
    {      
        $viewName = $this->componentInstance->GetViewName();
        if (is_null($viewName))
        {
            $viewName = $this->componentName;
        }

        $onlyTemplate = $this->componentInstance->GetOnlyTemplate();

        if ($onlyTemplate)
        {
            include $this->siteRoot.'/views/'.$viewName.'.php';
        }
        else
        {
            if( !is_readable($this->siteRoot.'/views/'.$viewName.'.php') )
            {
                trigger_error('cannot find specified view: '.$viewName);
            }

            // get any variables that the Component exposed for use in the View
            $outputVars = $this->componentInstance->GetExposedVariables();
            foreach($outputVars as $name => $value)
            {
                $$name = $value;
            }

            $headVarString = $this->componentInstance->GetHeadVariables();

            // include any page-specific stylesheets
            if(file_exists($this->siteRoot.'/views/'.$this->componentName.'.css'))
            {
                $css = file_get_contents($this->siteRoot.'/views/'.$this->componentName.'.css');

                $headVarString .= '<style type="text/css" >'.$css."</style>\n";
            }

            // Include the jquery code
            $jquery = file_get_contents('/opt/site/reverb/lib/jquery-1.9.0.min.js');
            $headVarString .= '<script type="text/javascript">'.$jquery."</script>\n";

            // include any page-specific javascript
            if(file_exists($this->siteRoot.'/views/'.$this->componentName.'.js'))
            {
                $javascript = file_get_contents($this->siteRoot.'/views/'.$this->componentName.'.js');
                $headVarString .= '<script type="text/javascript">'.$javascript."</script>\n";
            }

            include $this->siteRoot.'/views/default_header.php';
            include $this->siteRoot.'/views/'.$viewName.'.php';
            include $this->siteRoot.'/views/default_footer.php';
        }
    }
}


$gateway = new GatewayHtml;
$gateway->Prepare();
$gateway->ConstructOutput();

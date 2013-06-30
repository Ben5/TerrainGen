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
            $cssHref = '/css/'.$this->componentName.'.css';
            if ($this->projectName != '') {
                $cssHref = '/'.$this->projectName.$cssHref;
            }
            $headVarString .= '<link rel="stylesheet" type="text/css" href="'.$cssHref.'" />'."\n";

            // Include the jquery code
            $jqueryFiles = array('jquery-1.9.0.min.js',
                                 'jquery-ui.min.js', 
                                 'jquery.ui.accordion.min.js', 
                                 );
            foreach($jqueryFiles as $filename)
            {
                $headVarString .= '<script type="text/javascript" src="/js/'.$filename.'">'."</script>\n";
            }

            // include any page-specific javascript
            $jsSrc = '/js/'.$this->componentName.'.js';
            if ($this->projectName != '') {
                $jsSrc = '/'.$this->projectName.$jsSrc;
            }
            $headVarString .= '<script type="text/javascript" src="'.$jsSrc.'"></script>'."\n";

            include $this->siteRoot.'/views/default_header.php';
            include $this->siteRoot.'/views/'.$viewName.'.php';
            include $this->siteRoot.'/views/default_footer.php';
        }
    }
}


$gateway = new GatewayHtml;
$gateway->Prepare();
$gateway->ConstructOutput();

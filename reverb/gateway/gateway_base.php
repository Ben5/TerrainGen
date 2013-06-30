<?php

use TerrainGen\SiteConfig;

include "/opt/git/TerrainGen/site/config/site.php";
include SiteConfig::REVERB_ROOT."/system/error.php";

set_error_handler("Error::ErrorHandler" );

class GatewayBase 
{
    protected $siteRoot;
    protected $projectName;
    protected $componentName;
    protected $componentInstance;

    public function prepare()
    {
        $this->componentName = '';
        $this->siteRoot = SiteConfig::SITE_ROOT;
        $this->projectName = '';

        $action = 'Index';
        $params = array();
        
        foreach( $_REQUEST as $param=>$val )
        {
            switch( $param )
            {
                case "_project":
                {
                    $this->projectName = $val;
                }
                break;

                case "_component":
                {
                    $this->componentName = $val;
                }
                break;

                case "_action":
                {
                    $action = $val;
                }
                break;

                default:
                {
                    $params[$param] = $val;
                }
            }
        }

        if($this->componentName == "")
        {
            trigger_error("no component specified");
        }


        if( !is_readable($this->siteRoot."/components/$this->componentName.php") )
        {
            trigger_error('cannot find specified component: '.$this->componentName.' with site root: '.$this->siteRoot);
        }

        include $this->siteRoot."/components/$this->componentName.php";

        if( !class_exists($this->componentName) )
        {
            trigger_error("cannot find specified class: $this->componentName");
        }

        $this->componentInstance = new $this->componentName;

        $this->componentInstance->Prepare($action, $params);
    }
}

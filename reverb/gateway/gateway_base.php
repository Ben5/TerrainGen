<?php

include "/opt/site/site/config/site.php";
include SiteConfig::REVERB_ROOT."/system/error.php";

set_error_handler("Error::ErrorHandler" );

class GatewayBase 
{
    private $siteRootArray = array('terraingen' => '/opt/git/TerrainGen/site');

    protected $siteRoot;
    protected $componentName;
    protected $componentInstance;

    public function prepare()
    {
        $this->componentName = '';
        $this->siteRoot = SiteConfig::SITE_ROOT;
        $projectName = '';

        $action = 'Index';
        $params = array();
        
        foreach( $_REQUEST as $param=>$val )
        {
            switch( $param )
            {
                case "_project":
                {
                    if (!isset($this->siteRootArray[$val]))
                    {
                        trigger_error('unknown project name: '.$val);
                    }

                    $this->siteRoot = $this->siteRootArray[$val];
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
            trigger_error("cannot find specified component: $this->componentName");
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

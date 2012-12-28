<?php
require_once(SiteConfig::REVERB_ROOT."/system/componentbase.php");
require_once(SiteConfig::SITE_ROOT."/models/terrainGenerator.php");

class Terrain extends ComponentBase
{
    protected function 
    Index($params)
    {
        $terrainArray = TerrainGenerator::GenerateTerrain();
        $this->ExposeVariable("terrain", $terrainArray); 
    }
}

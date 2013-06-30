<?php

use TerrainGen\SiteConfig;

require_once(SiteConfig::REVERB_ROOT."/system/componentbase.php");
require_once(SiteConfig::SITE_ROOT."/models/terrainGenerator.php");

class Terrain extends ComponentBase
{
    protected function 
    Index($params)
    {
        $height = isset($params['height']) ? $params['height'] : null;
        $width  = isset($params['width'])  ? $params['width']  : null;

        $terrainGenerator = new TerrainGenerator();
        $terrainArray = $terrainGenerator->GenerateTerrain($height, $width);
        $this->ExposeVariable("terrain", $terrainArray); 
    }

    protected function
    GetTerrain($params)
    {
        $height = isset($params['height']) ? $params['height'] : null;
        $width  = isset($params['width'])  ? $params['width']  : null;

        $terrainGenerator = new TerrainGenerator();
        $terrainArray = $terrainGenerator->GenerateTerrain($height, $width);
        $this->ExposeVariable("terrain", $terrainArray); 
    }
}

<?php

use TerrainGen\SiteConfig;

require_once(SiteConfig::SITE_ROOT."/models/terrainClass.php");
require_once(SiteConfig::SITE_ROOT."/models/neighbourManager.php");

class TerrainGenerator
{
    const MAP_WIDTH  = 16;
    const MAP_HEIGHT = 16;

    const INITIAL_CELL_IS_OCEAN = 0.5; // these must total 1.0!
    const INITIAL_CELL_IS_BEACH = 0.1; // these must total 1.0!
    const INITIAL_CELL_IS_GRASS = 0.4; // these must total 1.0!

    // set up the probabilities of changing terrain type
    // note that the chances of not changing are left implicit so as to not require changing 2
    // numbers for each adjustment.
    const OCEAN_TO_BEACH = 0.2;
    const OCEAN_TO_OCEAN = 0.8;

    const BEACH_TO_GRASS = 0.4;
    const BEACH_TO_BEACH = 0.2;
    const BEACH_TO_OCEAN = 0.4;
    
    const GRASS_TO_BEACH = 0.2;
    const GRASS_TO_GRASS = 0.8;

    public function 
    GenerateTerrain(
        $height,
        $width)
    {
        if (is_null($height) || $height <= 0)
        {
            $height = self::MAP_HEIGHT;
        }
        if (is_null($width) || $width <= 0)
        {
            $width = self::MAP_WIDTH;
        }

        $terrainArray = array();

        $neighbourManager = new NeighbourManager();
    
        $neighbourManager->AddTerrainType( "ocean", new Neighbour("beach", new BeachTerrain(), self::OCEAN_TO_BEACH) );
        $neighbourManager->AddTerrainType( "ocean", new Neighbour("ocean", new OceanTerrain(), self::OCEAN_TO_OCEAN) );

        $neighbourManager->AddTerrainType( "beach", new Neighbour("ocean", new OceanTerrain(), self::BEACH_TO_OCEAN) );
        $neighbourManager->AddTerrainType( "beach", new Neighbour("grass", new GrassTerrain(), self::BEACH_TO_GRASS) );
        $neighbourManager->AddTerrainType( "beach", new Neighbour("beach", new BeachTerrain(), self::BEACH_TO_BEACH) );
        
        $neighbourManager->AddTerrainType( "grass", new Neighbour("beach", new BeachTerrain(), self::GRASS_TO_BEACH) );
        $neighbourManager->AddTerrainType( "grass", new Neighbour("grass", new GrassTerrain(), self::GRASS_TO_GRASS) );

        $neighbourManager->PopulateNeighbourObjects();

        for($heightIndex = 0; $heightIndex < $height; $heightIndex++)
        {
            for($widthIndex = 0; $widthIndex < $width; $widthIndex++)
            {
                $random = rand(1,10) / 10;

                // origin square
                if($widthIndex === 0 && $heightIndex === 0)
                {
                    $type = $this->GenerateOriginSquare($random);
                    $terrainArray[$heightIndex][$widthIndex] = $type;
                    continue;
                }
                
                $previousSquare = ($widthIndex === 0 ?
                                            null :
                                            $terrainArray[$heightIndex][$widthIndex-1]);
                $aboveSquare    = ($heightIndex === 0 ? 
                                            null :
                                            $terrainArray[$heightIndex-1][$widthIndex]);
                // first square of row
                if($widthIndex === 0)
                {
                    $type = $this->GenerateFirstSquareInRow($random, $aboveSquare);
                    $terrainArray[$heightIndex][$widthIndex] = $type;
                    continue;
                }

                // all other squares
                $neighbourType = $neighbourManager->GetNextTerrainType($random, $previousSquare, $aboveSquare);
                $terrainArray[$heightIndex][$widthIndex] = $neighbourType;
            }
        }

        return $terrainArray;
    }

    private function
    GenerateOriginSquare(
        $random)
    {
        // we order the terrain types like this:
        // OCEAN, BEACH, GRASS
        $type = ($random <= self::INITIAL_CELL_IS_OCEAN ?
                    new OceanTerrain() : 
                    ($random <= (self::INITIAL_CELL_IS_OCEAN + self::INITIAL_CELL_IS_BEACH) ?
                        new BeachTerrain() :
                        new GrassTerrain() ));

        return $type;
    }

    private function
    GenerateFirstSquareInRow(
        $random, 
        $aboveSquare)
    {
        switch($aboveSquare->GetType())
        {
            case 'ocean':
            {
                $type = ($random < self::OCEAN_TO_BEACH ?
                            new BeachTerrain() : 
                            new OceanTerrain() );
            }
            break;

            case 'beach':
            {
                $type = ($random < self::BEACH_TO_GRASS ?
                            new GrassTerrain() : 
                            ($random < self::BEACH_TO_GRASS + self::BEACH_TO_OCEAN ?
                               new OceanTerrain() : 
                               new BeachTerrain() ));
            }
            break;

            case 'grass':
            {
                $type = ($random < self::GRASS_TO_BEACH ?
                            new BeachTerrain() : 
                            new GrassTerrain() );
            }
            break;
        }
        return $type;
    }
}

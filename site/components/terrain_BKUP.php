<?php
include(SiteConfig::REVERB_ROOT."/system/componentbase.php");

class Terrain extends ComponentBase
{
    const MAP_WIDTH  = 32;
    const MAP_HEIGHT = 32;
    const INITIAL_CELL_IS_OCEAN = 0.5; // these must total 1.0!
    const INITIAL_CELL_IS_BEACH = 0.1; // these must total 1.0!
    const INITIAL_CELL_IS_GRASS = 0.4; // these must total 1.0!

    // set up the probabilities of changing terrain type
    // note that the chances of not changing are left implicit so as to not require changing 2
    // numbers for each adjustment.
    const OCEAN_TO_BEACH = 0.3;
    const BEACH_TO_GRASS = 0.8;
    const BEACH_TO_OCEAN = 0.6;
    const GRASS_TO_BEACH = 0.3;

    protected function 
    Index($params)
    {
        $terrainArray = array();

        $ocean = array("type"   => "ocean",
                       "colour" => "blue");
        $grass = array("type"   => "grass",
                       "colour" => "green");
        $beach = array("type"   => "beach",
                       "colour" => "yellow");

        ///////////////////////////////
        // rules for terrain generation
        ///////////////////////////////
        // ocean can only join other ocean or beach
        // beach can join ocean, beach or grass
        // grass can join other grass or beach
        //////////////////////////////
        // there are defined probabilities of each type meeting another type 
        // ///////////////////////////
        // we start in the top left hand corner and work to the right, row-wise
        // where there is a row above the current row, we consider the cell above as well as to the left
        ///////////////////////////////

        for($heightIndex = 0; $heightIndex < self::MAP_WIDTH; $heightIndex++)
        {
            for($widthIndex = 0; $widthIndex < self::MAP_WIDTH; $widthIndex++)
            {
                $random = rand(1,10) / 10;

                // origin square
                if($widthIndex === 0 && $heightIndex === 0)
                {
                    // we order the terrain types like this:
                    // OCEAN, BEACH, GRASS
                    $type = ($random <= self::INITIAL_CELL_IS_OCEAN ?
                                $ocean : 
                                ($random <= (self::INITIAL_CELL_IS_OCEAN + self::INITIAL_CELL_IS_BEACH) ?
                                    $beach :
                                    $grass));

                    $terrainArray[$heightIndex][$widthIndex] = $type;
                    continue;
                }
                
                $previousSquareType = ($widthIndex === 0 ?
                                            NULL :
                                            $terrainArray[$heightIndex][$widthIndex-1]);
                $aboveSquareType    = ($heightIndex === 0 ? 
                                            NULL :
                                            $terrainArray[$heightIndex-1][$widthIndex]);
                // first square of row
                if($widthIndex === 0)
                {
                    switch($aboveSquareType['type'])
                    {
                        case 'ocean':
                        {
                            $type = ($random < self::OCEAN_TO_BEACH ?
                                        $beach : $ocean);
                        }
                        break;

                        case 'beach':
                        {
                            $type = ($random < self::BEACH_TO_GRASS ?
                                        $grass : 
                                        ($random < self::BEACH_TO_GRASS + self::BEACH_TO_OCEAN ?
                                           $ocean : $beach));
                        }
                        break;

                        case 'grass':
                        {
                            $type = ($random < self::GRASS_TO_BEACH ?
                                        $beach : $grass);
                        }
                        break;
                    }

                    $terrainArray[$heightIndex][$widthIndex] = $type;
                    continue;
                }

                // all other squares
                switch($previousSquareType['type'])
                {
                    case "ocean":
                    {
                        // if the square above is grass, this one can't be ocean!
                        if(is_null($aboveSquareType))
                        {
                            $type = ($random < self::OCEAN_TO_BEACH ?
                                $beach : $ocean);
                        }
                        else
                        {
                            switch($aboveSquareType['type'])
                            {
                                case "grass":
                                {
                                    $type = $beach;
                                }
                                break;

                                case "ocean":
                                case "beach":
                                {
                                    $type = ($random < self::OCEAN_TO_BEACH ?
                                        $beach : $ocean);
                                }
                                break;
                            }
                        }
                    }
                    break;

                    case "beach":
                    {
                        // if the above square is ocean, then this one can't be grass
                        // if the above one is grass, then this can't be ocean
                        if(is_null($aboveSquareType))
                        {
                            $type = ($random < self::BEACH_TO_GRASS ?
                                        $grass : 
                                        ($random < self::BEACH_TO_GRASS + self::BEACH_TO_OCEAN ?
                                           $ocean : $beach));
                        }  
                        else
                        {
                            switch($aboveSquareType['type'])
                            {
                                case "ocean":
                                {
                                    $type = ($random < self::BEACH_TO_OCEAN ?
                                                $ocean : $beach );
                                }
                                break;

                                case "beach":
                                {
                                    $type = ($random < self::BEACH_TO_GRASS ?
                                                $grass : 
                                                ($random < self::BEACH_TO_GRASS + self::BEACH_TO_OCEAN ?
                                                   $ocean : $beach));
                                }
                                break;

                                case "grass":
                                {
                                    $type = ($random < self::BEACH_TO_GRASS ?
                                                $grass : $beach);
                                }
                                break;
                            }
                        }
                    }
                    break;

                    case "grass":
                    {
                        // if the square above is ocean, this one can't be grass!
                        if(is_null($aboveSquareType))
                        {
                            $type = ($random < self::GRASS_TO_BEACH ?
                                $beach : $grass);
                        }
                        else
                        {
                            switch($aboveSquareType['type'])
                            {
                                case "ocean":
                                {
                                    $type = $beach;
                                }
                                break;

                                case "beach":
                                case "grass":
                                {
                                    $type = ($random < self::GRASS_TO_BEACH ?
                                        $beach : $grass);
                                }
                                break;
                            }
                        }
                    }
                    break;
                }
                $terrainArray[$heightIndex][$widthIndex] = $type;
            }
        }

        $this->ExposeVariable("terrain", $terrainArray); 
    }

}

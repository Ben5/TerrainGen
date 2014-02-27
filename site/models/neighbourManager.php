<?php

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

class Neighbour
{
    private $name;
    private $type;
    private $probability;

    public function
    __construct(
        $name,
        $type,
        $probability)
    {
        $this->name = $name;
        $this->type = $type;
        $this->probability = $probability;
    }

    public function
    GetNeighbourTypeName()
    {
        return $this->name;
    }

    public function
    GetNeighbourType()
    {
        return $this->type;
    }

    public function
    GetNeighbourProbability()
    {
        return $this->probability;
    }
}

class NeighbourManager
{
    private $neighbourDictionary = array();
    private $neighbourObjectWarehouse = array();

    public function
    __construct()
    {

    }

    public function
    AddTerrainType(
        $terrainType,
        Neighbour $neighbour)
    {
       $this->neighbourDictionary[$terrainType][] = $neighbour; 
    }

    public function
    PopulateNeighbourObjects()
    {
        if( count($this->neighbourObjectWarehouse) != 0 ) 
        {
            trigger_error("calling PopulateNeighbourObjects but there are already objects!");   
        }

        foreach($this->neighbourDictionary as $neighbours)
        {
            foreach($neighbours as $neighbourObject)
            {
                $name = $neighbourObject->GetNeighbourTypeName();

                if( !isset($this->neighbourObjectWarehouse[$name]) )
                {
                    $this->neighbourObjectWarehouse[$name] = $neighbourObject->GetNeighbourType();
                }
            }
        }
    }

    public function 
    GetNextTerrainType(
        $random,
        TerrainTypeBase $previousTerrainType,
        TerrainTypeBase $otherPreviousTerrainType = null)
    {
        $previousTerrainTypeName = $previousTerrainType->GetType();
        if( !isset($this->neighbourDictionary[$previousTerrainTypeName]) ) 
        {
            trigger_error("no neighbours defined for terrain type: $previousTerrainTypeName");
        }

        $neighbours = array( $this->neighbourDictionary[$previousTerrainTypeName] );
        

        if( !is_null($otherPreviousTerrainType))
        {
            $otherPreviousTerrainTypeName = $otherPreviousTerrainType->GetType();

            if( !isset($this->neighbourDictionary[$otherPreviousTerrainTypeName]) )
            {
                trigger_error("no neighbours defined for terrain type: $otherPreviousTerrainTypeName");
            }

            $otherNeighbours = array($this->neighbourDictionary[$otherPreviousTerrainTypeName]);

            $neighbourIntersection = array();
            
            foreach($neighbours as $neighbourArray)
            {
                foreach($neighbourArray as $neighbour)
                {
                    $neighbourName = $neighbour->GetNeighbourTypeName();
                    foreach($otherNeighbours as $otherNeighbourArray)
                    {
                        foreach($otherNeighbourArray as $otherNeighbour)
                        {
                            if($neighbourName == $otherNeighbour->GetNeighbourTypeName())
                            {
                                $neighbourIntersection[] = $neighbour;
                            }
                        }
                    }
                }
            }

            $neighbours = array($neighbourIntersection);
        }

        $type = null;
        $cumulativeProbability = 0;

        foreach($neighbours as $neighbourArray)
        {
            foreach($neighbourArray as $neighbour)
            {
                $cumulativeProbability += $neighbour->GetNeighbourProbability();

                if($random <= $cumulativeProbability)
                {
                    $type = clone $neighbour->GetNeighbourType();
                    break 2;
                }
            }
        }
    
        if( is_null($type) )
        {
            $maxProbability = 0;

            foreach($neighbours as $neighbourArray)
            {
                foreach($neighbourArray as $neighbour)
                {
                    if($neighbour->GetNeighbourProbability() > $maxProbability)
                    {
                        $maxProbability = $neighbour->GetNeighbourProbability();
                        $type = clone $neighbour->GetNeighbourType();
                    }
                }
            }
        }

        return $type;
    }
}

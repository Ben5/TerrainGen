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
        TerrainTypeBase $otherPreviousTerrainType = NULL)
    {
        $previousTerrainTypeName = $previousTerrainType->GetType();
        if( !isset($this->neighbourDictionary[$previousTerrainTypeName]) ) 
        {
            trigger_error("no neighbours defined for terrain type: $previousTerrainType->GetType()");
        }

        $previousTerrainTypeName = $previousTerrainType->GetType();
        $neighbours = array(
            $previousTerrainTypeName =>
                $this->neighbourDictionary[$previousTerrainTypeName] );
        
        if( !is_null($otherPreviousTerrainType))
        {
            $otherPreviousTerrainTypeName = $otherPreviousTerrainType->GetType();
            if( isset($this->neighbourDictionary[$otherPreviousTerrainTypeName]) )
            {
                $otherNeighbours = array(
                    $otherPreviousTerrainTypeName =>
                         $this->neighbourDictionary[$otherPreviousTerrainTypeName]);
               //print_r($neighbours);
               //echo "<br><br>";
               //print_r($otherNeighbours);
                $neighbours = array_intersect($neighbours, $otherNeighbours);
            }
        }

       // $random = rand(1,10) / 10;
        $type = NULL;
        $cumulativeProbability = 0;

        foreach($neighbours as $typeName => $neighbourArray)
        {
            foreach($neighbourArray as $neighbour)
            {
                $cumulativeProbability += $neighbour->GetNeighbourProbability();
                if($random <= $cumulativeProbability)
                {
                    $type = clone $neighbour->GetNeighbourType();
                    $ben = 23;
                }
            }
        }

    
        if( is_null($type) )
        {
            $type = $previousTerrainType;
        }

        // todo! make $type an object of the right type! (and call this function!)


        return $type;
    }
}

<?php

abstract class TerrainTypeBase
{
    private $colour;
    private $type;

    protected function
    __construct(
        $colour,
        $type)
    {
        $this->colour = $colour;
        $this->type = $type;
    }

    public function
    GetType()
    {
        return $this->type;
    }
}

class OceanTerrain extends TerrainTypeBase
{
    public function
    __construct()
    {
        parent::__construct("blue", "ocean");
    }
}

class BeachTerrain extends TerrainTypeBase
{
    public function
    __construct()
    {
        parent::__construct("yellow", "beach");
    }
}

class GrassTerrain extends TerrainTypeBase
{
    public function
    __construct()
    {
        parent::__construct("green", "grass");
    }
}

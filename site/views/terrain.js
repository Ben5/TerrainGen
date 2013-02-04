function RefreshTerrain() 
{
    $('.terrainContainer').load('/html/terraingen/terrain/index .terrainContainer', 
                                {'height': $('#newY').val(), 'width': $('#newX').val()}
                                );
}

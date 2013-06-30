function RefreshTerrain() 
{
    $('.terrainContainer').load('/terraingen/html/terrain/GetTerrain .terrainContainer', 
                                {'height': $('#newY').val(), 'width': $('#newX').val()}
                                );
}

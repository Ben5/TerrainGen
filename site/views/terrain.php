</head>

<body>
<h1>TerrainGen</h1>

<div class="formContainer">
    <form class="terrainForm" name="terrainForm">
        <div class="label">Width:</div> 
        <input id="newX" type="text" />
        <br />
        
        <div class="label">Height:</div> 
        <input id="newY" type="text" />
        <br />

        <button id="generate" 
                onclick="RefreshTerrain(); return false;"
        >
            Generate Terrain
        </button>
    </form>
</div>

<div class="terrainContainer">
    <?php
    foreach($terrain as $row)
    {
        foreach($row as $cell)
        {
    ?>
            <div class="terrainCell <?php echo $cell->GetType();?>" ></div>
    <?php
        }
    ?>
        <div style="clear:both"></div>
    <?php
    }
    ?>
</div>

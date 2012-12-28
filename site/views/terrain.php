</head>

<body>
<h1>Terrain!</h1>

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

</body>

</html>

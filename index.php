<?php

require_once('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex</title>
</head>
<body>
    <form method="POST" action="./controller/getPokemon.php">
        <input type="search" name="pokemon-name" id="pokemon-name" placeholder="Nom du pokemon">
        <input type="number" name="pokemon-id" id="pokemon-id" placeholder="ID du pokemon">
        <button type="submit" name="submit">Rechercher</button>
    </form>
</body>
</html>
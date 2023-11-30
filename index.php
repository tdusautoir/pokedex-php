<?php

require_once('db.php');

$sql = "SELECT * FROM pokemons";
$query = $db->prepare($sql);
$query->execute();
$pokemons = $query->fetchAll();

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
        <input type="search" name="pokemon-data" placeholder="Nom ou ID du pokemon">
        <button type="submit" name="submit">Rechercher</button>
    </form>
    <?php foreach ($pokemons as $pokemon) : ?>
        <a href="./pokemon.php?id=<?= $pokemon->id ?>">
            <div>
                <h2><?= $pokemon->name ?></h2>
                <img src=".<?= $pokemon->image ?>" alt="Image of <?= $pokemon->name ?>">
            </div>
        </a>
    <?php endforeach; ?>
</body>

</html>
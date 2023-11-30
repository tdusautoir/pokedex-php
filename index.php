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
    <link rel="stylesheet" href="./public/reset.css">
    <link rel="stylesheet" href="./public/style.css">
    <title>Pokedex</title>
</head>

<body>
    <?php include('./components/navBar.php'); ?>

    <div class="pokedex">
        <?php foreach ($pokemons as $pokemon) : ?>
            <a href="./pokemon.php?id=<?= $pokemon->id ?>" class="pokemon-card">
                <h2>n°<?= $pokemon->id ?> - <?= $pokemon->name ?></h2>
                <img src=".<?= $pokemon->image ?>" alt="Image of <?= $pokemon->name ?>">
            </a>
        <?php endforeach; ?>
    </div>
</body>

</html>
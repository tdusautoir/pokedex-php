<?php

require_once('functions.php');
require_once('db.php');

init_php_session();

if ($_GET["typeId"] && $_GET["generationId"]) {
    $sql = "SELECT * FROM `types` INNER JOIN pokemons_types ON pokemons_types.typeId = types.id INNER JOIN pokemons ON pokemons_types.pokemonId = pokemons.id WHERE typeId = ? ANd generation = ?";
    $query = $db->prepare($sql);
    $query->execute([$_GET["typeId"], $_GET["generationId"]]);
    $pokemons = $query->fetchAll();
} else if ($_GET["generationId"]) {
    $sql = "SELECT * FROM pokemons WHERE generation = ?";
    $query = $db->prepare($sql);
    $query->execute([$_GET["generationId"]]);
    $pokemons = $query->fetchAll();
} else if ($_GET["typeId"]) {
    $sql = "SELECT * FROM `types` INNER JOIN pokemons_types ON pokemons_types.typeId = types.id INNER JOIN pokemons ON pokemons_types.pokemonId = pokemons.id WHERE typeId = ?";
    $query = $db->prepare($sql);
    $query->execute([$_GET["typeId"]]);
    $pokemons = $query->fetchAll();
} else {
    $sql = "SELECT * FROM pokemons";
    $query = $db->prepare($sql);
    $query->execute();
    $pokemons = $query->fetchAll();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script defer src="./public/js/script.js"></script>
    <link rel="stylesheet" href="./public/reset.css">
    <link rel="stylesheet" href="./public/style.css">
    <title>Pokedex</title>
</head>

<body>
    <?php include('./components/flashMessage.php'); ?>
    <?php include('./components/navBar.php'); ?>

    <div class="pokedex">
        <?php if (count($pokemons) === 0) : ?>
            <h2>Aucun pokémon trouvé</h2>
        <?php endif; ?>
        <?php foreach ($pokemons as $pokemon) : ?>
            <a href="./pokemon.php?id=<?= $pokemon->id ?>" class="pokemon-card">
                <h2>n°<?= $pokemon->id ?> - <?= $pokemon->name ?></h2>
                <img src=".<?= $pokemon->image ?>" alt="Image of <?= $pokemon->name ?>">
            </a>
        <?php endforeach; ?>
    </div>
</body>

</html>
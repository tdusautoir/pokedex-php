<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('db.php');
require_once('functions.php');

if (!isset($_GET)) {
    header('Location: index.php');
    die();
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    die();
}

$sql = "SELECT *, pokemons.id as pokemonId FROM pokemons INNER JOIN stats ON pokemons.id = stats.pokemonId WHERE pokemons.id = ? LIMIT 1";
$query = $db->prepare($sql);
$query->execute([$_GET['id']]);
$pokemon = $query->fetch();

if (!$pokemon) {
    header('Location: index.php');
    die();
}

$sql = "SELECT * FROM pokemons_types INNER JOIN types ON types.id = pokemons_types.typeId WHERE pokemonId = ?";
$query = $db->prepare($sql);
$query->execute([$pokemon->pokemonId]);
$types = $query->fetchAll();

$sql = "SELECT * FROM pokemons_evolutions WHERE pokemonId = ?";
$query = $db->prepare($sql);
$query->execute([$pokemon->pokemonId]);
$evolutions = $query->fetchAll();

$sql = "SELECT * FROM pokemons_pre_evolutions WHERE pokemonId = ?";
$query = $db->prepare($sql);
$query->execute([$pokemon->pokemonId]);
$preEvolutions = $query->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex - <?= $pokemon->name ?></title>
</head>

<body>
    <a href="./index.php">Retour</a>
    <h1><?= $pokemon->name ?></h1>
    <img src=".<?= $pokemon->image ?>" alt="Image of <?= $pokemon->name ?>">
    <?php if (count($types) > 0) : ?>
        <p>Types:</p>
        <ul>
            <?php foreach ($types as $type) : ?>
                <li><?= $type->name ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <p>HP : <?= $pokemon->hp ?></p>
    <p>Attack : <?= $pokemon->attack ?></p>
    <p>Defense : <?= $pokemon->defense ?></p>
    <p>Special Attack : <?= $pokemon->special_attack ?></p>
    <p>Special Defense : <?= $pokemon->special_defense ?></p>
    <p>Speed : <?= $pokemon->speed ?></p>
    <?php if (count($evolutions) > 0) : ?>
        <p>Evolutions :</p>
        <ul>
            <?php foreach ($evolutions as $evolution) : ?>
                <li><?= $evolution->name ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if (count($preEvolutions) > 0) : ?>
        <p>Prè évolutions :</p>
        <ul>
            <?php foreach ($preEvolutions as $evolution) : ?>
                <li><?= $evolution->name ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>

</html>
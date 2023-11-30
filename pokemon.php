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
    <link rel="stylesheet" href="./public/reset.css">
    <link rel="stylesheet" href="./public/style.css">
    <title>Pokedex - <?= $pokemon->name ?></title>
</head>

<body>
    <main>
        <?php include('./components/navBar.php'); ?>
        <a href="./index.php">Retour</a>
        <h1><?= $pokemon->name ?></h1>
        <div class="pokemon">
            <div class="pokemon-header">
                <img src=".<?= $pokemon->image ?>" alt="Image of <?= $pokemon->name ?>">
            </div>
            <div class="pokemon-stat">
                <div class="pokemon-stat-types">
                    <?php if (count($types) > 0) : ?>
                        <h2>Types:</h2>
                        <ul>
                            <?php foreach ($types as $type) : ?>
                                <li><img src=".<?= $type->image ?>" />
                                    <p><?= $type->name ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <h2>Statistiques :</h2>
                <p>HP : <?= $pokemon->hp ?></p>
                <p>Attack : <?= $pokemon->attack ?></p>
                <p>Defense : <?= $pokemon->defense ?></p>
                <p>Special Attack : <?= $pokemon->special_attack ?></p>
                <p>Special Defense : <?= $pokemon->special_defense ?></p>
                <p>Speed : <?= $pokemon->speed ?></p>
                <div class="pokemon-evolutions">
                    <?php if (count($evolutions) > 0) : ?>
                        <h2>Evolutions :</h2>
                        <ul>
                            <?php foreach ($evolutions as $evolution) : ?>
                                <form action="./controller/getPokemon.php" method="POST">
                                    <input type="hidden" name="pokemon-data" value="<?= $evolution->evolutionPokemonId ?>">
                                    <button type="submit" name="submit"><?= $evolution->name ?></button>
                                </form>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if (count($preEvolutions) > 0) : ?>
                        <h2>Pré-évolutions :</h2>
                        <ul>
                            <?php foreach ($preEvolutions as $evolution) : ?>
                                <form action="./controller/getPokemon.php" method="POST">
                                    <input type="hidden" name="pokemon-data" value="<?= $evolution->evolutionPokemonId ?>">
                                    <button type="submit" name="submit"><?= $evolution->name ?></button>
                                </form>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <form action="./controller/deletePokemon.php" method="POST" class="pokemom-delete-form">
            <input type="hidden" name="pokemonId" value="<?= $pokemon->pokemonId ?>">
            <button type="submit" name="submit">Supprimer</button>
        </form>
    </main>
</body>

</html>
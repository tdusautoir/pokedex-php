<?php

require_once('db.php');
require_once('functions.php');

init_php_session();

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
    <script defer src="./public/js/script.js"></script>
    <link ref="shortcut icon" href=".<?= $pokemon->sprite ?>" type="image/x-icon">
    <link rel="stylesheet" href="./public/reset.css">
    <link rel="stylesheet" href="./public/style.css">
    <title>Pokedex - <?= $pokemon->name ?></title>
</head>

<body>
    <main>
        <?php include('./components/flashMessage.php'); ?>
        <?php include('./components/navBar.php'); ?>
        <form class="pokemon-edit-form" method="POST" action="./controller/updatePokemon.php">
            <input type="hidden" name="pokemonId" value="<?= $pokemon->pokemonId ?>">
            <div class="pokemon-page">
                <a href="./index.php">Retour</a>
                <input type="text" name="name" value="<?= $pokemon->name ?>" />
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
                                            <a href="./index.php?typeId="><?= $type->name ?></a>
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
                                            <?php if (file_exists("./public/pokemon_sprites/$evolution->evolutionPokemonId.png")) : ?>
                                                <button type="submit" name="submit">
                                                    <img src="./public/pokemon_sprites/<?= $evolution->evolutionPokemonId ?>.png" alt="Image of <?= $evolution->name ?>">
                                                    <?= $evolution->name ?></button>
                                            <?php else : ?>
                                                <button type="submit" name="submit" class="no-img"><?= $evolution->name ?></button>
                                            <?php endif; ?>
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
                                            <?php if (file_exists("./public/pokemon_sprites/$evolution->evolutionPokemonId.png")) : ?>
                                                <button type="submit" name="submit">
                                                    <img src="./public/pokemon_sprites/<?= $evolution->evolutionPokemonId ?>.png" alt="Image of <?= $evolution->name ?>">
                                                    <?= $evolution->name ?></button>
                                            <?php else : ?>
                                                <button type="submit" name="submit" class="no-img"><?= $evolution->name ?></button>
                                            <?php endif; ?>
                                        </form>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="actions">
                    <form action="./controller/deletePokemon.php" method="POST" class="pokemom-delete-form">
                        <input type="hidden" name="pokemonId" value="<?= $pokemon->pokemonId ?>">
                        <button type="submit" name="delete">Supprimer</button>
                    </form>
                    <button type="submit" name="save-edit">Enregistrer</button>
                </div>
            </div>
        </form>
    </main>
</body>

</html>
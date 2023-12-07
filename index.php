<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('functions.php');
require_once('db.php');

init_php_session();

$elementsOnPage = 25;

foreach ($_GET as $key => $value) {
    if (!isInteger($value)) {
        unset($_GET[$key]);
    }
}

$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($currentPage - 1) * $elementsOnPage;

if (isset($_GET["typeId"]) && isset($_GET["generationId"])) {
    $sql = "SELECT * FROM `types` INNER JOIN pokemons_types ON pokemons_types.typeId = types.id INNER JOIN pokemons ON pokemons_types.pokemonId = pokemons.id WHERE typeId = :typeId AND generation = :generationId LIMIT :offset, :elementsOnPage";
    $query = $db->prepare($sql);
    $query->bindValue(':generationId', $_GET["generationId"], PDO::PARAM_INT);
    $query->bindValue(':typeId', $_GET["typeId"], PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':elementsOnPage', $elementsOnPage, PDO::PARAM_INT);
    $query->execute();
    $pokemons = $query->fetchAll();

    $sql = "SELECT COUNT(*) FROM `types` INNER JOIN pokemons_types ON pokemons_types.typeId = types.id INNER JOIN pokemons ON pokemons_types.pokemonId = pokemons.id WHERE typeId = :typeId AND generation = :generationId";
    $query = $db->prepare($sql);
    $query->bindValue(':generationId', $_GET["generationId"], PDO::PARAM_INT);
    $query->bindValue(':typeId', $_GET["typeId"], PDO::PARAM_INT);
    $query->execute();
    $totalElements = $query->fetchColumn();
} else if (isset($_GET["generationId"])) {
    $sql = "SELECT * FROM pokemons WHERE generation = :generationId LIMIT :offset, :elementsOnPage";
    $query = $db->prepare($sql);
    $query->bindValue(':generationId', $_GET["generationId"], PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':elementsOnPage', $elementsOnPage, PDO::PARAM_INT);
    $query->execute();
    $pokemons = $query->fetchAll();

    $sql = "SELECT COUNT(*) FROM pokemons WHERE generation = :generationId";
    $query = $db->prepare($sql);
    $query->bindValue(':generationId', $_GET["generationId"], PDO::PARAM_INT);
    $query->execute();
    $totalElements = $query->fetchColumn();
} else if (isset($_GET["typeId"])) {
    $sql = "SELECT * FROM `types` INNER JOIN pokemons_types ON pokemons_types.typeId = types.id INNER JOIN pokemons ON pokemons_types.pokemonId = pokemons.id WHERE typeId = :typeId  LIMIT :offset, :elementsOnPage";
    $query = $db->prepare($sql);
    $query->bindValue(':typeId', $_GET["typeId"], PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':elementsOnPage', $elementsOnPage, PDO::PARAM_INT);
    $query->execute();
    $pokemons = $query->fetchAll();

    $sql = "SELECT COUNT(*) FROM `types` INNER JOIN pokemons_types ON pokemons_types.typeId = types.id INNER JOIN pokemons ON pokemons_types.pokemonId = pokemons.id WHERE typeId = :typeId";
    $query = $db->prepare($sql);
    $query->bindValue(':typeId', $_GET["typeId"], PDO::PARAM_INT);
    $query->execute();
    $totalElements = $query->fetchColumn();
} else {
    $sql = "SELECT * FROM pokemons LIMIT :offset, :elementsOnPage";
    $query = $db->prepare($sql);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':elementsOnPage', $elementsOnPage, PDO::PARAM_INT);
    $query->execute();
    $pokemons = $query->fetchAll();

    $sql = "SELECT COUNT(*) FROM pokemons";
    $query = $db->prepare($sql);
    $query->execute();
    $totalElements = $query->fetchColumn();
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
    <script src="https://kit.fontawesome.com/49dbd7732f.js" crossorigin="anonymous"></script>
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

    <?php
    $totalPages = ceil($totalElements / $elementsOnPage);

    if ($totalPages > 1) : ?>
        <div class="pokedex-pagination">
            <p class="pokedex-pagination-result"> <?= $totalElements ?> résultats</p>
            <div class="pokedex-pagination-pages">
                <?php if ($currentPage > 1) : ?>
                    <a href="<?= build_url("page=" . ($currentPage - 1)) ?>"><i class="fa-solid fa-angle-left"></i></a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a class="<?= isset($_GET['page']) ? ($_GET['page'] == $i ? "active" : "") : ($i === 1 ? "active" : "") ?>" href="<?= build_url("page=$i") ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($currentPage < $totalPages) : ?>
                    <a href="<?= build_url("page=" . ($currentPage + 1)) ?>"><i class="fa-solid fa-angle-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>
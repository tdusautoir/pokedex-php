<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('db.php');
require_once('functions.php');

$sql = "SELECT DISTINCT generation FROM pokemons";
$query = $db->prepare($sql);
$query->execute();
$generations = $query->fetchAll();

$noSelectedGeneration = true;
if (isset($_GET['generationId'])) {
    foreach ($generations as $generation) {
        if ($generation->generation === $_GET['generationId']) {
            $noSelectedGeneration = false;
        }
    }
}

?>

<header>
    <nav class="navBar">
        <form method="POST" action="./controller/getPokemon.php">
            <input type="search" name="pokemon-data" placeholder="Nom ou ID du pokemon">
            <button type="submit" name="submit">Rechercher</button>
        </form>
    </nav>
    <div class="filterBar">
        <a href="./index.php" class="<?= $noSelectedGeneration ? "active" : '' ?>">Tout</a>
        <?php foreach ($generations as $generation) : ?>
            <a href="./index.php?generationId=<?= $generation->generation ?>" class="<?= $generation->generation === $_GET['generationId'] ? "active" : "" ?>">generation <?= $generation->generation ?></a>
        <?php endforeach; ?>
    </div>
</header>
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

$sql = "SELECT * FROM types";
$query = $db->prepare($sql);
$query->execute();
$allTypes = $query->fetchAll();

$currentType = null;
foreach ($allTypes as $type) {
    if (isset($_GET['typeId']) && $type->id === $_GET['typeId']) {
        $currentType = $type;
    }
}

$noParam = count($_GET) === 0;
?>

<header>
    <nav class="navBar">
        <form method="POST" action="./controller/getPokemon.php">
            <input type="search" name="pokemon-data" placeholder="Nom ou ID du pokemon">
            <button type="submit" name="submit">Rechercher</button>
        </form>
    </nav>
    <div class="filterBar <?= count($generations) > 5 ? "resize-1500" : '' ?>">
        <button class="filterby">filtrer</button>
        <a href="./index.php" class="<?= $noParam ? "active" : '' ?>">Tout</a>
        <?php foreach ($generations as $generation) : ?>
            <a href="<?= remove_from_url(build_url("generationId=" . $generation->generation), "page") ?>" class="<?= isset($_GET['generationId']) ? ($generation->generation === $_GET['generationId'] ? "active" : "") : "" ?>">generation <?= $generation->generation ?></a>
        <?php endforeach; ?>
        <select class="<?= isset($_GET['typeId']) ? "active" : "" ?>" name="typeId" id="select-type-id">
            <option value="<?= remove_from_current_url("typeId") ?>">Tous les types</option>
            <?php foreach ($allTypes as $type) : ?>
                <option value="<?= remove_from_url(build_url("typeId=" . $type->id), "page") ?>" <?= isset($_GET['typeId']) ? ($type->id === $_GET['typeId'] ? "selected=selected" : "") : "" ?>><?= $type->name ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($currentType)) : ?>
            <img class="current-type" src=".<?= $currentType->image ?>" />
        <?php endif; ?>
    </div>
</header>
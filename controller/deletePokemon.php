<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../functions.php');
require_once('../db.php');

if (isset($_POST["submit"])) {
    if (!(isset($_POST["pokemonId"]) && !empty($_POST["pokemonId"]))) {
        echo "aucun id spécifié";
        die();
    }

    try {
        $db->beginTransaction();

        $sql = "DELETE FROM pokemons_types WHERE pokemonId = ?";
        $query = $db->prepare($sql);
        $query->execute([$_POST["pokemonId"]]);

        $sql = "DELETE FROM pokemons_evolutions WHERE pokemonId = ?";
        $query = $db->prepare($sql);
        $query->execute([$_POST["pokemonId"]]);

        $sql = "DELETE FROM pokemons_pre_evolutions WHERE pokemonId = ?";
        $query = $db->prepare($sql);
        $query->execute([$_POST["pokemonId"]]);

        $sql = "DELETE FROM stats WHERE pokemonId = ?";
        $query = $db->prepare($sql);
        $query->execute([$_POST["pokemonId"]]);

        $sql = "DELETE FROM pokemons WHERE id = ?";
        $query = $db->prepare($sql);
        $query->execute([$_POST["pokemonId"]]);

        $db->commit();
        header("Location: ../index.php");
    } catch (Exception $e) {
        echo "erreur lors de la suppression";
        die();
    }
}

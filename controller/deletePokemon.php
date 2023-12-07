<?php

require_once('../functions.php');
require_once('../db.php');

init_php_session();

if (isset($_POST["delete"])) {
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

        create_flash_message("pokemon_deleted", "Le pokémon a bien été supprimé", FLASH_SUCCESS);
        header("Location: ../index.php");
        die();
    } catch (Exception $e) {
        create_flash_message("error", "Une erreur est survenue", FLASH_ERROR);
        header("Location: ../index.php");
        die();
    }
}

<?php

require_once('../functions.php');
require_once('../db.php');

init_php_session();

if (isset($_POST["save-edit"])) {
    if (!(isset($_POST["pokemonId"]) && !empty($_POST["pokemonId"]))) {
        create_flash_message("no id", "Aucun id spécifié", FLASH_ERROR);
        header("Location: ../index.php");
        die();
    }

    if (!(isset($_POST["name"]) && !empty($_POST["name"]))) {
        create_flash_message("no id", "Aucun nom spécifié", FLASH_ERROR);
        header("Location: ../index.php");
        die();
    }

    try {
        $sql = "UPDATE pokemons SET name = ? WHERE id = ?";
        $query = $db->prepare($sql);
        $query->execute([$_POST["name"], $_POST["pokemonId"]]);

        create_flash_message("pokemon_updated", "Le pokémon a bien été mis à jour", FLASH_SUCCESS);
        header("Location: ../pokemon.php?id=" . $_POST["pokemonId"]);
    } catch (Exception $e) {
        create_flash_message("error", "Une erreur est survenue", FLASH_ERROR);
        header("Location: ../index.php");
        die();
    }
}

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../functions.php');
require_once('../db.php');

$API_URL = "https://pokebuildapi.fr/api/v1/pokemon";

var_dump($_POST);

if (isset($_POST["submit"])) {
    if (!(isset($_POST["pokemon-name"]) && !empty($_POST["pokemon-name"])) && !(isset($_POST["pokemon-id"]) && !empty($_POST["pokemon-id"]))) {
        echo "Veuillez remplir un des champs";
        die();
    }

    if (isset($_POST["pokemon-name"]) && !empty($_POST["pokemon-name"])) {
        $pokemonName = $_POST["pokemon-name"];

        $sql = "SELECT * FROM pokemons WHERE name LIKE :name LIMIT 1";
        $query = $db->prepare($sql);
        $query->execute(["name" => "%" . $pokemonName . "%"]);
        $pokemon = $query->fetch();

        if ($pokemon) {
            var_dump($pokemon);
            die();
            header("Location: ../index.php?pokemon-id=" . $pokemon->id);
            die();
        }
    }

    if (isset($_POST["pokemon-id"]) && !empty($_POST["pokemon-id"])) {
        $pokemonId = $_POST["pokemon-id"];

        $sql = "SELECT * FROM pokemons WHERE id = ? LIMIT 1";
        $query = $db->prepare($sql);
        $query->execute([$pokemonId]);
        $pokemon = $query->fetch();

        if ($pokemon) {
            var_dump($pokemon);
            die();
            header("Location: ../index.php?pokemon-id=" . $pokemon->id);
            die();
        }
    }

    if (isset($pokemonName)) {
        $url = $API_URL . "/" . $pokemonName;
        $data = file_get_contents($url);
    }

    if (isset($pokemonId) && !$data) {
        $url = $API_URL . "/" . $pokemonId;
        $data = file_get_contents($url);
    }

    if (!$data) {
        echo "Pokemon introuvable";
    }

    try {
        $pokemon = json_decode($data);

        $db->beginTransaction();

        $sql = "INSERT INTO pokemons (id, name, image, sprite, generation) VALUES (:id, :name, :image, :sprite, :generation)";
        $query = $db->prepare($sql);
        $query->execute([
            "id" => $pokemon->id,
            "name" => $pokemon->name,
            "image" => "/public/pokemon_images/" . $pokemon->id . ".png",
            "sprite" => "/public/pokemon_sprites/" . $pokemon->id . ".png",
            "generation" => $pokemon->apiGeneration,
        ]);

        foreach ($pokemon->apiTypes as $type) {
            $sql = "SELECT * FROM types WHERE name LIKE :name";
            $query = $db->prepare($sql);
            $query->execute(["name" => "%" . $type->name . "%"]);
            $typeInDb = $query->fetch();

            if (!$typeInDb) {
                $sql = "INSERT INTO types (name, image) VALUES (:name, :image)";
                $query = $db->prepare($sql);
                $query->execute([
                    "name" => $type->name,
                    "image" => "/public/type_images/" . $type->name . ".png",
                ]);
                $typeId = $db->lastInsertId();
            } else {
                $typeId = $typeInDb->id;
            }

            $sql = "INSERT INTO pokemons_types (pokemonId, typeId) VALUES (:pokemonId, :typeId)";
            $query = $db->prepare($sql);
            $query->execute([
                "pokemonId" => $pokemon->id,
                "typeId" => $typeId,
            ]);
        }

        $sql = "INSERT INTO stats (pokemonId, hp, attack, defense, special_attack, special_defense, speed) VALUES (:pokemonId, :hp, :attack, :defense, :special_attack, :special_defense, :speed)";
        $query = $db->prepare($sql);
        $query->execute([
            "pokemonId" => $pokemon->id,
            "hp" => $pokemon->stats->HP,
            "attack" => $pokemon->stats->attack,
            "defense" => $pokemon->stats->defense,
            "special_attack" => $pokemon->stats->special_attack,
            "special_defense" => $pokemon->stats->special_defense,
            "speed" => $pokemon->stats->speed,
        ]);

        $db->commit();

        var_dump("success");
        die();
        header("Location: ../index.php?pokemon-id=" . $pokemon->id);
        die();
    } catch (Exception $e) {
        $db->rollback();
        echo "Une erreur est survenue";
    }
}

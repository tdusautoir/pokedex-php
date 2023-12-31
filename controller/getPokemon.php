<?php

require_once('../functions.php');
require_once('../db.php');

init_php_session();

$API_URL = "https://pokebuildapi.fr/api/v1/pokemon";

if (isset($_POST["submit"])) {
    if (!(isset($_POST["pokemon-data"]) && !empty($_POST["pokemon-data"]))) {
        create_flash_message("empty_field", "Veuillez remplir le champ", FLASH_ERROR);
        header("Location: ../index.php");
        die();
    }

    if (isset($_POST["pokemon-data"]) && !empty($_POST["pokemon-data"])) {
        $pokemonName = $_POST["pokemon-data"];

        $sql = "SELECT * FROM pokemons WHERE name LIKE :name LIMIT 1";
        $query = $db->prepare($sql);
        $query->execute(["name" => "%" . $pokemonName . "%"]);
        $pokemon = $query->fetch();

        if ($pokemon) {
            header("Location: ../pokemon.php?id=" . $pokemon->id);
            die();
        }
    }

    $pokemonId = $_POST["pokemon-data"];

    $sql = "SELECT * FROM pokemons WHERE id = ? LIMIT 1";
    $query = $db->prepare($sql);
    $query->execute([$pokemonId]);
    $pokemon = $query->fetch();

    if ($pokemon) {
        header("Location: ../pokemon.php?id=" . $pokemon->id);
        die();
    }

    $url = $API_URL . "/" . $_POST["pokemon-data"];
    $data = file_get_contents($url);

    if (!$data) {
        create_flash_message("pokemon_not_found", "Le pokémon n'a pas été trouvé", FLASH_WARNING);
        header("Location: ../index.php");
        die();
    }

    try {
        $pokemon = json_decode($data);

        $image = file_get_contents($pokemon->image);
        $sprite = file_get_contents($pokemon->sprite);

        if ($image && $sprite) {
            file_put_contents("../public/pokemon_images/" . $pokemon->id . ".png", $image);

            if (!file_exists("../public/pokemon_sprites/" . $pokemon->id . ".png")) {
                file_put_contents("../public/pokemon_sprites/" . $pokemon->id . ".png", $sprite);
            }
        }

        // check if exist with pokedex id
        $sql = "SELECT * FROM pokemons WHERE id = ? LIMIT 1";
        $query = $db->prepare($sql);
        $query->execute([$pokemon->id]);
        $pokemonExist = $query->fetch();

        if ($pokemonExist) {
            header("Location: ../pokemon.php?id=" . $pokemon->id);
            die();
        }

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
                if (!file_exists("../public/type_images/" . $type->name . ".png")) {
                    file_put_contents("../public/type_images/" . $type->name . ".png", file_get_contents($type->image));
                }

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

        foreach ($pokemon->apiEvolutions as $evolution) {
            $sql = "SELECT * FROM pokemons_evolutions WHERE name LIKE :name AND pokemonId = :pokemonId AND evolutionPokemonId = :evolutionPokemonId";
            $query = $db->prepare($sql);
            $query->execute(["name" => "%" . $evolution->name . "%", "pokemonId" => $pokemon->id, "evolutionPokemonId" => $evolution->pokedexId]);
            $evolutionInDb = $query->fetch();

            if (!$evolutionInDb) {
                $sql = "INSERT INTO pokemons_evolutions (name, pokemonId, evolutionPokemonId) VALUES (:name, :pokemonId, :evolutionPokemonId)";
                $query = $db->prepare($sql);
                $query->execute(["name" => $evolution->name, "pokemonId" => $pokemon->id, "evolutionPokemonId" => $evolution->pokedexId]);
            }
        }

        if ($pokemon->apiPreEvolution) {
            $sql = "SELECT * FROM pokemons_pre_evolutions WHERE name LIKE :name AND pokemonId = :pokemonId AND evolutionPokemonId = :evolutionPokemonId";
            $query = $db->prepare($sql);
            $query->execute(["name" => "%" . $pokemon->apiPreEvolution->name . "%", "pokemonId" => $pokemon->id, "evolutionPokemonId" => $pokemon->apiPreEvolution->pokedexIdd]);
            $preEvolutionInDb = $query->fetch();

            if (!$preEvolutionInDb) {
                $sql = "INSERT INTO pokemons_pre_evolutions (name, pokemonId, evolutionPokemonId) VALUES (:name, :pokemonId, :evolutionPokemonId)";
                $query = $db->prepare($sql);
                $query->execute(["name" => $pokemon->apiPreEvolution->name, "pokemonId" => $pokemon->id, "evolutionPokemonId" => $pokemon->apiPreEvolution->pokedexIdd]);
            }
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

        create_flash_message("pokemon_saved", "Pokemon enregistré !", FLASH_SUCCESS);
        header("Location: ../pokemon.php?id=" . $pokemon->id);
        die();
    } catch (Exception $e) {
        $db->rollback();
        create_flash_message("error", "Une erreur est survenue", FLASH_ERROR);
        header("Location: ../index.php");
        die();
    }
}

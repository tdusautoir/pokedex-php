<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../functions.php');
require_once('../db.php');

init_php_session();

if (isset($_POST["save-edit"])) {
    if (!(isset($_POST["pokemonId"]) && !empty($_POST["pokemonId"]))) {
        create_flash_message("no id", "Aucun id spécifié", FLASH_ERROR);
        header("Location: ../index.php");
        die();
    }

    $pokemonId = $_POST["pokemonId"];

    if (!(isset($_POST["name"]) && !empty($_POST["name"]))) {
        create_flash_message("no id", "Aucun nom spécifié", FLASH_ERROR);
        header("Location: ../index.php");
        die();
    }

    try {
        if (isset($_FILES['pokemon-picture']['name']) && !empty($_FILES['pokemon-picture']['name'])) {
            if (!(is_uploaded_file($_FILES['pokemon-picture']['tmp_name']))) {
                header("location: ../pokemon.php?id=" . $pokemonId);
                create_flash_message("pokemon_picture_error", "Une erreur est survenue, veuillez réessayer", FLASH_ERROR);
                exit();
            }

            if (!($_FILES['pokemon-picture']['size'] < 2000000)) {
                header("location: ../pokemon.php?id=" . $pokemonId);
                create_flash_message("pokemon_picture_error", "La photo doit être inférieure à 2Mo.", FLASH_ERROR);
                exit();
            }

            $uploadfile = $_FILES['pokemon-picture']['tmp_name'];
            $sourceProperties = getimagesize($uploadfile);
            $newFileName = $pokemonId;
            $uploaddir = dirname(__FILE__) . "/../public/pokemon_images/";
            $ext = pathinfo($_FILES['pokemon-picture']['name'], PATHINFO_EXTENSION);
            $image_width = $sourceProperties[0];
            $image_height = $sourceProperties[1];
            $imageType = $sourceProperties[2];
            $newImage_width = $image_width / $image_height * 500;
            $newImage_height = $image_height / $image_height * 500;

            switch ($imageType) {
                case IMAGETYPE_PNG: //$imageType == 3
                    $imageSrc = imagecreatefrompng($uploadfile);
                    $tmp = imageResize($newImage_width, $newImage_height, $imageSrc, $image_width, $image_height);
                    imagepng($tmp, $uploaddir . $newFileName . "." . $ext);
                    break;

                case IMAGETYPE_JPEG: //$imageType == 2
                    $imageSrc = imagecreatefromjpeg($uploadfile);
                    $tmp = imageResize($newImage_width, $newImage_height, $imageSrc, $image_width, $image_height);
                    imagejpeg($tmp, $uploaddir . $newFileName . "." . $ext);
                    break;

                case IMAGETYPE_GIF: //$imageType == 1
                    $imageSrc = imagecreatefromgif($uploadfile);
                    $tm = imageResize($newImage_width, $newImage_height, $imageSrc, $image_width, $image_height);
                    imagegif($tmp, $uploaddir . $newFileName . "." . $ext);
                    break;

                default:
                    header("location: ../pokemon.php?id=" . $pokemonId);
                    create_flash_message("pokemon_picture_error", "Le type de votre image doit être jpeg ou png.", FLASH_ERROR);
                    exit();
                    break;
            }
        }

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

if (
    isset($_SERVER['CONTENT_LENGTH'])
    && (int) $_SERVER['CONTENT_LENGTH'] > convertToBytes(ini_get('post_max_size') . 'B')
) {
    create_flash_message("pokemon_picture_error", "Votre fichier est trop large.", FLASH_ERROR);
    header("Location: ../pokemon.php?id=" . $_GET["pokemonId"]);
    exit();
}

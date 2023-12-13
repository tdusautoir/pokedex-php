<?php

//Constants
const FLASH = 'FLASH_MESSAGES';
const FORM = 'FORM_INFO';

//flash type
const FLASH_ERROR = 'error';
const FLASH_WARNING = 'warning';
const FLASH_INFO = 'info';
const FLASH_SUCCESS = 'success';

function dd($var) //function for debug
{
    echo "<pre style='font-size: 18px'>";
    print_r($var);
    echo "</pre>";
    die();
}

function dump($var) //function for debug
{
    echo "<pre style='font-size: 18px'>";
    print_r($var);
    echo "</pre>";
}

function init_php_session(): bool //init php session
{
    if (!session_id()) {
        session_start();
        session_regenerate_id();
        return true;
    }
    return false;
}

function clean_php_session(): void //clean the php session
{
    session_unset();
    session_destroy();
}


//create a flash message
function create_flash_message(string $name, string $message, string $type): void
{
    if (isset($_SESSION[FLASH][$name])) {
        unset($_SESSION[FLASH][$name]);
    }
    $_SESSION[FLASH][$name] = ['message' => $message, 'type' => $type];
}

//check if flash message is define by type
function isset_flash_message_by_type(string $type): bool
{
    if (isset($_SESSION[FLASH])) {
        foreach ($_SESSION[FLASH] as $key => $value) {
            if ($value['type'] == $type) {
                return true;
            } else {
                return false;
            }
        }
    }
    return false;
}

//delete flash message by type
function delete_flash_message_by_type(string $type): bool
{
    if (isset($_SESSION[FLASH])) {
        foreach ($_SESSION[FLASH] as $key => $value) {
            if ($value['type'] == $type) {
                unset($_SESSION[FLASH][$key]);
            } else {
                return false;
            }
        }
    }
    return false;
}

//Display flash message by type
function display_flash_message_by_type(string $type): void
{
    if (isset($_SESSION[FLASH])) {
        foreach ($_SESSION[FLASH] as $key => $value) {
            if ($value['type'] == $type) {

                $flash_message = $value['message'];
                unset($_SESSION[FLASH][$key]);
                echo $flash_message;
            }
        }
    }
}

function build_url(string $param): string
{
    $currentURL = $_SERVER['REQUEST_URI'];

    if (strpos($currentURL, 'index.php') === false) {
        $currentURL = './index.php';
    }

    $separator = (parse_url($currentURL, PHP_URL_QUERY) == NULL) ? '?' : '&'; // Vérifie si des paramètres existent déjà dans l'URL

    $param_value = explode('=', $param)[1];
    $param_index = explode('=', $param)[0];

    if (isset($_GET[$param_index])) {
        $currentURL = str_replace($param_index . '=' . $_GET[$param_index], $param_index . '=' . $param_value, $currentURL);
        return $currentURL;
    }

    $newURL = $currentURL . $separator . $param;
    return $newURL;
}


function remove_from_current_url(string $param_to_remove): string
{
    $currentURL = $_SERVER['REQUEST_URI'];

    if (strpos($currentURL, 'index.php') === false) {
        return './index.php';
    }

    $urlParts = parse_url($currentURL);

    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);

        if (isset($queryParams[$param_to_remove])) {
            unset($queryParams[$param_to_remove]);

            $newQuery = http_build_query($queryParams);

            $newURL = $urlParts['path'];
            if ($newQuery !== '') {
                $newURL .= '?' . $newQuery;
            }

            return $newURL;
        }
    }

    return $currentURL;
}

function remove_from_url(string $url, string $param_to_remove)
{
    $urlParts = parse_url($url);

    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);

        if (isset($queryParams[$param_to_remove])) {
            unset($queryParams[$param_to_remove]);

            $newQuery = http_build_query($queryParams);

            $newURL = $urlParts['path'];
            if ($newQuery !== '') {
                $newURL .= '?' . $newQuery;
            }

            return $newURL;
        }
    }

    return $url;
}

// Check if a value is a integer (
// "23" return true, 23 return true, 23.4 return false
function isInteger($input)
{
    return (ctype_digit(strval($input)));
}

function imageResize($newImageWidth, $newImageHeight, $imageSrc, $imageWidth, $imageHeight) //redimensioner une image et la copier dans un dossier
{
    $newImageLayer = imagecreatetruecolor($newImageWidth, $newImageHeight); //créer l'images et ses couleurs selon sa nouvelle hauteur et largeur

    imagecopyresampled($newImageLayer, $imageSrc, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $imageWidth, $imageHeight); //assembler l'image selon sa nouvelle hauteur et largeur et la copier dans un dossier

    return $newImageLayer; //retourne true si l'opération est un succés.
}

function convertToBytes(string $from): ?int
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $number = substr($from, 0, -2);
    $suffix = strtoupper(substr($from, -2));

    //B or no suffix
    if (is_numeric(substr($suffix, 0, 1))) {
        return preg_replace('/[^\d]/', '', $from);
    }

    $exponent = array_flip($units)[$suffix] ?? null;
    if ($exponent === null) {
        return null;
    }

    return $number * (1024 ** $exponent);
}

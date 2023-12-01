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

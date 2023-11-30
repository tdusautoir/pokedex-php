<?php

function dd($var) //function for debug
{
    echo "<pre style='font-size: 18px'>";
    print_r($var);
    echo "</pre>";
    die();
}

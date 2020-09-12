<?php

$matches = [];
 echo $_SERVER["REQUEST_URI"];
if ( in_array($_SERVER["REQUEST_URI"], ['/index.html', '/','' ] ) ){
    echo_file_get_contents('index.html');

    die;
}

if (preg_match('/\/([^\/]+)\/([^\/]+)/', $_SERVER["REQUEST_URI"], $matches)) {
    $_GET['tipo_recurso'] = $matches[1];
    $_GET['id_recurso'] = $matches[2];

    error_log( print_r($matches, 1) );
    require 'server.php';
} elseif ( preg_match('/\/([^\/]+)\/?/', $_SERVER["REQUEST_URI"], $matches) ) {
    $_GET['tipo_recurso'] = $matches[1];
    error_log( print_r($matches, 1) );

    require 'server.php';
} else {

    error_log('No matches');
    http_response_code( 404 );
}
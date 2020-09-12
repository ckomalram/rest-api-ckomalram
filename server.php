<?php
/*levantar con php -S localhost:8000 server.php */

/*
$user = array_key_exists('PHP_AUTH_USER', $_SERVER)? $_SERVER['PHP_AUTH_USER'] : '';
$pwd = array_key_exists('PHP_AUTH_PW', $_SERVER)? $_SERVER['PHP_AUTH_PW'] : '';

if($user !=='carlyle' || $pwd!=='1234'){
    die;
}*/

// HMAC
// if(
// !array_key_exists('HTTP_X_HASH', $_SERVER) ||
// !array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) ||
// !array_key_exists('HTTP_X_UID', $_SERVER) 
// ){
//     die;
// }

// list($hash, $uid, $timestamp) = [
//     $_SERVER['HTTP_X_HASH'],
//     $_SERVER['HTTP_X_UID'],
//     $_SERVER['HTTP_X_TIMESTAMP']
// ];

// $secret ='1234';

// $newHash = sha1($uid.$timestamp.$secret);

// if($newHash !== $hash){
//     die;
// }

if(!array_key_exists('HTTP_X_TOKEN', $_SERVER)){
    http_response_code( 400 );
    die;
}

$url = 'http://localhost:8001';
$ch = curl_init($url);
curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
        "X-Token: {$_SERVER['HTTP_X_TOKEN']}"
    ]
);
curl_setopt($ch,
CURLOPT_RETURNTRANSFER,
true);

$ret = curl_exec($ch);

if ($ret !== 'true')
{
    die;
}

//definir recursos disponibles
$tipoRecursosPermitidos = ['books', 'authors', 'genres'];

//validar que recurso que viene por petición get es valido en el array
$tipoRecurso = $_GET['tipo_recurso'];

if(!in_array($tipoRecurso , $tipoRecursosPermitidos)){
    http_response_code( 400 );
    die;
}

$books= [
    1 =>[
        'titulo' => 'Carlyle Komalram',
        'id_autor' => 1,
        'id_genero' => 1,
    ],
    2 =>[
        'titulo' => 'Komalram',
        'id_autor' => 2,
        'id_genero' => 2,
    ],
    3 =>[
        'titulo' => 'Carlyle',
        'id_autor' => 3,
        'id_genero' => 3,
    ],
];


header('Content-Type: Application/json');
//conseguimos el id del recurso buscado
$idRecurso = array_key_exists('id_recurso',  $_GET)? $_GET['id_recurso'] : '';
//generamos respuestas segun la acción
switch(strtoupper($_SERVER['REQUEST_METHOD'])){
    case 'GET':
        if (empty($idRecurso)){
            echo json_encode($books);
        }else{
            if(array_key_exists($idRecurso,$books )){
                echo json_encode($books[$idRecurso]);
            }
        }
        
        break;
    case 'POST':
         $json = file_get_contents('php://input');
       //   echo $json;
         $books[] = json_decode($json,true);

       // echo array_keys($books)[count($books) -1];
       echo json_encode ($books);
        break;
    case 'PUT':
            //validar que el recurso buscado exista
            if (!empty($idRecurso) && array_key_exists($idRecurso , $books)){
                $json = file_get_contents('php://input');
                $books[$idRecurso] =  json_decode($json, true);
                echo json_encode ($books);
            }
    break;
    case 'DELETE':
            //VALIDAMOS QUE EXISTA EL RECURSO
            if (!empty($idRecurso) && array_key_exists($idRecurso , $books)){
                unset($books[$idRecurso]);
                echo json_encode ($books);
            }
    break;
}
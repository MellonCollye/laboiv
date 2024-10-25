<?php
session_start();
If(!isset($_SESSION['idSesion'])) {
    exit;
}

include('./modelo.php');
$requestMethod = $_SERVER['REQUEST_METHOD'];
$capPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = strtolower($capPath);
function handleGetData($params) {
    header('Content-Type: application/json');
    readPintores($params);
}

function handleGetOptions() {
    header('Content-Type: application/json');
    readEstilos();
}

function handlePostData($params) {
    header('Content-Type: application/json');
    createPintor($params);
}

function handlePatchData($params) {
    header('Content-Type: application/json');
    updatePintor($params);
}

function handleDeleteData($params) {
    header('Content-Type: application/json');
    deletePintor($params);
}

$routes = [
    'GET' => [
        '/laboratorioiii/php/sesion/src/controlador.php/data' => 'handleGetData',
        '/laboratorioiii/php/sesion/src/controlador.php/options' => 'handleGetOptions'
    ],
    'POST' => [
        '/laboratorioiii/php/sesion/src/controlador.php/data' => 'handlePostData',
         '/laboratorioiii/php/sesion/src/controlador.php/delete' => 'handleDeleteData'
    ],
    'PATCH' => [
        '/laboratorioiii/php/sesion/src/controlador.php/data' => 'handlePatchData'
    ],
];

if (isset($routes[$requestMethod][$path])) {
    switch ($requestMethod) {
        case 'GET':
            call_user_func($routes[$requestMethod][$path], $_GET);
            break;
        case 'POST':
           call_user_func($routes[$requestMethod][$path], $_POST);
           break;
        case 'PATCH':
            parse_str(file_get_contents('php://input'), $patchVars);
            call_user_func($routes[$requestMethod][$path], $patchVars);
            break;
        case 'DELETE':
            parse_str(file_get_contents('php://input'), $deleteVars);
            call_user_func($routes[$requestMethod][$path], $deleteVars);
            break;
        default:
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(['error' => 'Método no permitido']);
            break;
    }
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['error' => 'Ruta no encontrada']);
}
?>

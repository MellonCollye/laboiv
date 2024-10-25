<?php
session_start();
If(!isset($_SESSION['idSesion'])) {
    exit;
}

include('./dbInterface.php');
include('./utils.php');

$methods = [
    'get-user' => 'handleGetUser',
    'create-user' => 'handleCreateUser',
    'delete-user' => 'handleDeleteUser',
];

function handleRpcRequest($rpcRequest) {
    call_user_func($methods[$rpcRequest->$method], $rpcRequest->$body);
}

function validateRpcRequest($rpcRequest){
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if($requestMethod != 'POST'){
        $error = new Error('This server only responds to POST');
        return;
    }
    if($rpcRequest){
        //$rpcRequest->$method esta en $methods
        handleRpcRequest($rpcRequest);
    }else{
        $error = new Error('Rpc method not found');
    }
}

validateRpcRequest($_POST);
?>

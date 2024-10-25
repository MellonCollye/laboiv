<?php
include('./auth.php');
$json = '';
session_start();
If(isset($_SESSION['idSesion'])) {
    $json = json_encode(['sessionCount' => getSessionCount($_POST['user'])]);
    exit;
}

$user = htmlspecialchars($_POST['user']);
$password = htmlspecialchars($_POST['password']);

if ( auth( $user, $password ) ) {

    $_SESSION['idSesion'] = session_create_id();
    $_SESSION['name'] = $user;

    $json = json_encode(['sessionCount' => getSessionCount($user)]);
}

echo $json;

?> 
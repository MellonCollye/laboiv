<?php
    session_start();
    If(!isset($_SESSION['idSesion'])) {
        exit;
    }

    header('location: index.html');
    exit;
    ;
?> 
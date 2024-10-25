<?php
session_start();
If(!isset($_SESSION['idSesion'])) {
    header('location: /LaboratorioIII/Php/Sesion/login.html');
    exit;
}
session_unset();
session_destroy();

header('location: /LaboratorioIII/Php/Sesion/login.html');

exit;

?> 
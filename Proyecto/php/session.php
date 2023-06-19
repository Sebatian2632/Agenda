<?php
 /* 
Nombre del programa: session.php
Descripción: Este documento funciona para saber si existe una sesion o es nula y recuperar el correo del
usuario que ha iniciado sesión
Funciones: ninguna
*/ 

    session_start();
    $correo = isset($_SESSION['correo']) ? $_SESSION['correo'] : null;
    echo json_encode(array('correo' => $correo));
?>


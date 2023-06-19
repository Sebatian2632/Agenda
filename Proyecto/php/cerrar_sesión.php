<?php
 /* 
Nombre del programa: cerrar_sesión.php
Descripción: Este documento permite cerrar la sesión que está utilizando en usuario
Funciones: ninguna
*/ 

    session_start();
    session_destroy();
    header('Location:../html/login.html')
?>
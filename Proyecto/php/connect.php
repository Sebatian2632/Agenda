<?php
 /* 
Nombre del programa: connect.php
Descripción: Este documento permite hacer la conexión con la base de datos
Funciones: ninguna
*/ 

    $conex=mysqli_connect("localhost","root","","agendaDB");
    if(!$conex){
        die("Error al conectarse a la base de datos: ".mysqli_connect_error());
    }
        
?>
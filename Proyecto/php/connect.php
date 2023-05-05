<?php
        $conex=mysqli_connect("localhost","root","","agendaDB");
        if(!$conex){
                die("Error al conectarse a la base de datos: ".mysqli_connect_error());
        }
        
?>
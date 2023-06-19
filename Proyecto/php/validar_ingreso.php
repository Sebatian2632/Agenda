<?php
 /* 
Nombre del programa: validar_ingreso.php
Descripción: Este documento permite revisar que el usuario con el que se desea iniciar sesión existe y
redireccionarlo a la pantalla de incio
Funciones: ninguna
*/ 

include('connect.php');
session_start();

$usuario = $_POST['correo'];
$contrasena = $_POST['clave'];

$_SESSION['correo'] = $usuario;

$consulta = "SELECT * FROM usuario WHERE correo= '$usuario' and contrasena ='$contrasena'";
$resultado = mysqli_query($conex,$consulta);
$datos = mysqli_fetch_array($resultado);
$filas = mysqli_num_rows($resultado);


if($filas)
{
    header("location:../html/calendario.html");
}
else{
    echo "<script>
        document.getElementById('error-message').style.display = 'block';
    |   </script>";
    header("location:../html/login.html");
}

mysqli_free_result($resultado);
mysqli_close($conex);
?>

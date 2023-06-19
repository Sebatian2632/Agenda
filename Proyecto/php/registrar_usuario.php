<?php
 /* 
Nombre del programa: registar_usuario.php
Descripción: Este documento permite revisar que el usuario con el que se desea iniciar sesión existe y
redireccionarlo a la pantalla de incio
Funciones: ninguna
*/ 

include('connect.php');

$usuario=$_POST['usuario'];
$correo=$_POST['correo'];
$contrasena=$_POST['clave'];

$consulta = "SELECT * FROM usuario WHERE correo = '$correo'";
$resultado = mysqli_query($conex,$consulta);
$rconsulta = mysqli_num_rows($resultado);

if($rconsulta)
{
    echo "
    <script>
        alert('El correo ya se encuentra asociado a un usuario');
        window.location = '../html/register.html';
    </script>
    ";
}
else
{
    $query = "INSERT INTO usuario(correo,contrasena,nom_usuario) VALUES ('$correo','$contrasena','$usuario')";
    $resultado = mysqli_query($conex,$query);
    echo "
    <script>
        alert('El usuario se registro correctamente');
        window.location = '../html/login.html';
    </script>
    ";

    if($resultado)
{
    echo "
    <script>
        document.getElementById('good').style.display = 'none';
    </script>
    ";

}
}

?>
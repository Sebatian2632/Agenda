<?php
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
        window.location = '../html/register.html';
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
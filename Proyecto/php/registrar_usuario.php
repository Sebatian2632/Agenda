<?php
include('connect.php');

$usuario=$_POST['usuario'];
$correo=$_POST['correo'];
$contrasena=$_POST['clave'];

$consulta = "SELECT * FROM usuarios WHERE correo = '$correo'";
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
    $query = "INSERT INTO usuarios(correo,clave,nombre) VALUES ('$correo','$contrasena','$usuario')";
    $resultado = mysqli_query($conex,$query);

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
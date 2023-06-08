<?php
include('connect.php');

// Obtener el correo electrónico del formulario de recuperación
$correo = $_POST['correo'];

// Consultar la tabla de usuarios para verificar el correo electrónico
$query = "SELECT idUsuario FROM usuario WHERE correo = '$correo'";
$result = mysqli_query($conex, $query);
$row = mysqli_fetch_assoc($result);

if ($row) {
  // Generar un código de recuperación de contraseña aleatorio
  $codigo = bin2hex(random_bytes(16));

  // Guardar el código de recuperación en la base de datos
  $query = "UPDATE usuario SET codigo_recuperacion = '$codigo' WHERE correo = '$correo'";
  mysqli_query($conex, $query);

  // Enviar un correo electrónico al usuario con el código de recuperación y un enlace a una página donde pueda ingresar una nueva contraseña
  $resetLink = "http://ejemplo.com/restablecer.php?codigo=" . $codigo;
  $mensaje = "Hemos recibido una solicitud para restablecer la contraseña de su cuenta.\n\n";
  $mensaje .= "Para continuar, haga clic en el siguiente enlace:\n\n";
  $mensaje .= "$resetLink\n\n";
  $mensaje .= "Si no solicitó el restablecimiento de su contraseña, ignore este mensaje.\n";

  ini_set("SMTP", "smtp.gmail.com");
  ini_set("smtp_port", "587");

  mail($correo, "Recuperación de contraseña", $mensaje);

  echo "Se ha enviado un correo electrónico con las instrucciones para restablecer su contraseña.";
} else {
  // Mostrar un mensaje de error si el correo electrónico no se encuentra en la base de datos
  echo "El correo electrónico ingresado no se encuentra en nuestra base de datos.";
}

?>

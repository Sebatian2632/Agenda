<?php
 /* 
Nombre del programa: calendario.php
Descripción: Este documento permite recuperar los eventos (tareas) para mostrarlos en el calendario
Funciones: ninguna
*/ 

include 'connect.php';



// Realiza la consulta para obtener los eventos desde la base de datos (TAREAS)
session_start();
$correo = isset($_SESSION['correo']) ? $_SESSION['correo'] : null;

$query = "SELECT idtareas, nom_tarea, fecha
FROM tareas
JOIN compartir ON tareas.idtareas = compartir.tareas_idtareas
JOIN usuario ON usuario.idUsuario = compartir.usuario_idUsuario
WHERE usuario.correo = '$correo';";
$stmt = $conex->query($query);

// Prepara un array para almacenar los eventos
$eventos = [];

// Recorre los resultados de la consulta y agrega los eventos al array
while ($row = $stmt->fetch_assoc()) {
  $evento = [
    'title' => $row['nom_tarea'],
    'start' => $row['fecha']
  ];
  $eventos[] = $evento;
}

// Retorna los eventos como respuesta en formato JSON
echo json_encode($eventos);
?>
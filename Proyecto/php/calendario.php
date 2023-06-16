<?php
include 'connect.php';

// Realiza la consulta para obtener los eventos desde la base de datos (TAREAS)
$query = "SELECT idtareas, nom_tarea, fecha FROM tareas";
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
<?php
include 'connect.php';

// Realiza la consulta para obtener los eventos desde la base de datos
$query = "SELECT idtareas, nom_tarea, fecha FROM tareas";
$stmt = $conex->query($query);

$query2 = "SELECT idhabitos, nom_habito, lunes, martes, miercoles, jueves, viernes, sabado, domingo FROM habitos";
$stmt2 = $conex->query($query2);

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

while ($row = $stmt2->fetch_assoc()) {
  $evento = [
    'title' => $row['nom_habito'],
    'start' => $row['lunes'],
    'start' => $row['martes'],
    'start' => $row['miercoles'],
    'start' => $row['jueves'],
    'start' => $row['viernes'],
    'start' => $row['sabado'],
    'start' => $row['domingo']
  ];
  $eventos[] = $evento;
}

// Retorna los eventos como respuesta en formato JSON
echo json_encode($eventos);
?>
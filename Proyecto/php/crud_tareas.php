<?php
    
    //Conexión a la base de datos
    include 'connect.php';
    $Respuesta = array();

    $data = json_decode(file_get_contents('php://input'), true);
    $accion = isset($data['accion']) ? $data['accion'] : '';

    switch ($accion) {
        case 'create':
            actionCreatePHP($conex);
            break;
        case 'update':
            actionUpdatePHP($conex);
            break;
        case 'delete':
            actionDeletePHP($conex);
            break;
        case 'read':
            actionRead($conex);
            break;
        case 'read_id':
            actionReadByIdPHP($conex);
            break;
        default:
            # code...
            break;
    }

    function actionCreatePHP($conex)
    {
        //Recuperación de los datos
        $data = json_decode(file_get_contents('php://input'), true);    //Parte para decodificar lo que recibimos del js

        $nombre = $data['nombre'];
        $fecha = $data['fecha'];  
        $lugar = $data['lugar'];
        $duracion = $data['duracion'];
        $descripcion = $data['descripcion'];
        $prioridad = $data['prioridad'];
        $idUsuario = isset($data['idUsuario']) ? $data['idUsuario'] : '';

        //Inserta los datos de la Nueva tarea en la base de datos
        $consultainsert = "INSERT INTO `tareas`(`nom_tarea`, `fecha`, `lugar`, `duracion`, `descripcion`, `prioridad`, `estado`) VALUES ('$nombre','$fecha','$lugar','$duracion','$descripcion','$prioridad',0)";
        $resultadoinsert = mysqli_query($conex,$consultainsert);
        if($resultadoinsert){
            //Consulta el ID de la tarea creada
            $consultaid =  "SELECT idtareas FROM tareas WHERE nom_tarea = '$nombre'";
            $resultadoid = mysqli_query($conex,$consultaid);
            if(mysqli_num_rows($resultadoid)==1)
            {
                $idtareas = mysqli_fetch_assoc($resultadoid)['idtareas']; 
                //Crea la relación entre el usuario y la tarea, pone al usuario como propietario
                $consultainsert2 = "INSERT INTO `compartir`(`propietario`, `usuario_idUsuario`, `tareas_idtareas`) VALUES (1,'$idUsuario','$idtareas')";
                $resultadoinsert2 = mysqli_query($conex,$consultainsert2);
                if($resultadoinsert){
                    echo json_encode(['Respuesta' => 1]);
                }
            }else{
                echo json_encode(['Respuesta' => 0]);
            }
        }
    }
?>
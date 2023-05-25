<?php
    
    //Conexión a la base de datos
    include 'connect.php';
    $Respuesta = array();

    //$data = json_decode(file_get_contents('php://input'), true);
    //$accion = isset($data['accion']) ? $data['accion'] : '';
    $accion    = $_POST['accion'];

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
            actionReadPHP($conex);
            break;
        case 'read_id':
            actionReadByIdPHP($conex);
            break;
        default:
            # code...
            break;
    }

    function actionCreatePHP($conex){
        $nom_tarea = $_POST['nom_tarea'];
        $fecha = $_POST['fecha'];  
        $lugar = $_POST['lugar'];
        $duracion = $_POST['duracion'];
        $descripcion = $_POST['descripcion'];
        //$prioridad = $_POST['prioridad'];

        //Inserta los datos de la Nueva tarea en la base de datos
        // $QueryCreate = "INSERT INTO `tareas`(`idtareas`, `nom_tarea`, `fecha`, `lugar`, `duracion`, `descripcion`, `prioridad`, `estado`) 
        //                 VALUES (NULL, '$nom_tarea','$fecha','$lugar','$duracion','$descripcion','$prioridad',0)";
        $QueryCreate = "INSERT INTO `tareas`(`idtareas`, `nom_tarea`, `fecha`, `lugar`, `duracion`, `descripcion`, `estado`) 
                        VALUES (NULL, '$nom_tarea','$fecha','$lugar','$duracion','$descripcion',0)";

        if(mysqli_query($conex,$QueryCreate)){
            $Respuesta['estado'] = 1;
            $Respuesta['mensaje'] = "El registro se guardo correctamente";
            $Respuesta['id'] = mysqli_insert_id($conex);
        }
        else{
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "Ocurrio un error desconocido";
            $Respuesta['id'] = -1;
        }
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    function actionReadPHP($conex) {
        $QueryRead = "SELECT * FROM tareas";
        $ResultadoRead = mysqli_query($conex, $QueryRead);
        $numeroRegistros = mysqli_num_rows($ResultadoRead);

        if ($numeroRegistros > 0) {
            $Respuesta['estado'] = 1;
            $Respuesta['mensaje'] = "Los registros se listan correctamente";
            $Respuesta['entregas'] = array(); // Inicializar la matriz de tareas
            
            while ($RenglonEntrega = mysqli_fetch_assoc($ResultadoRead)) {
                $Entrega = array();
                $Entrega['idtareas'] = $RenglonEntrega['idtareas'];
                $Entrega['nom_tarea'] = $RenglonEntrega['nom_tarea'];
                $Entrega['fecha'] = $RenglonEntrega['fecha'];
                $Entrega['descripcion'] = $RenglonEntrega['descripcion'];
                $Entrega['duracion'] = $RenglonEntrega['duracion'];
                $Entrega['prioridad'] = $RenglonEntrega['prioridad'];
                $Entrega['estado'] = $RenglonEntrega['estado'];
                
                array_push($Respuesta['entregas'], $Entrega);
            }
        } else {
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "Lo siento, pero no hay registros para mostrar";
        }
        
        echo json_encode($Respuesta);
        mysqli_close($conex); 
    }
    

    function actionUpdatePHP($conex){
        $id = $_POST['id'];
        $nom_tarea = $_POST['nom_tarea'];
        $fecha = $_POST['fecha'];
        $lugar = $_POST['lugar'];
        $duracion = $_POST['duracion'];
        $descripcion = $_POST['descripcion'];
        $prioridad = $_POST['prioridad'];

        $queryUpdate   = "UPDATE tareas SET 
                         id_tareas='".$nom_tarea."',
                         nom_tarea='".$fecha."', 
                         lugar='".$lugar."',
                         fecha='".$fecha."',
                         duracion='".$duracion."'
                         descripcion='".$descripcion."'
                         prioridad='".$prioridad."'
                         WHERE idtareas=".$id;

        mysqli_query($conex,$queryUpdate);        
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    function actionReadByIdPHP($conex){
        //$id                  = $_POST['id'];
        $id = 17;
        $queryReadById       = "SELECT * FROM tareas WHERE idtareas=".$id;
        $resultById          = mysqli_query($conex,$queryReadById);
        $numeroRegistrosById = mysqli_num_rows($resultById);

        if($numeroRegistrosById>0){
            $Respuesta['estado']  = 1;
            $Respuesta['mensaje'] = "Registro encontrado";
             
            $RenglonEntregaById = mysqli_fetch_assoc($resultById);

            $Respuesta['idtareas'] = $RenglonEntregaById['idtareas'];
            $Respuesta['nom_tarea'] = $RenglonEntregaById['nom_tarea'];
            $Respuesta['fecha'] = $RenglonEntregaById['fecha'];
            $Respuesta['lugar'] = $RenglonEntregaById['lugar'];
            $Respuesta['duracion'] = $RenglonEntregaById['duracion'];
            $Respuesta['descripcion'] = $RenglonEntregaById['descripcion'];
            $Respuesta['prioridad'] = $RenglonEntregaById['prioridad'];
        }else{
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "No se encuentra el registro";
        }
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    function actionDeletePHP($conex){
        //$id            = $_POST['id'];
        $id = '17';
        $queryEliminar = "DELETE * FROM tareas WHERE idtareas=".$id;
        mysqli_query($conex,$queryEliminar);
        if(mysqli_affected_rows($conex)>0)
        {
            $Respuesta['estado']  = 1;
            $Respuesta['mensaje'] = "El registro se elimino correctamente";
        }else{
            $Respuesta['estado']  = 0;
            $Respuesta['mensaje'] = "Ocurrio un error desconcido";
        }
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }
?>
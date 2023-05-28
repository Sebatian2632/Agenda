<?php
    
    //Conexión a la base de datos
    include 'connect.php';
    $Respuesta = array();
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
        case 'read_idAct':
            actionReadByIdPHP($conex);
            break;
        default:
            # code...
            break;
    }

    function actionCreatePHP($conex){
        if (isset($_POST['correo'])) {
            $correo = $_POST['correo'];
            
            // Realizar una consulta para obtener el ID del usuario según el correo
            $QueryCorreo = "SELECT idUsuario FROM usuario WHERE correo = '$correo'";
            $ResultadoCorreo = mysqli_query($conex, $QueryCorreo);
            
            // Verificar si se obtuvo algún resultado
            if ($ResultadoCorreo && mysqli_num_rows($ResultadoCorreo) > 0) {
                $fila = mysqli_fetch_assoc($ResultadoCorreo);
                $idcorreo = $fila['idUsuario'];
            }
        }   
        $nom_tarea = $_POST['nom_tarea'];
        $fecha = $_POST['fecha'];  
        $lugar = $_POST['lugar'];
        $duracion = $_POST['duracion'];
        $descripcion = $_POST['descripcion'];

        $QueryCreate = "INSERT INTO `tareas`(`idtareas`, `nom_tarea`, `fecha`, `lugar`, `duracion`, `descripcion`, `estado`) 
                        VALUES (NULL, '$nom_tarea','$fecha','$lugar','$duracion','$descripcion',0)";
                        
        if(mysqli_query($conex,$QueryCreate)){
            $QueryLeerId = "SELECT idtareas FROM tareas WHERE (nom_tarea = '$nom_tarea' AND fecha = '$fecha' 
                            AND lugar = '$lugar' AND duracion = '$duracion' AND descripcion = '$descripcion')";

            $ResultadoLeerId = mysqli_query($conex, $QueryLeerId);

            if($ResultadoLeerId && mysqli_num_rows($ResultadoLeerId) > 0 ){
                $fila = mysqli_fetch_assoc($ResultadoLeerId);
                $idtareaRecup = $fila['idtareas'];

                $QueryPropietario = "INSERT INTO `compartir`(`tareas_idtareas`, `usuario_idUsuario`, `propietario`) 
                            VALUES ('$idtareaRecup','$idcorreo',1)";

                if(mysqli_query($conex,$QueryPropietario)){
                    $Respuesta['estado'] = 1;
                    $Respuesta['mensaje'] = "El registro se guardo correctamente";
                    $Respuesta['id'] = mysqli_insert_id($conex);   

                    echo json_encode($Respuesta);
                    mysqli_close($conex);   
                }else{
                    $Respuesta['estado'] = 0;
                    $Respuesta['mensaje'] = "Ocurrio un error desconocido 1";
                    $Respuesta['id'] = -1;
        
                    echo json_encode($Respuesta);
                    mysqli_close($conex);   
                }

            }else{
                $Respuesta['estado'] = 0;
                $Respuesta['mensaje'] = "Ocurrio un error desconocido 2";
                $Respuesta['id'] = -1;

                echo json_encode($Respuesta);
                mysqli_close($conex);   
            }
        }else{
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "Ocurrio un error desconocido 3";
            $Respuesta['id'] = -1;

            echo json_encode($Respuesta);
            mysqli_close($conex);   
        }
    }

    function actionReadPHP($conex) {
        if (isset($_POST['correo'])) {
            $correo = $_POST['correo'];
            
            // Realizar una consulta para obtener el ID del usuario según el correo
            $QueryCorreo = "SELECT idUsuario FROM usuario WHERE correo = '$correo'";
            $ResultadoCorreo = mysqli_query($conex, $QueryCorreo);
            
            // Verificar si se obtuvo algún resultado
            if ($ResultadoCorreo && mysqli_num_rows($ResultadoCorreo) > 0) {
                $fila = mysqli_fetch_assoc($ResultadoCorreo);
                $idcorreo = $fila['idUsuario'];
            }
        }        

        $QueryRead =    "SELECT * FROM tareas JOIN compartir ON compartir.tareas_idtareas = tareas.idtareas
                        WHERE compartir.usuario_idUsuario = '$idcorreo'";
        $ResultadoRead = mysqli_query($conex, $QueryRead);
        $numeroRegistros = mysqli_num_rows($ResultadoRead);

        if ($numeroRegistros > 0) {
            $Respuesta['estado'] = 1;
            $Respuesta['mensaje'] = "Los registros se listan correctamente";
            $Respuesta['entregas'] = array();
            
            while ($RenglonEntrega = mysqli_fetch_assoc($ResultadoRead)) {
                $Entrega = array();
                $Entrega['idtareas'] = $RenglonEntrega['idtareas'];
                $Entrega['nom_tarea'] = $RenglonEntrega['nom_tarea'];
                $Entrega['fecha'] = $RenglonEntrega['fecha'];
                $Entrega['descripcion'] = $RenglonEntrega['descripcion'];
                $Entrega['duracion'] = $RenglonEntrega['duracion'];
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

        $queryEstadoAct = "SELECT estado FROM tareas WHERE idtareas=".$id;
        $resultEstadoAct = mysqli_query($conex,$queryEstadoAct);
        $numeroEstadoAct = mysqli_num_rows($resultEstadoAct);

        $queryUpdate   = "UPDATE tareas SET
                         nom_tarea='".$nom_tarea."', 
                         lugar='".$lugar."',
                         fecha='".$fecha."',
                         duracion='".$duracion."',
                         descripcion='".$descripcion."'
                         WHERE idtareas=".$id;

        if(mysqli_query($conex,$queryUpdate)){
            if($numeroEstadoAct>0){
                $RenglonEntregaById = mysqli_fetch_assoc($resultEstadoAct);
                $Respuesta['estado'] = 1;
                $Respuesta['estadoAct'] = $RenglonEntregaById['estado'];
                if(mysqli_affected_rows($conex)>0){     
                    $Respuesta['mensaje'] = "La tarea se actualizó correctamente";
                }else{
                    $Respuesta['mensaje'] = "No se realizaron cambios";
                }
            }
        }else{
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "Ocurrio un error desconocido";
        } 
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    function actionReadByIdPHP($conex){
        $id                  = $_POST['id'];
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
            $Respuesta['estadoAct'] = $RenglonEntregaById['estado'];
        }else{
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "No se encuentra el registro";
        }
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    function actionDeletePHP($conex){
        $id = $_POST['id'];
        $queryEliminarRelacion = "DELETE FROM compartir WHERE tareas_idtareas=".$id;
        mysqli_query($conex,$queryEliminarRelacion);

        if(mysqli_affected_rows($conex)>0){
            $queryEliminar = "DELETE FROM tareas WHERE idtareas=".$id;
            mysqli_query($conex,$queryEliminar);

            if(mysqli_affected_rows($conex)>0){
                $Respuesta['estado']  = 1;
                $Respuesta['mensaje'] = "La tarea se eliminó correctamente.";
            }else{
                $Respuesta['estado']  = 0;
                $Respuesta['mensaje'] = "No se pudo eliminar la tarea.";
            }
        }else{
            $Respuesta['estado']  = 0;
            $Respuesta['mensaje'] = "No se pudo eliminar la tarea.";
        }
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }
?>
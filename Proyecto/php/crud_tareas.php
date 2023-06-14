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
        case 'read_idMarc':
            actionMarcarPHP($conex);
            break;
        case 'share':
            actionShare($conex);
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
        $estadoAct = $_POST['estadoAct'];

        $QueryCreate = "INSERT INTO `tareas`(`idtareas`, `nom_tarea`, `fecha`, `lugar`, `duracion`, `descripcion`) 
                        VALUES (NULL, '$nom_tarea','$fecha','$lugar','$duracion','$descripcion')";
                        
        if(mysqli_query($conex,$QueryCreate)){
            $Respuesta['id'] = mysqli_insert_id($conex);   
            $QueryLeerId = "SELECT idtareas FROM tareas WHERE (nom_tarea = '$nom_tarea' AND fecha = '$fecha' 
                            AND lugar = '$lugar' AND duracion = '$duracion' AND descripcion = '$descripcion')";

            $ResultadoLeerId = mysqli_query($conex, $QueryLeerId);

            if($ResultadoLeerId && mysqli_num_rows($ResultadoLeerId) > 0 ){
                $fila = mysqli_fetch_assoc($ResultadoLeerId);
                $idtareaRecup = $fila['idtareas'];

                $QueryPropietario = "INSERT INTO `compartir`(`tareas_idtareas`, `usuario_idUsuario`, `propietario`, `estado`, `aceptar`) 
                            VALUES ('$idtareaRecup','$idcorreo',1, '$estadoAct',1)";

                if(mysqli_query($conex,$QueryPropietario)){
                    $Respuesta['estado'] = 1;
                    $Respuesta['mensaje'] = "El registro se guardo correctamente";

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

        $fechaHoy = $_POST['fechaHoy'];

        $QueryRead =    "SELECT * FROM tareas JOIN compartir ON compartir.tareas_idtareas = tareas.idtareas
                        WHERE compartir.usuario_idUsuario = '$idcorreo'";
        $ResultadoRead = mysqli_query($conex, $QueryRead);
        $numeroRegistros = mysqli_num_rows($ResultadoRead);

        if ($numeroRegistros > 0) {
            $Respuesta['entregas'] = array();
            
            while ($RenglonEntrega = mysqli_fetch_assoc($ResultadoRead)) {
                $Entrega = array();
                $Entrega['idtareas'] = $RenglonEntrega['idtareas'];
                $Entrega['nom_tarea'] = $RenglonEntrega['nom_tarea'];
                $Entrega['descripcion'] = $RenglonEntrega['descripcion'];
                $Entrega['duracion'] = $RenglonEntrega['duracion'];
                $Entrega['fecha'] = $RenglonEntrega['fecha'];
                
                if($RenglonEntrega['fecha'] < $fechaHoy && $RenglonEntrega['estado'] != 1){     // Retrasada
                    $queryUpdateEstado =    "UPDATE compartir SET estado=2 
                                            WHERE tareas_idtareas ='".$Entrega['idtareas']."' 
                                            AND usuario_idUsuario=".$idcorreo;

                    if(mysqli_query($conex,$queryUpdateEstado)){
                        $Entrega['estado'] = 2;
                        $Respuesta['estado'] = 1;
                        $Respuesta['mensaje'] = "Los registros se listan correctamente";
                    }else{
                        $Respuesta['estado'] = 0;
                        $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                    }
                }else if($RenglonEntrega['estado'] == 1){                                       // Completada
                    $Entrega['estado'] = 1;
                    $Respuesta['estado'] = 1;
                    $Respuesta['mensaje'] = "Los registros se listan correctamente";
                }else{                                                                          // Pendiente
                    $queryUpdateEstado =    "UPDATE compartir SET estado=0 
                                            WHERE tareas_idtareas ='".$Entrega['idtareas']."' 
                                            AND usuario_idUsuario=".$idcorreo;

                    if(mysqli_query($conex,$queryUpdateEstado)){
                        $Entrega['estado'] = 0;
                        $Respuesta['estado'] = 1;
                        $Respuesta['mensaje'] = "Los registros se listan correctamente";
                    }else{
                        $Respuesta['estado'] = 0;
                        $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                    }
                }

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

        $id = $_POST['id'];
        $nom_tarea = $_POST['nom_tarea'];
        $fecha = $_POST['fecha'];
        $lugar = $_POST['lugar'];
        $duracion = $_POST['duracion'];
        $descripcion = $_POST['descripcion'];
        //$estadoAct = $_POST['estadoAct'];
        $fechaHoy = $_POST['fechaHoy'];

        $queryEstadoAct = "SELECT estado FROM compartir WHERE tareas_idtareas='".$id."' AND usuario_idUsuario=".$idcorreo;
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
                if(mysqli_affected_rows($conex)>0){   
                    if($RenglonEntregaById['estado'] == 1){
                        $Respuesta['estadoAct'] = 1;        // estadoAct = 1 = "Completada"
                        
                        $queryUpdateEstado =    "UPDATE compartir SET estado=1 
                                                WHERE tareas_idtareas ='".$id."' 
                                                AND usuario_idUsuario=".$idcorreo;

                        if(mysqli_query($conex,$queryUpdateEstado)){
                            $Respuesta['estado'] = 1;
                            $Respuesta['mensaje'] = "La tarea se actualizó correctamente";
                        }else{
                            $Respuesta['estado'] = 0;
                            $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                        }
                    }
                    elseif($fecha < $fechaHoy){
                        $Respuesta['estadoAct'] = 2;        // estadoAct = 2 = "Retrasada"

                        $queryUpdateEstado =    "UPDATE compartir SET estado=2 
                                                WHERE tareas_idtareas ='".$id."' 
                                                AND usuario_idUsuario=".$idcorreo;

                        if(mysqli_query($conex,$queryUpdateEstado)){
                            if(mysqli_affected_rows($conex)>0){   
                                $Respuesta['estado'] = 1;
                                $Respuesta['mensaje'] = "La tarea se actualizó correctamente";
                            }else{
                                $Respuesta['estado'] = 1;
                                $Respuesta['mensaje'] = "La tarea se actualizó correctamente";
                            }
                        }else{
                            $Respuesta['estado'] = 0;
                            $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                        }
                    }else{
                        $Respuesta['estadoAct'] = 0;        // estadoAct = 0 = "Pendiente"

                        $queryUpdateEstado =    "UPDATE compartir SET estado=0 
                                                WHERE tareas_idtareas ='".$id."' 
                                                AND usuario_idUsuario=".$idcorreo;

                        if(mysqli_query($conex,$queryUpdateEstado)){
                            $Respuesta['estado'] = 1;
                            $Respuesta['mensaje'] = "La tarea se actualizó correctamente";
                        }else{
                            $Respuesta['estado'] = 0;
                            $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                        }
                    }  
                }else{
                    $Respuesta['estadoAct'] = $RenglonEntregaById['estado'];;
                    $Respuesta['estado'] = 1;
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
        $queryReadById       = "SELECT * FROM tareas JOIN compartir ON compartir.tareas_idtareas = tareas.idtareas 
                                WHERE idtareas='".$id."' AND tareas_idtareas=".$id;
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

    function actionMarcarPHP($conex){
        $id = $_POST['id'];
        $estadoCompletada = $_POST['estadoCompletada'];
        $fechaHoy = $_POST['fechaHoy'];

        if (isset($_POST['correo'])) {
            $correo = $_POST['correo'];
            
            // Realizar una consulta para obtener el ID del usuario según el correo
            $queryCorreo = "SELECT idUsuario FROM usuario WHERE correo = '$correo'";
            $resultadoCorreo = mysqli_query($conex, $queryCorreo);
            
            // Verificar si se obtuvo algún resultado
            if ($resultadoCorreo && mysqli_num_rows($resultadoCorreo) > 0) {
                $fila = mysqli_fetch_assoc($resultadoCorreo);
                $idcorreo = $fila['idUsuario'];
            }
        }   

        $queryEstadoAct = "SELECT estado FROM compartir WHERE tareas_idtareas='".$id."' AND usuario_idUsuario=".$idcorreo;
        $resultEstadoAct = mysqli_query($conex,$queryEstadoAct);
        $numeroEstadoAct = mysqli_num_rows($resultEstadoAct);
        $renglonEntregaById = mysqli_fetch_assoc($resultEstadoAct);

        if($numeroEstadoAct>0){
            if($estadoCompletada == 1 && $renglonEntregaById['estado'] != 1){
                $queryEstadoCompletada = "UPDATE compartir SET estado=1 
                                            WHERE tareas_idtareas ='".$id."' 
                                            AND usuario_idUsuario=".$idcorreo;

                if(mysqli_query($conex,$queryEstadoCompletada)){
                    $queryLeerDatos = "SELECT * FROM tareas WHERE idtareas=".$id;
                    $resultLeerDatos= mysqli_query($conex,$queryLeerDatos);
                    $numeroLeerDatos = mysqli_num_rows($resultLeerDatos);
                    
                    $Respuesta['estadoAct'] = 1;
                    $Respuesta['estado'] = 1;
                    $Respuesta['mensaje'] = "Tarea completada";

                    if($numeroLeerDatos>0){                        
                        $renglonLeerDatos = mysqli_fetch_assoc($resultLeerDatos);

                        $Respuesta['idtareas'] = $renglonLeerDatos['idtareas'];
                        $Respuesta['nom_tarea'] = $renglonLeerDatos['nom_tarea'];
                        $Respuesta['fecha'] = $renglonLeerDatos['fecha'];
                        $Respuesta['duracion'] = $renglonLeerDatos['duracion'];
                    }else{
                        $Respuesta['estado'] = 0;
                        $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                    }
                }else{
                    $Respuesta['estado'] = 0;
                    $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                }
            }else if($estadoCompletada == 0 && $renglonEntregaById['estado'] == 1){
                $queryLeerDatos = "SELECT * FROM tareas WHERE idtareas=".$id;
                $resultLeerDatos= mysqli_query($conex,$queryLeerDatos);
                $numeroLeerDatos = mysqli_num_rows($resultLeerDatos);

                if($numeroLeerDatos>0){
                    $renglonLeerDatos = mysqli_fetch_assoc($resultLeerDatos);
                    $fecha = $renglonLeerDatos['fecha'];

                    if($fecha < $fechaHoy){
                        $queryEstadoCompletada = "UPDATE compartir SET estado=2 
                                                    WHERE tareas_idtareas ='".$id."' 
                                                    AND usuario_idUsuario=".$idcorreo;
                        
                        if(mysqli_query($conex,$queryEstadoCompletada)){
                            $Respuesta['estadoAct'] = 2;
                            $Respuesta['estado'] = 1;
                            $Respuesta['mensaje'] = "Tarea retrasada";

                            $Respuesta['idtareas'] = $renglonLeerDatos['idtareas'];
                            $Respuesta['nom_tarea'] = $renglonLeerDatos['nom_tarea'];
                            $Respuesta['fecha'] = $renglonLeerDatos['fecha'];
                            $Respuesta['duracion'] = $renglonLeerDatos['duracion'];
                        }else{
                            $Respuesta['estado'] = 0;
                            $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                        }
                    }else{
                        $queryEstadoCompletada = "UPDATE compartir SET estado=0 
                                                    WHERE tareas_idtareas ='".$id."' 
                                                    AND usuario_idUsuario=".$idcorreo;

                        if(mysqli_query($conex,$queryEstadoCompletada)){
                            $Respuesta['estadoAct'] = 0;
                            $Respuesta['estado'] = 1;
                            $Respuesta['mensaje'] = "Tarea pendiente";

                            $Respuesta['idtareas'] = $renglonLeerDatos['idtareas'];
                            $Respuesta['nom_tarea'] = $renglonLeerDatos['nom_tarea'];
                            $Respuesta['fecha'] = $renglonLeerDatos['fecha'];
                            $Respuesta['duracion'] = $renglonLeerDatos['duracion'];

                        }else{
                            $Respuesta['estado'] = 0;
                            $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                        }
                    }        
                }else{
                    $Respuesta['estado'] = 0;
                    $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                }            
            }else{
                $queryLeerDatos = "SELECT * FROM tareas WHERE idtareas=".$id;
                $resultLeerDatos= mysqli_query($conex,$queryLeerDatos);
                $numeroLeerDatos = mysqli_num_rows($resultLeerDatos);

                if($numeroLeerDatos>0){
                    $Respuesta['estadoAct'] = $renglonEntregaById['estado'];
                    $Respuesta['estado'] = 1;
                    $Respuesta['mensaje'] = "Sin cambios";
                    $renglonLeerDatos = mysqli_fetch_assoc($resultLeerDatos);

                    $Respuesta['idtareas'] = $renglonLeerDatos['idtareas'];
                    $Respuesta['nom_tarea'] = $renglonLeerDatos['nom_tarea'];
                    $Respuesta['fecha'] = $renglonLeerDatos['fecha'];
                    $Respuesta['duracion'] = $renglonLeerDatos['duracion'];
                }else{
                    $Respuesta['estado'] = 0;
                    $Respuesta['mensaje'] = "Ocurrio un error desconocido";
                }
            }
        }else{
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "Ocurrio un error desconocido";
        }
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    function actionShare($conex){
        $usuario = $_POST['nombre'];
        $idtarea = $_POST['id'];
        $email = $_POST['correo'];

        $consultanombre = "SELECT nom_usuario FROM usuario WHERE correo = '$email'";
        $resultadonombre = mysqli_query($conex,$consultanombre);
        $fila = mysqli_fetch_assoc($resultadonombre);
        $nombreEncontrado = $fila['nom_usuario'];
        if($nombreEncontrado == $usuario)
        {
            $Respuesta['estado']  = 2;
        }
        else
        {
            $consulta = "SELECT * FROM usuario WHERE nom_usuario = '$usuario'";
            $resultado = mysqli_query($conex,$consulta);
            $rconsulta = mysqli_num_rows($resultado);
            if($rconsulta > 0){
                // Consulta para obtener el id de la persona a compartir
                $consultaid = "SELECT idUsuario FROM usuario WHERE nom_usuario = '$usuario'";
                $resultadoid = mysqli_query($conex,$consultaid);
                $fila = mysqli_fetch_assoc($resultadoid);
                $idEncontrado = $fila['idUsuario'];

                //Consulta para saber el id del propietario
                $consultaid2 = "SELECT idUsuario FROM usuario WHERE correo = '$email'";
                $resultadoid2 = mysqli_query($conex,$consultaid2);
                $fila = mysqli_fetch_assoc($resultadoid2);
                $idPropietario = $fila['idUsuario'];

                //Consulta para saber el estado actual de la tarea
                $consultaestado = "SELECT estado FROM compartir WHERE propietario = 1 AND usuario_idUsuario = '$idPropietario' AND tareas_idtareas = '$idtarea'";
                $resultadoestado = mysqli_query($conex,$consultaestado);
                $fila = mysqli_fetch_assoc($resultadoestado);
                $estado = $fila['estado'];
                
                //Consulta para insertar los resultados
                $queryShare = "INSERT INTO `compartir`(`propietario`, `usuario_idUsuario`, `tareas_idtareas`, `estado`, `aceptar`) 
                                VALUES (0,'$idEncontrado','$idtarea','$estado',0)";
                if(mysqli_query($conex,$queryShare))
                {
                    $Respuesta['estado']  = 1;
                }
                else
                {
                    $Respuesta['estado']  = 3;
                }
            }
            else{
                $Respuesta['estado']  = 0;
            }
        }
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }
?>
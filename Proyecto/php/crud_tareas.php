<?php
    /*
    Nombre del programa: PHP para tareas
    Descripción: Son todas las funciones PHP relacionadas con las tareas
    Funciones: 
        actionCreatePHP()
        actionReadPHP()
        actionUpdatePHP()
        actionReadByIdPHP()
        actionDeletePHP()
        actionMarcarPHP()
        actionShare()
    */
    
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

    /* 
    - CREAR TAREA -
    La función actionCreatePHP() crea en la BD los datos de una tarea nueva y crea la relación con el usuario, los datos 
    los debe recibir desde crud_tareas.js y envía una Respuesta con lo que necesita el Javascript, y mensajes en caso de error.
    */
    function actionCreatePHP($conex){
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

        // Recupera los datos que el usuario ingresó
        $nom_tarea = $_POST['nom_tarea'];
        $fecha = $_POST['fecha'];  
        $lugar = $_POST['lugar'];
        $duracion = $_POST['duracion'];
        $descripcion = $_POST['descripcion'];
        $estadoAct = $_POST['estadoAct'];

        // Crea el nuevo registro de tarea en la BD
        $queryCreate = "INSERT INTO `tareas`(`idtareas`, `nom_tarea`, `fecha`, `lugar`, `duracion`, `descripcion`) 
                        VALUES (NULL, '$nom_tarea','$fecha','$lugar','$duracion','$descripcion')";
        
        // Si logra crear el registro entra a crear la relación entre el usuario y la tarea, e identifica al usuario como propietario
        // Si alguna de las operaciones falla, entra a los else y manda mensajes de error
        if(mysqli_query($conex,$queryCreate)){
            $Respuesta['id'] = mysqli_insert_id($conex);   
            $queryLeerId = "SELECT idtareas FROM tareas WHERE (nom_tarea = '$nom_tarea' AND fecha = '$fecha' 
                            AND lugar = '$lugar' AND duracion = '$duracion' AND descripcion = '$descripcion')";

            $resultadoLeerId = mysqli_query($conex, $queryLeerId);

            if($resultadoLeerId && mysqli_num_rows($resultadoLeerId) > 0 ){
                $fila = mysqli_fetch_assoc($resultadoLeerId);
                $idtareaRecup = $fila['idtareas'];

                $queryPropietario = "INSERT INTO `compartir`(`tareas_idtareas`, `usuario_idUsuario`, `propietario`, `estado`, `aceptar`) 
                            VALUES ('$idtareaRecup','$idcorreo',1, '$estadoAct',1)";

                if(mysqli_query($conex,$queryPropietario)){
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

            // Envía la respuesta para poder utilizarla en el javascript
            echo json_encode($Respuesta);
            mysqli_close($conex);   
        }
    }

    /* 
    - LEER TAREAS -
    La función actionReadPHP() recupera todos los registros de tareas que existen en la BD relacionados con
    el usuario y comprueba el estado de la tarea, para ponerla como Pendiente, Completada o Retrasada.
    */
    function actionReadPHP($conex) {
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

        $fechaHoy = $_POST['fechaHoy'];

        // Recopila todos los registros de tareas que están relacionados con la sesión del usuario
        $queryRead =    "SELECT * FROM tareas JOIN compartir ON compartir.tareas_idtareas = tareas.idtareas
                        WHERE compartir.usuario_idUsuario = '$idcorreo'";
        $resultadoRead = mysqli_query($conex, $queryRead);
        $numeroRegistros = mysqli_num_rows($resultadoRead);

        // Si hay registros los envía al Javascript y comprueba el estado de la tarea (Pendiente, Completada, Retrasada)
        // Si no hay registros envía un mensaje diciendo que no hay registros para mostrar
        // Si ocurre un error dentro del if, envía mensajes de error
        if ($numeroRegistros > 0) {
            $Respuesta['entregas'] = array();
            
            while ($renglonEntrega = mysqli_fetch_assoc($resultadoRead)) {
                $Entrega = array();
                $Entrega['idtareas'] = $renglonEntrega['idtareas'];
                $Entrega['nom_tarea'] = $renglonEntrega['nom_tarea'];
                $Entrega['descripcion'] = $renglonEntrega['descripcion'];
                $Entrega['duracion'] = $renglonEntrega['duracion'];
                $Entrega['fecha'] = $renglonEntrega['fecha'];
                $Entrega['aceptar'] = $renglonEntrega['aceptar'];
                $Entrega['propietario'] = $renglonEntrega['propietario'];
                
                if($renglonEntrega['fecha'] < $fechaHoy && $renglonEntrega['estado'] != 1){     // Retrasada
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
                }else if($renglonEntrega['estado'] == 1){                                       // Completada
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
        
        // Envía la respuesta para poder utilizarla en el javascript
        echo json_encode($Respuesta);
        mysqli_close($conex); 
    }
    
    /* 
    - EDITAR TAREA -
    La función actionUpdatePHP() actualiza una tarea específica de la BD la cual debe estar relacionada con
    el usuario y comprueba el estado de la tarea, para ponerla como Pendiente, Completada o Retrasada.
    */
    function actionUpdatePHP($conex){
        if (isset($_POST['correo'])) {
            $correo = $_POST['correo'];
            
            // Realizar una consulta para obtener el ID del usuario según el correo
            $queryCorreo = "SELECT idUsuario FROM usuario WHERE correo = '$correo'";
            $resultadoCorreo = mysqli_query($conex, $queryCorreo);
            
            // Verificar si se obtuvo algún resultado de la consulta $queryCorreo, a traves de resultadoCorreo
            if ($resultadoCorreo && mysqli_num_rows($resultadoCorreo) > 0) {
                $fila = mysqli_fetch_assoc($resultadoCorreo);
                $idcorreo = $fila['idUsuario'];
            }
        }   

        // Recupera los datos que el usuario ingresó
        $id = $_POST['id'];
        $nom_tarea = $_POST['nom_tarea'];
        $fecha = $_POST['fecha'];
        $lugar = $_POST['lugar'];
        $duracion = $_POST['duracion'];
        $descripcion = $_POST['descripcion'];
        $fechaHoy = $_POST['fechaHoy'];

        // Comprueba el estado de la tarea leyéndolo de la BD
        $queryEstadoAct = "SELECT estado FROM compartir WHERE tareas_idtareas='".$id."' AND usuario_idUsuario=".$idcorreo;
        $resultEstadoAct = mysqli_query($conex,$queryEstadoAct);
        $numeroEstadoAct = mysqli_num_rows($resultEstadoAct);

        //  Manda a actualizar la tarea de la BD
        $queryUpdate   = "UPDATE tareas SET
                         nom_tarea='".$nom_tarea."', 
                         lugar='".$lugar."',
                         fecha='".$fecha."',
                         duracion='".$duracion."',
                         descripcion='".$descripcion."'
                         WHERE idtareas=".$id;

        if(mysqli_query($conex,$queryUpdate)){
            if($numeroEstadoAct>0){
                $renglonEntregaById = mysqli_fetch_assoc($resultEstadoAct);

                // Si se actualizó el registro comprueba el estado de la tarea y también lo actualiza, si ocurre un error envía un mensaje
                // Si no se modificó nada en la BD, envía un mensaje de que no se realizaron cambios
                if(mysqli_affected_rows($conex)>0){   
                    if($renglonEntregaById['estado'] == 1){
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
                    $Respuesta['estadoAct'] = $renglonEntregaById['estado'];;
                    $Respuesta['estado'] = 1;
                    $Respuesta['mensaje'] = "No se realizaron cambios";
                }
            }
        }else{
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "Ocurrio un error desconocido";
        } 

        // Envía la respuesta para poder utilizarla en el javascript
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    /* 
    - LEER DATOS DE UNA TAREA ESPECÍFICA -
    La función actionReadByIdPHP() recupera los datos de una tarea desde la BD relacionada con
    el usuario y comprueba el estado de la tarea, para ponerla como Pendiente, Completada o Retrasada.
    */
    function actionReadByIdPHP($conex){
        $id = $_POST['id'];

        // Lee los datos del registro según el id de la tarea
        $queryReadById = "SELECT * FROM tareas JOIN compartir ON compartir.tareas_idtareas = tareas.idtareas 
                                WHERE idtareas='".$id."' AND tareas_idtareas=".$id;

        $resultById = mysqli_query($conex,$queryReadById);
        $numeroRegistrosById = mysqli_num_rows($resultById);

        // Si encuentra el registro, guarda los datos en $Respuesta
        // Sino envía un mensaje de error
        if($numeroRegistrosById>0){
            $Respuesta['estado']  = 1;
            $Respuesta['mensaje'] = "Registro encontrado";
             
            $renglonEntregaById = mysqli_fetch_assoc($resultById);

            $Respuesta['idtareas'] = $renglonEntregaById['idtareas'];
            $Respuesta['nom_tarea'] = $renglonEntregaById['nom_tarea'];
            $Respuesta['fecha'] = $renglonEntregaById['fecha'];
            $Respuesta['lugar'] = $renglonEntregaById['lugar'];
            $Respuesta['duracion'] = $renglonEntregaById['duracion'];
            $Respuesta['descripcion'] = $renglonEntregaById['descripcion'];
            $Respuesta['estadoAct'] = $renglonEntregaById['estado'];
        }else{
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "No se encuentra el registro";
        }

        // Envía la respuesta para poder utilizarla en el javascript
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    /* 
    - ELIMINAR TAREA -
    La función actionDeletePHP() elimina una tarea específica de la BD la cual debe estar relacionada con
    el usuario y comprueba el estado de la tarea, para ponerla como Pendiente, Completada o Retrasada.
    */
    function actionDeletePHP($conex){
        $id = $_POST['id'];

        // Primero elimina la relación entre el usuario y la tarea a eliminar
        $queryEliminarRelacion = "DELETE FROM compartir WHERE tareas_idtareas=".$id;
        mysqli_query($conex,$queryEliminarRelacion);

        // Si se eliminó correctamente entra en el if
        // Sino envía el mensaje de error
        if(mysqli_affected_rows($conex)>0){
            // Elimina de la BD la tarea
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

        // Envía la respuesta para poder utilizarla en el javascript
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    /* 
    - MARCAR TAREA -
    La función actionMarcarPHP() sirve para marcar o desmarcar la tarea como Completada, donde si no está Completada, 
    compara la fecha de la BD, con la fecha del día actual para poner el estado de la tarea como Pendiente o Retrasada.
    */
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

        // Revisa el estado que tiene guardada la tarea del usuario en la BD
        $queryEstadoAct = "SELECT estado FROM compartir WHERE tareas_idtareas='".$id."' AND usuario_idUsuario=".$idcorreo;
        $resultEstadoAct = mysqli_query($conex,$queryEstadoAct);
        $numeroEstadoAct = mysqli_num_rows($resultEstadoAct);
        $renglonEntregaById = mysqli_fetch_assoc($resultEstadoAct);

        // Si la consulta a la BD se hizo correctamente, según el estado que tenga y la acción que se hace actualiza el estado
        // y lee los datos de la tarea para poder recuperarlos después al Javascript.
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

        // Envía la respuesta para poder utilizarla en el javascript
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    /* 
    - COMPARTIR TAREA -
    La función actionShare() sirve para compartir una tarea con otro usuario, leyendo el nom_usuario de a quien se va a compartir
    y crea la relación entre usuario y tarea, pero no lo pone como propietario
    */
    function actionShare($conex){
        $usuario = $_POST['nombre'];
        $idtarea = $_POST['id'];
        $email = $_POST['correo'];

        $consultanombre = "SELECT nom_usuario FROM usuario WHERE correo = '$email'";
        $resultadonombre = mysqli_query($conex,$consultanombre);
        $fila = mysqli_fetch_assoc($resultadonombre);
        $nombreEncontrado = $fila['nom_usuario'];

        if($nombreEncontrado == $usuario){
            $Respuesta['estado']  = 2;
            
        }else{
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
                $consultaid2 = "SELECT usuario_idUsuario FROM compartir WHERE propietario = 1 AND tareas_idtareas = '$idtarea'";
                $resultadoid2 = mysqli_query($conex,$consultaid2);
                $fila = mysqli_fetch_assoc($resultadoid2);
                $idPropietario = $fila['usuario_idUsuario'];

                //Consulta para saber el estado actual de la tarea
                $consultaestado = "SELECT estado FROM compartir WHERE propietario = 1 AND usuario_idUsuario = '$idPropietario' AND tareas_idtareas = '$idtarea'";
                $resultadoestado = mysqli_query($conex,$consultaestado);
                $fila = mysqli_fetch_assoc($resultadoestado);
                $estado = $fila['estado'];
                
                //Consulta para insertar los resultados
                $queryShare = "INSERT INTO `compartir`(`propietario`, `usuario_idUsuario`, `tareas_idtareas`, `estado`, `aceptar`) 
                                VALUES (0,'$idEncontrado','$idtarea','$estado',0)";
                if(mysqli_query($conex,$queryShare)){
                    $Respuesta['estado']  = 1;
                }
                else{
                    $Respuesta['estado']  = 3;
                }
            }
            else{
                $Respuesta['estado']  = 0;
            }
        }

        // Envía la respuesta para poder utilizarla en el javascript
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }
?>
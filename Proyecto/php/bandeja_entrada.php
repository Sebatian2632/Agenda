<?php
 /* 
Nombre del programa: bandeja_entrada.php
Descripción: Este documento permite recuperar las tareas que son compartidas entre los usuarios
Funciones: ninguna
*/ 

    //Conexión a la base de datos
    include 'connect.php';
    $Respuesta = array();
    $accion = $_POST['accion'];

    switch ($accion) {
        case 'create':
            actionCreatePHP($conex);
            break;
        case 'read':
            actionReadPHP($conex);
            break;
        case 'read_id':
            actionReadByIdPHP($conex);
            break;
        case 'aceptar':
            actionAccept($conex);
            break;
        default:
            # code...
            break;
    }

    function actionReadPHP($conex){
        $email = $_POST['correo'];
        //Consulta para saber el id del usuario
        $consultaid = "SELECT idUsuario FROM usuario WHERE correo = '$email'";
        $resultadoid = mysqli_query($conex,$consultaid);
        $fila = mysqli_fetch_assoc($resultadoid);
        $idEncontrado = $fila['idUsuario'];
        //Consulta para saber las tareas que tiene
        $consultatarea = "SELECT * FROM compartir WHERE propietario = 0 AND aceptar = 0 AND usuario_idUsuario = '$idEncontrado'";
        $resultadotarea = mysqli_query($conex,$consultatarea);
        $numerotareas = mysqli_num_rows($resultadotarea);
        if ($numerotareas > 0) {
            $Respuesta['compartir'] = array();
        
            while ($RenglonCompartir = mysqli_fetch_assoc($resultadotarea)) {
                $Compartir = array();
                //$Compartir['propietario'] = $RenglonCompartir['propietario'];
                //$Compartir['usuario_idUsuario'] = $RenglonCompartir['usuario_idUsuario'];
                $Compartir['tareas_idtareas'] = $RenglonCompartir['tareas_idtareas'];
                //$Compartir['estado'] = $RenglonCompartir['estado'];
                //$Compartir['aceptar'] = $RenglonCompartir['aceptar'];
            
                // Obtener el usuario_idUsuario con propietario = 1 y tareas_idtareas correspondiente
                $tareas_id = $RenglonCompartir['tareas_idtareas'];
                $consultaUsuarioId = "SELECT usuario_idUsuario FROM compartir WHERE propietario = 1 AND tareas_idtareas = '$tareas_id'";
                $resultadoUsuarioId = mysqli_query($conex, $consultaUsuarioId);
                $RenglonUsuarioId = mysqli_fetch_assoc($resultadoUsuarioId);
                $usuarioId = $RenglonUsuarioId['usuario_idUsuario'];
            
                // Obtener el nom_usuario correspondiente al usuario_idUsuario_propietario
                $consultaNomUsuario = "SELECT nom_usuario FROM usuario WHERE idUsuario = '$usuarioId'";
                $resultadoNomUsuario = mysqli_query($conex, $consultaNomUsuario);
                $RenglonNomUsuario = mysqli_fetch_assoc($resultadoNomUsuario);
                $nomUsuario = $RenglonNomUsuario['nom_usuario'];
            
                // Agregar nom_usuario al array $Compartir
                $Compartir['nom_usuario_propietario'] = $nomUsuario;
            
                $Respuesta['estado'] = 1;
                array_push($Respuesta['compartir'], $Compartir);
            }                
        } else {
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "Lo siento, pero no hay registros para mostrar";
        }
        echo json_encode($Respuesta);
        mysqli_close($conex); 
    }

    function actionReadByIdPHP($conex){
        $idTarea = $_POST['id'];

        $consultainfo = "SELECT * FROM tareas WHERE idtareas = '$idTarea'";
        $rinfo = mysqli_query($conex,$consultainfo);

        $numeroRegistrosById = mysqli_num_rows($rinfo);

        if($numeroRegistrosById>0){
            $Respuesta['estado']  = 1;
            $Respuesta['mensaje'] = "Registro encontrado";
            
            $RenglonEntregaById = mysqli_fetch_assoc($rinfo);

            $Respuesta['idtareas'] = $RenglonEntregaById['idtareas'];
            $Respuesta['nom_tarea'] = $RenglonEntregaById['nom_tarea'];
            $Respuesta['fecha'] = $RenglonEntregaById['fecha'];
            $Respuesta['lugar'] = $RenglonEntregaById['lugar'];
            $Respuesta['duracion'] = $RenglonEntregaById['duracion'];
            $Respuesta['descripcion'] = $RenglonEntregaById['descripcion'];
            //$Respuesta['estadoAct'] = $RenglonEntregaById['estado'];
        }else{
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "No se encuentra el registro";
        }
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    function actionAccept($conex){
        $idtarea = $_POST['id'];
        $email = $_POST['correo'];

        //Consulta para saber el id del propietario
        $consultaid = "SELECT idUsuario FROM usuario WHERE correo = '$email'";
        $resultadoid = mysqli_query($conex,$consultaid);
        $fila = mysqli_fetch_assoc($resultadoid);
        $idPropietario = $fila['idUsuario'];

        $consultaact = "UPDATE compartir SET aceptar=1 WHERE usuario_idUsuario='$idPropietario' AND tareas_idtareas='$idtarea'";
        if(mysqli_query($conex,$consultaact))
        {
            $Respuesta['estado'] = 1;
        }
        else
        {
            $Respuesta['estado'] = 0;
        }
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }
?>
<?php
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
           
            break;
    }
    function actionCreatePHP($conex)
    {
        $data = json_decode(file_get_contents('php://input'), true);        

        $nombre = $data['nombre'];
        $descripcion = $data['descripcion'];
        $prioridad = $data['prioridad'];
        $lunes = $data['lunes'];
        $martes = $data['martes'];
        $miercoles = $data['miercoles'];
        $jueves = $data['jueves'];
        $viernes = $data['viernes'];
        $sabado = $data['sabado'];
        $domingo = $data['domingo'];
        $idUsuario = isset($data['idUsuario']) ? $data['idUsuario'] : '';

   
        $consultainsert = "INSERT INTO `habitos`
        (`nom_habito`,`descripcion`,`prioridad`,`usuario_idUsuario`,`lunes`,`martes`,`miercoles`,`jueves`,`viernes`,`sabado`,`domingo`)
         VALUES ('$nombre','$descripcion','$prioridad','$idUsuario','$lunes','$martes','$miercoles','$jueves','$viernes','$sabado','$domingo')";
        $resultadoinsert = mysqli_query($conex,$consultainsert);
        //
        //
    }
?>
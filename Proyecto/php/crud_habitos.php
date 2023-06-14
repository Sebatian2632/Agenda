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
        
        $nom_habito = $_POST['nom_habito'];
        $descripcion = $_POST['descripcion'];  
        $prioridad = $_POST['prioridad'];
        $lunes = $_POST['lunes'];
        $martes = $_POST['martes'];
        $miercoles = $_POST['miercoles'];
        $jueves = $_POST['jueves'];
        $viernes = $_POST['viernes'];
        $sabado = $_POST['sabado'];
        $domingo = $_POST['domingo'];

        $QueryCreate = "INSERT INTO `habitos`(`nom_habito`, `descripcion`, `prioridad`,`lunes`,`martes`,`miercoles`,`jueves`,`viernes`,`sabado`,`domingo`,usuario_idUsuario) 
                        VALUES ( '$nom_habito','$descripcion','$prioridad','$lunes','$martes','$miercoles','$jueves','$viernes','$sabado','$domingo','$idcorreo')";
        

         if(mysqli_query($conex,$QueryCreate)){
            $Respuesta['id'] = mysqli_insert_id($conex);   

                    $Respuesta['estado'] = 1;
                    $Respuesta['mensaje'] = "El registro se guardo correctamente";

                    echo json_encode($Respuesta);
                    mysqli_close($conex);   
                

            }else{
                $Respuesta['estado'] = 0;
                $Respuesta['mensaje'] = "Ocurrio un error desconocido 2";
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

        $QueryRead =    "SELECT * FROM habitos 
                        WHERE usuario_idUsuario = '$idcorreo'";
        $ResultadoRead = mysqli_query($conex, $QueryRead);
        $numeroRegistros = mysqli_num_rows($ResultadoRead);

        if ($numeroRegistros > 0) {
            $Respuesta['entregas'] = array();
            
            while ($RenglonEntrega = mysqli_fetch_assoc($ResultadoRead)) {
                $Entrega = array();
                $Entrega['idhabitos'] = $RenglonEntrega['idhabitos'];
                $Entrega['nom_habito'] = $RenglonEntrega['nom_habito'];
                $Entrega['descripcion'] = $RenglonEntrega['descripcion'];
                $Entrega['prioridad'] = $RenglonEntrega['prioridad'];

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
        $nom_habito = $_POST['nom_habito'];
        $descripcion = $_POST['descripcion'];
        $prioridad = $_POST['prioridad'];
        $lunes = $_POST['lunes'];
        $martes = $_POST['martes'];
        $miercoles = $_POST['miercoles'];
        $jueves = $_POST['jueves'];
        $viernes = $_POST['viernes'];
        $sabado = $_POST['sabado'];
        $domingo = $_POST['domingo'];

        $queryUpdate = "UPDATE habitos SET
                 nom_habito='".$nom_habito."', 
                 descripcion='".$descripcion."', 
                 prioridad='".$prioridad."', 
                 lunes='".$lunes."', 
                 martes='".$martes."', 
                 miercoles='".$miercoles."', 
                 jueves='".$jueves."', 
                 viernes='".$viernes."', 
                 sabado='".$sabado."', 
                 domingo='".$domingo."' 
                 WHERE idhabitos=".$id;   

        if(mysqli_query($conex,$queryUpdate)){
            if(mysqli_affected_rows($conex)>0){   
                $Respuesta['estado'] = 1;
                $Respuesta['mensaje'] = "El hábito se actualizó correctamente";
            }else{
                $Respuesta['estado'] = 0;
                $Respuesta['mensaje'] = "No se realizaron cambios";
            }
        }else{
            $Respuesta['estado'] = 0;
            $Respuesta['mensaje'] = "Ocurrio un error desconocido";
        } 
        echo json_encode($Respuesta);
        mysqli_close($conex);
        
        
        
        
        
        /*
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
        $nom_habito = $_POST['nom_habito'];
        $descripcion = $_POST['descripcion'];
        $prioridad = $_POST['prioridad'];
        $lunes = $_POST['lunes'];
        $martes = $_POST['martes'];
        $miercoles = $_POST['miercoles'];
        $jueves = $_POST['jueves'];
        $viernes = $_POST['viernes'];
        $sabado = $_POST['sabado'];
        $domingo = $_POST['domingo'];

        $queryUpdate   = "UPDATE habitos SET
                         nom_habito='".$nom_habito."', 
                         descripcion='".$descripcion."'
                         prioridad='".$prioridad."',
                         lunes='".$lunes."',
                         martes='".$martes."',
                         miercoles='".$miercoles."',
                         jueves='".$jueves."',
                         viernes='".$viernes."',
                         sabado='".$sabado."',
                         domingo='".$domingo."',
                         WHERE idhabitos=".$id;

        
        echo json_encode($Respuesta);
        mysqli_close($conex);*/
    }


    function actionReadByIdPHP($conex){
        $id                  = $_POST['id'];
        $queryReadById       = "SELECT * FROM habitos  
                                WHERE idhabitos='".$id."' ";
        $resultById          = mysqli_query($conex,$queryReadById);
        $numeroRegistrosById = mysqli_num_rows($resultById);

        if($numeroRegistrosById>0){
           
            $Respuesta['mensaje'] = "Registro encontrado";
             
            $RenglonEntregaById = mysqli_fetch_assoc($resultById);

            $Respuesta['idhabitos'] = $RenglonEntregaById['idhabitos'];
            $Respuesta['nom_habito'] = $RenglonEntregaById['nom_habito'];
            $Respuesta['descripcion'] = $RenglonEntregaById['descripcion'];
            $Respuesta['prioridad'] = $RenglonEntregaById['prioridad'];
            $Respuesta['lunes'] = $RenglonEntregaById['lunes'];
            $Respuesta['martes'] = $RenglonEntregaById['martes'];
            $Respuesta['miercoles'] = $RenglonEntregaById['miercoles'];
            $Respuesta['jueves'] = $RenglonEntregaById['jueves'];
            $Respuesta['viernes'] = $RenglonEntregaById['viernes'];
            $Respuesta['sabado'] = $RenglonEntregaById['sabado'];
            $Respuesta['domingo'] = $RenglonEntregaById['domingo'];
        }else{
            
            $Respuesta['mensaje'] = "No se encuentra el registro";
        }
        echo json_encode($Respuesta);
        mysqli_close($conex);
    }

    function actionDeletePHP($conex){
        $id = $_POST['id'];



            $queryEliminar = "DELETE FROM habitos WHERE idhabitos=".$id;
            mysqli_query($conex,$queryEliminar);

            if(mysqli_affected_rows($conex)>0){
                $Respuesta['estado']  = 1;
                $Respuesta['mensaje'] = "La tarea se eliminó correctamente.";
            }else{
                $Respuesta['estado']  = 0;
                $Respuesta['mensaje'] = "No se pudo eliminar la tarea.";
            }

        echo json_encode($Respuesta);
        mysqli_close($conex);

    }
        
    
?>
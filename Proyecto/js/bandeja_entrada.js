
// -----------------  READ COMPARTIR  ------------------
// Funciona al cargar la página 
async function actionRead(){
    const email = await obtenerCorreo();
    //console.log(email);
    $.ajax({
        method: "POST",
        url: "../php/bandeja_entrada.php",
        data: {
          correo: email,
          accion: "read"
        },
        success: function(respuesta) {
          JSONRespuesta = JSON.parse(respuesta);
          if(JSONRespuesta.estado==1){
            tabla = $("#example2").DataTable();
            JSONRespuesta.compartir.forEach(tareas => {
              let simbolo = '<i class="fas fa-envelope" style="color: #af66eb;"></i>';
              let tareasId = tareas.tareas_idtareas;
              let propietario = tareas.nom_usuario_propietario;
              let mensaje = 'Ha compartido una tarea contigo';
              let acciones = '<button class="btn btn-primary" data-toggle="modal" data-target="#myModal" onclick="verCorreo(' + tareasId + ')">Ver correo</button>';
              tabla.row.add([simbolo, propietario, mensaje, acciones]).draw().node().id="renglon_"+tareas.tareas_idtareas;
              });
            
          }
        }
      }); 
}

function verCorreo(tareasId) {
  idLeer = tareasId;
  $.ajax({
    method:"POST",
    url: "../php/bandeja_entrada.php",
    data: {
      id: idLeer,
      accion:"read_id"
    },
    success: function( respuesta ) {
      JSONRespuesta = JSON.parse(respuesta);
      console.log(respuesta);
      if(JSONRespuesta.estado==1){
        let nom_tarea = document.getElementById("titulo");
        nom_tarea.value=JSONRespuesta.nom_tarea;
        let descripcion = document.getElementById("descripcion");
        descripcion.value=JSONRespuesta.descripcion;
        let lugar = document.getElementById("lugar");
        lugar.value=JSONRespuesta.lugar;
        let fecha = document.getElementById("fecha");
        fecha.value=JSONRespuesta.fecha;
        let duracion = document.getElementById("duracion");
        duracion.value=JSONRespuesta.duracion;

      }else{
        toastr.error("Registro no encontrado");
      }
    }
  });
}

//Leemos el correo de la sesion
async function obtenerCorreo() {
    const response = await fetch("../php/session.php");
    const data = await response.json();
    const user = data.correo;
    return user;
  }
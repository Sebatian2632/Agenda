let idEliminar=0;
let idActualizar=0;
let idLeer=0;

// ----------------  CREATE TAREAS  -----------------
// Funciona al oprimir el botón de Nueva Tarea
async function actionCreate()
{
    //Recuperamos los datos del formulario
    let nom_tarea = document.getElementById('nombreTarea').value;
    let descripcion = document.getElementById('descripcion').value;
    let lugar = document.getElementById('lugar').value;
    let fecha = document.getElementById('fecha').value;
    let duracion = document.getElementById('duracion').value;
    let estado = 0;

    const email = await obtenerCorreo();

    // Validaciones not null, para asegurar que llene todos los campos
    if(nom_tarea === "" || descripcion === "" || lugar === "" || fecha === "" || duracion === ""){
        console.log('No puso todos los campos');
        //alert("Favor de llenar todos los campos");
        toastr.error("Favor de rellenar todos los campos. Intente de nuevo.");
    }else{
        var formData = new FormData();
        formData.append('nom_tarea', nom_tarea);
        formData.append('fecha', fecha);
        formData.append('lugar', lugar);
        formData.append('duracion', duracion);
        formData.append('descripcion', descripcion);
        formData.append('estado', estado);
        formData.append('accion', "create");
        formData.append('correo', email);

        console.log(nom_tarea);
        console.log(descripcion);
        console.log(lugar);
        console.log(fecha);
        console.log(duracion);
        console.log(email);
        limpiarpagina();

        $.ajax({ 
          method:"POST",
          url: "../php/crud_tareas.php",
          data: formData,
          contentType: false,
          processData: false,
          success: function(respuesta) {
            //alert(respuesta);
            JSONRespuesta = JSON.parse(respuesta); 
            if(JSONRespuesta.estado==1){
              //alert(JSONRespuesta.mensaje);
              tabla = $("#example2").DataTable();
              let estadoAct;
              if(estado == 0){
                estadoAct = "Pendiente";
              }
              if(estado == 1){
                estadoAct = "Completada";
              }
              if(estado == 2){
                estadoAct = "Retrasada";
              }
              let Botones="";
                Botones = '<i class="fas fa-eye" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+JSONRespuesta.id+')"></i>';
                Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar('+JSONRespuesta.id+')"></i>';    
                Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar('+JSONRespuesta.id+')"></i>';
                Botones += '<i class="fas fa-share" style="font-size:25px;color: #1855b1; margin-right: 10px;" data-toggle="modal" data-target="#modal_share_tarea"></i>';
              tabla.row.add([nom_tarea, fecha, duracion, estadoAct, Botones]).draw().node().id="renglon_"+JSONRespuesta.id;
              //toastr.success(JSONRespuesta.mensaje);
            }else{
              toastr.error(JSONRespuesta.mensaje);
            }
          }
      });
    }    
}

// -----------------  READ TAREAS  ------------------
// Pone en la tabla todos los registros de la BD
async function actionRead() {
  const email = await obtenerCorreo();

  $.ajax({
    method:"POST",
    url: "../php/crud_tareas.php",
    data: {
      accion: "read",
      correo: email
    },
    success: function( respuesta ) {
      JSONRespuesta = JSON.parse(respuesta);
      if(JSONRespuesta.estado==1){
        //alert(JSONRespuesta.mensaje);
        tabla = $("#example2").DataTable();
            JSONRespuesta.entregas.forEach(tareas => {
              let estadoAct;
              if(tareas.estado == 0){
                estadoAct = "Pendiente";
              }
              if(tareas.estado == 1){
                estadoAct = "Completada";
              }
              if(tareas.estado == 2){
                estadoAct = "Retrasada";
              }
              let Botones="";
                Botones = '<i class="fas fa-eye" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+tareas.idtareas+')"></i>';
                Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar('+tareas.idtareas+')"></i>';    
                Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar('+tareas.idtareas+')"></i>';
                Botones += '<i class="fas fa-share" style="font-size:25px;color: #1855b1; margin-right: 10px;" data-toggle="modal" data-target="#modal_share_tarea"></i>';
              tabla.row.add([tareas.nom_tarea, tareas.fecha, tareas.duracion, estadoAct, Botones]).draw().node().id="renglon_"+tareas.idtareas;
            });
      } 
      console.log(respuesta);
    }
  });
}

// -----------------  READ_BY_ID TAREAS  ------------------
// Funciona al oprimir el botón de morado de leer para cada tarea, o cuando se selecciona desde el calendario
function actionReadById(id){
  idLeer=id;
  //alert(idActualizar);

  $.ajax({
    method:"POST",
    url: "../php/crud_tareas.php",
    data: {
      id: idLeer,
      accion:"read_id"
    },
    success: function( respuesta ) {
      JSONRespuesta = JSON.parse(respuesta);
      if(JSONRespuesta.estado==1){
        let nom_tarea = document.getElementById("nombreTareaRead");
        nom_tarea.value=JSONRespuesta.nom_tarea;
        let descripcion = document.getElementById("descripcionRead");
        descripcion.value=JSONRespuesta.descripcion;
        let lugar = document.getElementById("lugarRead");
        lugar.value=JSONRespuesta.lugar;
        let fecha = document.getElementById("fechaRead");
        fecha.value=JSONRespuesta.fecha;
        let duracion = document.getElementById("duracionRead");
        duracion.value=JSONRespuesta.duracion;
        //alert("FUNCIONA HASTA AQUI");
        
      }else{
        alert("Registro no encontrado");
        //toastr.error("Registro no encontrado");
      }
    }
  });
}

// -----------------  UPDATE TAREAS  ------------------
// Funciona al oprimir el botón verde de editar para cada tarea
function actionUpdate(){
    let nom_tarea = document.getElementById("nombreTarea_Update").value;
    let fecha = document.getElementById("fecha_Update").value;
    let lugar = document.getElementById("lugar_Update").value;
    let duracion = document.getElementById("duracion_Update").value;
    let descripcion = document.getElementById("descripcion_Update").value;
  
    var formData = new FormData();
        formData.append('id', idActualizar);
        formData.append('nom_tarea', nom_tarea);
        formData.append('fecha', fecha);
        formData.append('lugar', lugar);
        formData.append('duracion', duracion);
        formData.append('descripcion', descripcion);
        formData.append('accion', "update");
  
    $.ajax({
      method:"POST",
      url: "../php/crud_tareas.php",
      data: formData,
      contentType: false,
      processData: false,
      
      success: function( respuesta ) {
        JSONRespuesta = JSON.parse(respuesta);
        if(JSONRespuesta.estado==1){
          let tabla = $("#example2").DataTable();
          let estadoAct;
          console.log(JSONRespuesta.estadoAct)
          if(JSONRespuesta.estadoAct == 1){
            estadoAct = "Completada";
          }else if(JSONRespuesta.estadoAct == 0){
            estadoAct = "Pendiente";
          }else{
            estadoAct = "Retrasada";
          }
          let Botones="";
            Botones = '<i class="fas fa-eye" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+idActualizar+')"></i>';
            Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar('+idActualizar+')"></i>';    
            Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar('+idActualizar+')"></i>';
            Botones += '<i class="fas fa-share" style="font-size:25px;color: #1855b1; margin-right: 10px;" data-toggle="modal" data-target="#modal_share_tarea"></i>';
          ////////////////////////////////////////////////
          var temp = tabla.row("#renglon_"+idActualizar).data();
          temp[0] = nom_tarea;
          temp[1] = fecha;
          temp[2] = duracion;
          temp[3] = estadoAct;
          temp[4] = Botones;
          tabla.row("#renglon_"+idActualizar).data(temp).draw();
          /////////////////////////////////////////////////
          toastr.success(JSONRespuesta.mensaje);
        }else{
          toastr.error(JSONRespuesta.mensaje);
      }
      }
    });
  }

// -----------------  DELETE TAREAS  ------------------
// Funciona al oprimir el botón rojo de eliminar para cada tarea
function actionDelete() {
  $.ajax({
    method:"POST",
    url: "../php/crud_tareas.php",
    data: {
      id: idEliminar,
      accion:"delete"
    },
    success: function( respuesta ) {
      JSONRespuesta = JSON.parse(respuesta);
      if(JSONRespuesta.estado==1){
        let tabla = $("#example2").DataTable();
        tabla.row("#renglon_"+idEliminar).remove().draw();
        toastr.success(JSONRespuesta.mensaje);
      }else{
        toastr.error(JSONRespuesta.mensaje);
      }
    }
  });
}

//Limpia las variables del create
function limpiarpagina()
{
    document.getElementById("nombreTarea").value = "";
    document.getElementById("fecha").value = "";
    document.getElementById("lugar").value = "";
    document.getElementById("duracion").value = "";
    document.getElementById("descripcion").value = "";
}

//Leemos el correo de la sesion
async function obtenerCorreo() {
  const response = await fetch("../php/session.php");
  const data = await response.json();
  const user = data.correo;
  return user;
}

//Función para rellenar lo que hay en BD, para despues poder actualizar
function identificarActualizar(id){
    idActualizar=id;
    //alert(idActualizar);
  
    $.ajax({
      method:"POST",
      url: "../php/crud_tareas.php",
      data: {
        id: idActualizar,
        accion:"read_idAct"
      },
      success: function( respuesta ) {
        JSONRespuesta = JSON.parse(respuesta);
        if(JSONRespuesta.estado==1){
          let nom_tarea = document.getElementById("nombreTarea_Update");
          nom_tarea.value = JSONRespuesta.nom_tarea;
          let descripcion = document.getElementById("descripcion_Update");
          descripcion.value = JSONRespuesta.descripcion;
          let lugar = document.getElementById("lugar_Update");
          lugar.value = JSONRespuesta.lugar;
          let fecha = document.getElementById("fecha_Update");
          fecha.value = JSONRespuesta.fecha;
          let duracion = document.getElementById("duracion_Update");
          duracion.value = JSONRespuesta.duracion;
          
        }else{
          toastr.error("Registro no encontrado");
        }
      }
    });
  }
 
//Asigna el id al idEliminar
function identificarEliminar(id){
  idEliminar=id;
  //alert(idEliminar);
}
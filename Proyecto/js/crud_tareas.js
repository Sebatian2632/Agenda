let idEliminar=0;
let idActualizar=0;
let idLeer=0;
let idMarcar=0;

// ----------------  CREATE TAREAS  -----------------
// Funciona al oprimir el botón de Nueva Tarea
async function actionCreate(){
  //Recuperamos los datos del formulario
  let nom_tarea = document.getElementById('nombreTarea').value;
  let descripcion = document.getElementById('descripcion').value;
  let lugar = document.getElementById('lugar').value;
  let fecha = document.getElementById('fecha').value;
  let duracion = document.getElementById('duracion').value;
  let estadoAct;

  let fechaActual = new Date();
  let anio = fechaActual.getFullYear();
  let mes = String(fechaActual.getMonth() + 1).padStart(2, '0');
  let dia = String(fechaActual.getDate()).padStart(2, '0');
  let fechaFormateada = anio + '-' + mes + '-' + dia;

  // Compara las fechas y actualiza el estado
  if(fecha == fechaFormateada || fecha > fechaFormateada){
      estadoAct = 0;
  }else if (fecha < fechaFormateada) {
      estadoAct = 2;
  }else{
    estadoAct = 0;
  }
  console.log(estadoAct);

  const email = await obtenerCorreo();

  // Validaciones not null, para asegurar que llene todos los campos
  if(nom_tarea === "" || descripcion === "" || lugar === "" || fecha === "" || duracion === ""){
      console.log('No puso todos los campos');
      toastr.error("Favor de rellenar todos los campos. Intente de nuevo.");
  }else{
      var formData = new FormData();
      formData.append('nom_tarea', nom_tarea);
      formData.append('fecha', fecha);
      formData.append('lugar', lugar);
      formData.append('duracion', duracion);
      formData.append('descripcion', descripcion);
      formData.append('estadoAct', estadoAct);
      formData.append('accion', "create");
      formData.append('correo', email);

      console.log(nom_tarea);
      console.log(descripcion);
      console.log(lugar);
      console.log(fecha);
      console.log(duracion);
      console.log(estadoAct);
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
            if(estadoAct == 0){
              estadoActT = "Pendiente";
            }
            if(estadoAct == 2){
              estadoActT = "Retrasada";
            }
            let Botones="";
              Botones = '<i class="fas fa-eye" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+JSONRespuesta.id+')"></i>';
              Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar('+JSONRespuesta.id+')"></i>';    
              Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar('+JSONRespuesta.id+')"></i>';
              Botones += '<i class="fas fa-share" style="font-size:25px;color: #1855b1; margin-right: 10px;" data-toggle="modal" data-target="#modal_share_tarea" onclick="Compartirid('+JSONRespuesta.id+')"></i>';
            tabla.row.add([nom_tarea, fecha, duracion, estadoActT, Botones]).draw().node().id="renglon_"+JSONRespuesta.id;
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

  let fechaActual = new Date();

  let anio = fechaActual.getFullYear();
  let mes = String(fechaActual.getMonth() + 1).padStart(2, '0');
  let dia = String(fechaActual.getDate()).padStart(2, '0');
  let fechaFormateada = anio + '-' + mes + '-' + dia;

  $.ajax({
    method:"POST",
    url: "../php/crud_tareas.php",
    data: {
      accion: "read",
      correo: email,
      fechaHoy: fechaFormateada
    },
    success: function( respuesta ) {
      JSONRespuesta = JSON.parse(respuesta);
      if(JSONRespuesta.estado==1){
        //alert(JSONRespuesta.mensaje);
        tabla = $("#example2").DataTable();
        JSONRespuesta.entregas.forEach(tareas => {
          if (tareas.aceptar == 1) {
            let estadoAct;
            if (tareas.estado == 0) {
              estadoAct = "Pendiente";
            }
            if (tareas.estado == 1) {
              estadoAct = "Completada";
            }
            if (tareas.estado == 2) {
              estadoAct = "Retrasada";
            }
            let Botones = "";
            Botones += '<i class="fas fa-eye" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById(' + tareas.idtareas + ')"></i>';
            if (tareas.propietario == 1) {
              Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar(' + tareas.idtareas + ')"></i>';
              Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar(' + tareas.idtareas + ')"></i>';
            }
            Botones += '<i class="fas fa-share" style="font-size:25px;color: #1855b1; margin-right: 10px;" data-toggle="modal" data-target="#modal_share_tarea" onclick="Compartirid(' + tareas.idtareas + ')"></i>';
            tabla.row.add([tareas.nom_tarea, tareas.fecha, tareas.duracion, estadoAct, Botones]).draw().node().id = "renglon_" + tareas.idtareas;
          }
        });
      } 
    }
  });
}

// -----------------  READ_BY_ID TAREAS  ------------------
// Funciona al oprimir el botón de morado de leer para cada tarea, o cuando se selecciona desde el calendario
function actionReadById(id){
  idLeer=id;

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
        
        let completadaCheckbox = document.getElementById("completadaRead");
        completadaCheckbox.checked = JSONRespuesta.estadoAct == 1;

      }else{
        toastr.error("Registro no encontrado");
      }
    }
  });
}

// -----------------  UPDATE TAREAS  ------------------
// Funciona al oprimir el botón verde de editar para cada tarea
async function actionUpdate(){
  const email = await obtenerCorreo();

  let nom_tarea = document.getElementById("nombreTarea_Update").value;
  let fecha = document.getElementById("fecha_Update").value;
  let lugar = document.getElementById("lugar_Update").value;
  let duracion = document.getElementById("duracion_Update").value;
  let descripcion = document.getElementById("descripcion_Update").value;
  let estadoAct;

  let fechaActual = new Date();
  
  let anio = fechaActual.getFullYear();
  let mes = String(fechaActual.getMonth() + 1).padStart(2, '0');
  let dia = String(fechaActual.getDate()).padStart(2, '0');
  let fechaFormateada = anio + '-' + mes + '-' + dia;

  console.log(fecha);
  console.log(fechaFormateada);

  if(nom_tarea === "" || descripcion === "" || lugar === "" || fecha === "" || duracion === ""){
    console.log('No puso todos los campos');
    toastr.error("Favor de rellenar todos los campos. Intente de nuevo.");
  }else{
    var formData = new FormData();
        formData.append('id', idActualizar);
        formData.append('nom_tarea', nom_tarea);
        formData.append('fecha', fecha);
        formData.append('lugar', lugar);
        formData.append('duracion', duracion);
        formData.append('descripcion', descripcion);
        formData.append('estadoAct', estadoAct);
        formData.append('fechaHoy', fechaFormateada);
        formData.append('accion', "update");
        formData.append('correo', email);
  
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
          console.log(JSONRespuesta.estadoAct)
          let EstAct;
          if(JSONRespuesta.estadoAct == 1){
            EstAct = "Completada";
          }
          if(JSONRespuesta.estadoAct == 0){
            EstAct = "Pendiente";
          }
          if(JSONRespuesta.estadoAct == 2){
            EstAct = "Retrasada";
          }
          let Botones="";
            Botones = '<i class="fas fa-eye" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+idActualizar+')"></i>';
            Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar('+idActualizar+')"></i>';    
            Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar('+idActualizar+')"></i>';
            Botones += '<i class="fas fa-share" style="font-size:25px;color: #1855b1; margin-right: 10px;" data-toggle="modal" data-target="#modal_share_tarea" onclick="Compartirid('+idActualizar+')"></i>';
          ////////////////////////////////////////////////
          var temp = tabla.row("#renglon_"+idActualizar).data();
          temp[0] = nom_tarea;
          temp[1] = fecha;
          temp[2] = duracion;
          temp[3] = EstAct;
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

// -----------------  MARCAR COMO COMPLETADA  ------------------
// Hace que el estado de la tarea sea 1 = "Completada"
async function marcarCompletada(estadoCompletada){
  idMarcar=idLeer;
  const email = await obtenerCorreo();

  let fechaActual = new Date();
  
  let anio = fechaActual.getFullYear();
  let mes = String(fechaActual.getMonth() + 1).padStart(2, '0');
  let dia = String(fechaActual.getDate()).padStart(2, '0');
  let fechaFormateada = anio + '-' + mes + '-' + dia;

  console.log(estadoCompletada);
  
  $.ajax({
    method:"POST",
    url: "../php/crud_tareas.php",
    data: {
      id: idMarcar,
      estadoCompletada: estadoCompletada,
      fechaHoy: fechaFormateada,
      accion:"read_idMarc",
      correo: email
    },
    success: function( respuesta ) {
      JSONRespuesta = JSON.parse(respuesta);
        if(JSONRespuesta.estado==1){
          let tabla = $("#example2").DataTable();
          console.log(JSONRespuesta.estadoAct)
          let estadoCompletada;
          if(JSONRespuesta.estadoAct == 1){
            estadoCompletada = "Completada";
          }
          if(JSONRespuesta.estadoAct == 0){
            estadoCompletada = "Pendiente";
          }
          if(JSONRespuesta.estadoAct == 2){
            estadoCompletada = "Retrasada";
          }

          let nomTarea = JSONRespuesta.nom_tarea;
          let fecha = JSONRespuesta.fecha;
          let duracion = JSONRespuesta.duracion;
        
          let Botones="";
            Botones = '<i class="fas fa-eye" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+idMarcar+')"></i>';
            Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar('+idMarcar+')"></i>';    
            Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar('+idMarcar+')"></i>';
            Botones += '<i class="fas fa-share" style="font-size:25px;color: #1855b1; margin-right: 10px;" data-toggle="modal" data-target="#modal_share_tarea"></i>';
          
          var temp = tabla.row("#renglon_"+idMarcar).data();
          temp[0] = nomTarea;
          temp[1] = fecha;
          temp[2] = duracion;
          temp[3] = estadoCompletada;
          temp[4] = Botones;
          tabla.row("#renglon_"+idMarcar).data(temp).draw();
          
          toastr.info(JSONRespuesta.mensaje);
          
      }else{
        toastr.error("No se pudo marcar como completada. Volver a intentarlo.");
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

// -----------------  SHARE TAREAS  ------------------
// Comparte una tarea de un usuario a otro
function Compartirid(id) {
  idtarea = id;
  document.getElementById("nombreUsuario").value = "";  // Limpiar el campo de nombreUsuario si es necesario
}

async function Compartir() {
  let nombreUsuario = "";
  const email = await obtenerCorreo();
  while (nombreUsuario == "") {
    nombreUsuario = document.getElementById("nombreUsuario").value;
  }
  $.ajax({
    method: "POST",
    url: "../php/crud_tareas.php",
    data: {
      id: idtarea,
      nombre: nombreUsuario,
      correo: email,
      accion: "share"
    },
    success: function(respuesta) {
      JSONRespuesta = JSON.parse(respuesta);
      if (JSONRespuesta.estado == 1) {
        toastr.success("Se compartió la tarea con éxito.");
      } else if (JSONRespuesta.estado == 2) {
        toastr.error("Favor de elegir un nombre diferente al suyo.");
      }
      else if(JSONRespuesta.estado == 3){
        toastr.error("Error al compartir. Intentelo de nuevo.");
      } else {
        toastr.error("Nombre no encontrado en la base de datos. Favor de verificar que el nombre sea el correcto.");
      }
    }
  });  
}
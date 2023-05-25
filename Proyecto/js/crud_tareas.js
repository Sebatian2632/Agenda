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
    //let prioridad = 1;

    // // Obtener los elementos input por su ID
    // let botonBaja = document.getElementById("option_a1");
    // let botonMedia = document.getElementById("option_a2");
    // let botonAlta = document.getElementById("option_a3");

    // // Leer el valor de cada botón
    // let prioridad = (botonAlta.checked && 3) || (botonMedia.checked && 2) || (botonBaja.checked && 1);
    let estado = 0;
    // Imprimir el valor de la prioridad seleccionada
    //sconsole.log(`Prioridad seleccionada: ${prioridad}`);

    // VALIDACIONES NOT NULL
    //if(nom_tarea === null || descripcion === null || lugar === null || fecha === null || duracion === null || prioridad === null){
    if(nom_tarea === null || descripcion === null || lugar === null || fecha === null || duracion === null){
        if(prioridad === undefined){
            console.log('me diste un click');
            alert("Favor de llenar todos los campos");
        }
    }
    else{
        const idUsuario = await obtenerCorreo();

        var formData = new FormData();
        formData.append('nom_tarea', nom_tarea);
        formData.append('fecha', fecha);
        formData.append('lugar', lugar);
        formData.append('duracion', duracion);
        formData.append('descripcion', descripcion);
        //formData.append('prioridad', prioridad);
        formData.append('estado', estado);
        formData.append('accion', "create");

        console.log(nom_tarea);
        console.log(descripcion);
        console.log(lugar);
        console.log(fecha);
        console.log(duracion);
        //console.log(prioridad);
        console.log(idUsuario);
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
              }else{
                estadoAct = "Completada";
              }
              let Botones="";
                Botones = '<i class="fas fa-file" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+JSONRespuesta.id+')"></i>';
                Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar('+JSONRespuesta.id+')"></i>';    
                Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar('+JSONRespuesta.id+')"></i>';
                Botones += '<i class="fas fa-share" style="font-size:25px;color: #1855b1; margin-right: 10px;" data-toggle="modal" data-target="#modal_share_tarea"></i>';
              tabla.row.add([nom_tarea, fecha, duracion, estadoAct, Botones]).draw().node().id="renglon_"+JSONRespuesta.id;
              //toastr.success(JSONRespuesta.mensaje);
            }else{
              //toastr.error(JSONRespuesta.mensaje);
              alert(JSONRespuesta.mensaje);
            }
          }
      });
    }    
}

// -----------------  READ TAREAS  ------------------
// Pone en la tabla todos los registros de la BD
function actionRead() {
  $.ajax({
    method:"POST",
    url: "../php/crud_tareas.php",
    data: {
      accion: "read"
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
              }else{
                estadoAct = "Completada";
              }
              let Botones="";
                Botones = '<i class="fas fa-file" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+tareas.idtareas+')"></i>';
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
          if(JSONRespuesta.estadoAct == 0){
            estadoAct = "Pendiente";
          }else{
            estadoAct = "Completada";
          }
          let Botones="";
            Botones = '<i class="fas fa-file" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+idActualizar+')"></i>';
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
          //alert(JSONRespuesta.mensaje);
          //toastr.success(JSONRespuesta.mensaje);
        }else{
          alert(JSONRespuesta.mensaje);
        //toastr.error(JSONRespuesta.mensaje);
      }
      }
    });
  }

// Limpia las variables del create
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
    //const response = await fetch("../php/session.php");
    //const data = await response.json();
    //const user = data.idUsuario;
    //return user;
    return 1;
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
          //alert("FUNCIONA HASTA AQUI");
          
        }else{
          alert("Registro no encontrado");
          //toastr.error("Registro no encontrado");
        }
      }
    });
  }

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
          let tabla = $("#tablaTareas").DataTable();
          tabla.row("#renglon_"+idEliminar).remove().draw();
          alert(JSONRespuesta.mensaje);
          toastr.success(JSONRespuesta.mensaje);
        }else{
          alert(JSONRespuesta.mensaje);
          toastr.error(JSONRespuesta.mensaje);
        }
      }
    });
  }
  
  function identificarEliminar(id){
    idEliminar=id;
    //idEliminar='17';
    //alert(idEliminar);
  }
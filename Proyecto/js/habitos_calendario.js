// -----------------  READ TAREAS  ------------------
// Pone en la tabla todos los registros de la BD pero en el calendario
async function actionReadCalendario() {
    const email = await obtenerCorreo();
  
  
    $.ajax({
      method:"POST",
      url: "../php/crud_habitos.php",
      data: {
        accion: "read",
        correo: email,
  
      },
      success: function( respuesta ) {
        JSONRespuesta = JSON.parse(respuesta);
        console.log(JSONRespuesta.estado);
  
          tabla = $("#example2").DataTable();
              JSONRespuesta.entregas.forEach(habitos => {
                
                let Botones="";
                  Botones = '<i class="fas fa-eye" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+habitos.idhabitos+')"></i>';
                  Botones += '<i class="fas fa-bars" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_estadisticas" onclick=""></i>';
                  Botones += '<br>Ya realicé el hábito hoy &nbsp; <input type="checkbox" id="RealizadoHoy" value="">'
                tabla.row.add([habitos.nom_habito, Botones]).draw().node().id="renglon_"+habitos.idhabitos;
              });
        console.log(respuesta);
      }
    });
  }
  
  
  
  // -----------------  READ_BY_ID TAREAS  ------------------
  // Funciona al oprimir el botón de morado de leer para cada tarea, o cuando se selecciona desde el calendario
  function actionReadById(id){
    idLeer=id;
  
    $.ajax({
      method:"POST",
      url: "../php/crud_habitos.php",
      data: {
        id: idLeer,
        accion:"read_id"
      },
      success: function( respuesta ) {
        console.log(respuesta);
        JSONRespuesta = JSON.parse(respuesta);
        console.log(JSONRespuesta);
        //if(JSONRespuesta.estado==1)
        console.log(JSONRespuesta.nom_habito);
          let nom_habito = document.getElementById("nombreHabitoRead");
          nom_habito.value = JSONRespuesta.nom_habito;
          
          let descripcion = document.getElementById("descripcionRead");
          descripcion.value = JSONRespuesta.descripcion;
  
          console.log(JSONRespuesta.prioridad);
          
          if(JSONRespuesta.prioridad==='3'){
            PrioridadAlta.style.display = "block"}else{PrioridadAlta.style.display = "none"}
  
          if(JSONRespuesta.prioridad==='2'){
            PrioridadMedia.style.display = "block";}else{PrioridadMedia.style.display = "none"}
  
          if(JSONRespuesta.prioridad==='1'){
            PrioridadBaja.style.display = "block"}else{PrioridadBaja.style.display = "none"}
  
          let lunes = document.getElementById("lunesRead");
          lunes.checked=false;
          let martes = document.getElementById("martesRead");
          martes.checked=false;
          let miercoles = document.getElementById("miercolesRead");
          miercoles.checked=false;
          let jueves = document.getElementById("juevesRead");
          jueves.checked=false;
          let viernes = document.getElementById("viernesRead");
          viernes.checked=false;
          let sabado = document.getElementById("sabadoRead");
          sabado.checked=false;
          let domingo = document.getElementById("domingoRead");
          domingo.checked=false;
          if(JSONRespuesta.lunes=='1'){
            
            lunes.checked=true;
            }
          if(JSONRespuesta.martes=='1'){
            
            martes.checked=true;
            }
          if(JSONRespuesta.miercoles=='1'){
            
            miercoles.checked=true;
            }
          if(JSONRespuesta.jueves=='1'){
            
            jueves.checked=true;
            }
          if(JSONRespuesta.viernes=='1'){
            
            viernes.checked=true;
            }  
         if(JSONRespuesta.sabado=='1'){
            
            sabado.checked=true;
            }   
         if(JSONRespuesta.domingo=='1'){
            
            domingo.checked=true;
            }
        //}else{
        //  toastr.error("Registro no encontrado");
        //}
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
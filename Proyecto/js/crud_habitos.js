//para la base de datos de habitos
async function actionCreate()
{
//obtenemos el nombre, descripcion, prioridad, y días seleccionados
    let nom_habito = document.getElementById('nombreHabito').value;//:)
    let descripcion = document.getElementById('descripcion').value;

    let botonBaja = document.getElementById("baja");
    let botonMedia = document.getElementById("media");
    let botonAlta = document.getElementById("alta");


    let lunes = document.getElementById("Lunes").value;
    let martes = document.getElementById("Martes").value;
    let miercoles = document.getElementById("Miercoles").value;
    let jueves = document.getElementById("Jueves").value;
    let viernes = document.getElementById("Viernes").value;
    let sabado = document.getElementById("Sabado").value;
    let domingo = document.getElementById("Domingo").value;

    let prioridad = (botonAlta.checked && 3) || (botonMedia.checked && 2) || (botonBaja.checked && 1);
    const email = await obtenerCorreo();
    //verificamos que tenga nombre
    if((nom_habito == '' || descripcion == ''|| prioridad== '')||(botonBaja=='' && botonMedia=='' &&botonAlta=='')||
    (lunes=='0' && martes=='0' && miercoles=='0' && jueves=='0' && viernes=='0' && sabado=='0' && domingo=='0')){
        
      alert("Favor de llenar todos los campos y seleccionar almenos un dia para realizar el habito");
        
    }//verificamos que al menos haya seleccionado un dia
    else{
      var formData = new FormData();
      formData.append('nom_habito', nom_habito);
      formData.append('descripcion', descripcion);
      formData.append('prioridad', prioridad);
      formData.append('lunes', lunes);
      formData.append('martes', martes);
      formData.append('miercoles', miercoles);
      formData.append('jueves', jueves);
      formData.append('viernes', viernes);
      formData.append('sabado', sabado);
      formData.append('domingo', domingo);
      formData.append('accion', "create");
      formData.append('correo', email);

        console.log(nom_habito);
        console.log(descripcion);
        console.log(prioridad);
        console.log(lunes);
        console.log(martes);
        console.log(miercoles);
        console.log(jueves);
        console.log(viernes);
        console.log(sabado);
        console.log(domingo);
       console.log(email);
       
        limpiarpagina();

        $.ajax({ 
          
          method:"POST",
          url: "../php/crud_habitos.php",
          data: formData,
          contentType: false,
          processData: false,
          
           success: function(respuesta) {

            //alert(respuesta);
            console.log(respuesta);
           JSONRespuesta = JSON.parse(respuesta); 
            if(JSONRespuesta.estado==1){
              alert(JSONRespuesta.mensaje);
              tabla = $("#example2").DataTable();
              
              let Botones="";
                Botones = '<i class="fas fa-eye" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+JSONRespuesta.id+')"></i>';
                Botones += '<i class="fas fa-bars" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_estadisticas" onclick=""></i>';
                Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar('+JSONRespuesta.id+')"></i>';    
                Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar('+JSONRespuesta.id+')"></i>';
              tabla.row.add([nom_habito, descripcion, prioridad, Botones]).draw().node().id="renglon_"+JSONRespuesta.id;
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
                Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar('+habitos.idhabitos+')"></i>';    
                Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar('+habitos.idhabitos+')"></i>';
                
              tabla.row.add([habitos.nom_habito, habitos.descripcion, habitos.prioridad, Botones]).draw().node().id="renglon_"+habitos.idhabitos;
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


// -----------------  UPDATE TAREAS  ------------------
// Funciona al oprimir el botón verde de editar para cada tarea
async function actionUpdate(){
  const email = await obtenerCorreo();

  let nom_habito = document.getElementById("nombreHabito_Update").value;
  let descripcion = document.getElementById("descripcion_Update").value;  

  var baja = document.getElementById("baja_Update").checked ? 1 : 0;
  var media = document.getElementById("media_Update").checked ? 1 : 0;
  var alta = document.getElementById("alta_Update").checked ? 1 : 0;

  let prioridad;

    if(baja === 1){
      prioridad = 1;
    }
    else if(media === 1){
      prioridad = 2;
    }else if(alta === 1){
      prioridad = 3;
    }

    console.log(prioridad);

    let lunes = document.getElementById("lunes_Update").value;
    let martes = document.getElementById("martes_Update").value;
    let miercoles = document.getElementById("miercoles_Update").value;
    let jueves = document.getElementById("jueves_Update").value;
    let viernes = document.getElementById("viernes_Update").value;
    let sabado = document.getElementById("sabado_Update").value;
    let domingo = document.getElementById("domingo_Update").value;
    console.log(domingo.value);   

    if((nom_habito == '' || descripcion == ''|| prioridad== '')||(baja=='' && media=='' &&alta=='')||
    (lunes=='0' && martes=='0' && miercoles=='0' && jueves=='0' && viernes=='0' && sabado=='0' && domingo=='0')){
    console.log('No puso todos los campos');
    toastr.error("Favor de rellenar todos los campos. Intente de nuevo.");
  }else{
    var formData = new FormData();
        formData.append('id', idActualizar);
        formData.append('nom_habito', nom_habito);
        formData.append('descripcion', descripcion);
        formData.append('prioridad', prioridad);
        formData.append('lunes', lunes);
        formData.append('martes', martes);
        formData.append('miercoles', miercoles);
        formData.append('jueves', jueves);
        formData.append('viernes', viernes);
        formData.append('sabado', sabado);
        formData.append('domingo', domingo);
        formData.append('accion', "update");
        formData.append('correo', email);
  
    $.ajax({
      method:"POST",
      url: "../php/crud_habitos.php",
      data: formData,
      contentType: false,
      processData: false,
      
      success: function( respuesta ) {
        JSONRespuesta = JSON.parse(respuesta);
        if(JSONRespuesta.estado==1){
          let Botones="";
            Botones = '<i class="fas fa-eye" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_read_tarea" onclick="actionReadById('+idActualizar+')"></i>';
            Botones += '<i class="fas fa-bars" style="font-size:25px;color: #af66eb; margin-right: 10px;" data-toggle="modal" data-target="#modal_estadisticas" onclick=""></i>';
            Botones += '<i class="fas fa-edit" style="font-size:25px;color: #168645; margin-right: 10px;" data-toggle="modal" data-target="#modal_update_tarea" onclick="identificarActualizar('+idActualizar+')"></i>';    
            Botones += '<i class="fas fa-trash" style="font-size:25px;color: #da2c2c; margin-right: 10px;" data-toggle="modal" data-target="#modal_delete_tarea" onclick="identificarEliminar('+idActualizar+')"></i>';
            
          ////////////////////////////////////////////////
          var temp = tabla.row("#renglon_"+idActualizar).data();
          temp[0] = nom_habito;
          temp[1] = descripcion;
          temp[2] = prioridad;
          temp[3] = Botones;
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


//Función para rellenar lo que hay en BD, para despues poder actualizar
function identificarActualizar(id){
  idActualizar=id;
  //alert(idActualizar);

  $.ajax({
    method:"POST",
    url: "../php/crud_habitos.php",
    data: {
      id: idActualizar,
      accion:"read_idAct"
    },
    success: function( respuesta ) {
      JSONRespuesta = JSON.parse(respuesta);

        let nom_habito = document.getElementById("nombreHabito_Update");
        nom_habito.value = JSONRespuesta.nom_habito;
        let descripcion = document.getElementById("descripcion_Update");
        descripcion.value = JSONRespuesta.descripcion;
        baja.value = document.getElementById("baja_Update");
        media.value =document.getElementById("media_Update");
        alta.value = document.getElementById("alta_Update");
        if(JSONRespuesta.prioridad=== '1'){
          baja.checked=true;
        }
        if(JSONRespuesta.prioridad=== '2'){
         media.checked=true;
        }
        if(JSONRespuesta.prioridad=== '3'){
          alta.checked=true;
        }

        let prioridad = (alta.checked && 3) || (media.checked && 2) || (baja.checked && 1);//
        prioridad.value= JSONRespuesta.prioridad;
        console.log(prioridad);
        let lunes = document.getElementById("lunes_Update");
        lunes.checked=false;
        let martes = document.getElementById("martes_Update");
        martes.checked=false;
        let miercoles = document.getElementById("miercoles_Update");
        miercoles.checked=false;
        let jueves = document.getElementById("jueves_Update");
        jueves.checked=false;
        let viernes = document.getElementById("viernes_Update");
        viernes.checked=false;
        let sabado = document.getElementById("sabado_Update");
        sabado.checked=false;
        let domingo = document.getElementById("domingo_Update");
        domingo.checked=false;
        if(JSONRespuesta.lunes=='1'){
          
          lunes.checked=true;
          lunes.value = "1";
          }
        if(JSONRespuesta.martes=='1'){
          
          martes.checked=true;
          martes.value = "1";
          }
        if(JSONRespuesta.miercoles=='1'){
          
          miercoles.checked=true;
          miercoles.value = "1";
          }
        if(JSONRespuesta.jueves=='1'){
          
          jueves.checked=true;
          jueves.value = "1";
          viernes.value = "1";
          }
        if(JSONRespuesta.viernes=='1'){
          
          viernes.checked=true;
          }  
       if(JSONRespuesta.sabado=='1'){
          
          sabado.checked=true;
          sabado.value = "1";
          }   
       if(JSONRespuesta.domingo=='1'){
          
          domingo.checked=true;
          domingo.value = "1";
          }

     //}else{
       //toastr.error("Registro no encontrado");
     //}
    }
  });
}


// -----------------  DELETE TAREAS  ------------------
// Funciona al oprimir el botón rojo de eliminar para cada tarea
function actionDelete() {
  $.ajax({
    method:"POST",
    url: "../php/crud_habitos.php",
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

function identificarEliminar(id){
  idEliminar=id;
  //alert(idEliminar);
}


//Limpiar las variables del formulario
function limpiarpagina()
{
    document.getElementById("nombreHabito").value = "";
    document.getElementById("descripcion").value = "";
}

//Leemos el correo de la sesion
async function obtenerCorreo() {
    const response = await fetch("../php/session.php");
    const data = await response.json();
    const user = data.correo;
    return user;
  }


//funcion para cuando se selecciona frecuencia de lunes a viernes
function lunavie() {
  var checkboxContainer = document.getElementById("checkboxContainer");
  var checkboxContainerUpdate = document.getElementById("checkboxContainerUpdate");
  var lunes = document.getElementById('Lunes');
  var martes = document.getElementById('Martes');
  var miercoles = document.getElementById('Miercoles');
  var jueves = document.getElementById('Jueves');
  var viernes = document.getElementById('Viernes');
  var sabado = document.getElementById('Sabado');
  var domingo = document.getElementById('Domingo');
  var lunes_Update = document.getElementById('lunes_Update');
  var martes_Update = document.getElementById('martes_Update');
  var miercoles_Update = document.getElementById('miercoles_Update');
  var jueves_Update = document.getElementById('jueves_Update');
  var viernes_Update = document.getElementById('viernes_Update');
  var sabado_Update = document.getElementById('sabado_Update');
  var domingo_Update = document.getElementById('domingo_Update');
  checkboxContainer.style.display = "none";
  checkboxContainerUpdate.style.display = "none";
  lunes.checked = true;
  martes.checked = true;
  miercoles.checked = true;
  jueves.checked = true;
  viernes.checked = true;
  sabado.checked = false;
  domingo.checked = false;

  lunes_Update.checked = true;
  martes_Update.checked = true;
  miercoles_Update.checked = true;
  jueves_Update.checked = true;
  viernes_Update.checked = true;
  sabado_Update.checked = false;
  domingo_Update.checked = false;

}
//funcion para cuando se selecciona diario como frecuencia
function diario() {
  var checkboxContainer = document.getElementById("checkboxContainer");
  var checkboxContainerUpdate = document.getElementById("checkboxContainerUpdate");
  var lunes = document.getElementById('Lunes');
  var martes = document.getElementById('Martes');
  var miercoles = document.getElementById('Miercoles');
  var jueves = document.getElementById('Jueves');
  var viernes = document.getElementById('Viernes');
  var sabado = document.getElementById('Sabado');
  var domingo = document.getElementById('Domingo');

  var lunes_Update = document.getElementById('lunes_Update');
  var martes_Update = document.getElementById('martes_Update');
  var miercoles_Update = document.getElementById('miercoles_Update');
  var jueves_Update = document.getElementById('jueves_Update');
  var viernes_Update = document.getElementById('viernes_Update');
  var sabado_Update = document.getElementById('sabado_Update');
  var domingo_Update = document.getElementById('domingo_Update');

  checkboxContainer.style.display = "none";
  checkboxContainerUpdate.style.display = "none";
  lunes.checked = true;
  martes.checked = true;
  miercoles.checked = true;
  jueves.checked = true;
  viernes.checked = true;
  sabado.checked = true;
  domingo.checked = true;

  lunes_Update.checked = true;
  martes_Update.checked = true;
  miercoles_Update.checked = true;
  jueves_Update.checked = true;
  viernes_Update.checked = true;
  sabado_Update.checked = true;
  domingo_Update.checked = true;
}
//funcion para cuando se selecciona frecuencia personalizada
function personalizado() {
  var checkboxContainer = document.getElementById("checkboxContainer");
  var checkboxContainerUpdate = document.getElementById("checkboxContainerUpdate");
  checkboxContainer.style.display = "block";
  checkboxContainerUpdate.style.display = "block";
  }
//funcion para asignar el value de cada casilla seleccionada
  function dias(){
    var lunes = document.getElementById('Lunes');
    var martes = document.getElementById('Martes');
    var miercoles = document.getElementById('Miercoles');
    var jueves = document.getElementById('Jueves');
    var viernes = document.getElementById('Viernes');
    var sabado = document.getElementById('Sabado');
    var domingo = document.getElementById('Domingo');

    var lunes_Update = document.getElementById('lunes_Update');
    var martes_Update = document.getElementById('martes_Update');
    var miercoles_Update = document.getElementById('miercoles_Update');
    var jueves_Update = document.getElementById('jueves_Update');
    var viernes_Update = document.getElementById('viernes_Update');
    var sabado_Update = document.getElementById('sabado_Update');
    var domingo_Update = document.getElementById('domingo_Update');
  
  if(lunes.checked==true){
    lunes.value = "1";
  }else{
    lunes.value = "0";
  }
  if(martes.checked==true){
    martes.value = "1";
  }else{
    martes.value = "0";
  }
  if(miercoles.checked==true){
    miercoles.value = "1";
  }else{
    miercoles.value = "0";
  }
  if(jueves.checked==true){
    jueves.value = "1";
  }else{
    jueves.value = "0";
  }
  if(viernes.checked==true){
    viernes.value = "1";
  }else{
    viernes.value = "0";
  }
  if(sabado.checked==true){
    sabado.value = "1";
  }else{
    sabado.value = "0";
  }
  if(domingo.checked==true){
    domingo.value = "1";
  }else{
    domingo.value = "0";
  }
  
  
  if(lunes_Update.checked==true){
    lunes_Update.value = "1";
  }else{
    lunes_Update.value = "0";
  }
  if(martes_Update.checked==true){
    martes_Update.value = "1";
  }else{
    martes_Update.value = "0";
  }
  if(miercoles_Update.checked==true){
    miercoles_Update.value = "1";
  }else{
    miercoles_Update.value = "0";
  }
  if(jueves_Update.checked==true){
    jueves_Update.value = "1";
  }else{
    jueves_Update.value = "0";
  }
  if(viernes_Update.checked==true){
    viernes_Update.value = "1";
  }else{
    viernes_Update.value = "0";
  }
  if(sabado_Update.checked==true){
    sabado_Update.value = "1";
  }else{
    sabado_Update.value = "0";
  }
  if(domingo_Update.checked==true){
    domingo_Update.value = "1";
  }else{
    domingo_Update.value = "0";
  } 
  console.log(domingo_Update.value);
}
//funcion mostrar checkboxs en editar habito
function showCheckbox01() {
  var checkboxContainer01 = document.getElementById("checkboxContainerUpdate");
  checkboxContainer01.style.display = "block";
}
//funcion ocultar checkboxs en editar habito
function ocultarCheckbox01() {
  var checkboxContainer01 = document.getElementById("checkboxContainer01");
  checkboxContainer01.style.display = "none";
}

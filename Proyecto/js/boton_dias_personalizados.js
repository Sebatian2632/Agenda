//funcion para cuando se selecciona frecuencia de lunes a viernes
function lunavie() {
  var checkboxContainer = document.getElementById("checkboxContainer");
  var lunes = document.getElementById('Lunes');
  var martes = document.getElementById('Martes');
  var miercoles = document.getElementById('Miercoles');
  var jueves = document.getElementById('Jueves');
  var viernes = document.getElementById('Viernes');
  var sabado = document.getElementById('Sabado');
  var domingo = document.getElementById('Domingo');
  checkboxContainer.style.display = "none";
  lunes.checked = true;
  martes.checked = true;
  miercoles.checked = true;
  jueves.checked = true;
  viernes.checked = true;
  sabado.checked = false;
  domingo.checked = false;

}
//funcion para cuando se selecciona diario como frecuencia
function diario() {
  var checkboxContainer = document.getElementById("checkboxContainer");
  var lunes = document.getElementById('Lunes');
  var martes = document.getElementById('Martes');
  var miercoles = document.getElementById('Miercoles');
  var jueves = document.getElementById('Jueves');
  var viernes = document.getElementById('Viernes');
  var sabado = document.getElementById('Sabado');
  var domingo = document.getElementById('Domingo');
  checkboxContainer.style.display = "none";
  lunes.checked = true;
  martes.checked = true;
  miercoles.checked = true;
  jueves.checked = true;
  viernes.checked = true;
  sabado.checked = true;
  domingo.checked = true;
}
//funcion para cuando se selecciona frecuencia personalizada
function personalizado() {
  var checkboxContainer = document.getElementById("checkboxContainer");
  checkboxContainer.style.display = "block";
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
}
//funcion mostrar checkboxs en editar habito
function showCheckbox01() {
  var checkboxContainer01 = document.getElementById("checkboxContainer01");
  checkboxContainer01.style.display = "block";
}
//funcion ocultar checkboxs en editar habito
function ocultarCheckbox01() {
  var checkboxContainer01 = document.getElementById("checkboxContainer01");
  checkboxContainer01.style.display = "none";
}

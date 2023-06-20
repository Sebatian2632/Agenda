/*
Nombre del programa: login.js
Descripción:
Contiene las funciones necesarias para iniciar sesión en el sistema, validando los valores de entrada
*/

function validateForm(){
    var x = document.forms["inicio"]["correo"].value;
    var x1 = document.forms["inicio"]["clave"].value;

      if(x==""|| x1==""){

        mensaje();
        return false;

      }
}

function mensaje(){
    document.getElementById('error-message').style.display = 'block';
}
    

/*
Nombre del programa: register.js
Descripción:
Contiene las funciones necesarias para mostrar hacer el registro validado de usuarios
*/

function validateForm(){
    var x = document.forms["registrar"]["correo"].value;
    var x1 = document.forms["registrar"]["clave"].value;
    var x2 = document.forms["registrar"]["usuario"].value;
    var x3 = document.forms["registrar"]["clavec"].value;


      if(x==""|| x1=="" || x2==""){

        document.getElementById('error-message1').style.display = 'block';
        document.getElementById('error-message2').style.display = 'none';
        return false;

      }
      else if(x1 != x3)
      {
        document.getElementById('error-message1').style.display = 'none';
        document.getElementById('error-message2').style.display = 'block';
        return false;
      }
    }
function validateForm(){
    var x = document.forms["inicio"]["correo"].value;
    var x1 = document.forms["inicio"]["clave"].value;

      if(x==""|| x1==""){

        alert("Campos incompletos");
        return false;

      }
    }
    
//para la base de datos de habitos
async function actionCreate()
{
//obtenemos el nombre, descripcion, prioridad, y días seleccionados
    let nombre = document.getElementById('nombreHabito').value;//:)
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

    //verificamos que tenga nombre
    if(nombre == '' || descripcion == ''){
        
            alert("Favor de llenar todos los campos");
        
    }//verificamos que al menos haya seleccionado un dia
    else{ if(lunes=='0' && martes=='0' && miercoles=='0' && jueves=='0' && viernes=='0' && sabado=='0' && domingo=='0')
    { alert("Debe de seleccionar al menos un día para el habito");}
    else{
        const idUsuario = await obtenerCorreo();

        console.log(nombre);
        console.log(descripcion);
        console.log(prioridad);
        console.log(lunes);
        console.log(martes);
        console.log(miercoles);
        console.log(jueves);
        console.log(viernes);
        console.log(sabado);
        console.log(domingo);
        console.log(idUsuario);
        limpiarpagina();

        fetch('../php/crud_habitos.php', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json'
            },
            body: JSON.stringify({
            nombre,
            descripcion,
            prioridad,
            lunes,
            martes,
            miercoles,
            jueves,
            viernes,
            sabado,
            domingo,
            idUsuario,
            accion : 'create'
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            
        });
    }    
}

//Limpiar las variables del formulario
function limpiarpagina()
{
    document.getElementById("nombreHabito").value = "";
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
}


// ----------------  CREATE TAREAS  -----------------
// Funciona al oprimir el botón de Nueva Tarea

async function actionCreate()
{
    //Recuperamos los datos del formulario
    let nombre = document.getElementById('nombreTarea').value;
    let descripcion = document.getElementById('descripcion').value;
    let lugar = document.getElementById('lugar').value;
    let fecha = document.getElementById('fecha').value;
    let duracion = document.getElementById('duracion').value;
    //let prioridad = document.getElementById('prioridad').value;           //ESTA PENDIENTE LO DE PRIORIDAD
    let prioridad = 1;

    // VALIDACIONES NOT NULL
    if(nombre === null || descripcion === null || lugar === null || fecha === null || duracion === null || prioridad === null){
        console.log('me diste un click');
        alert("Favor de llenar todos los campos")
    }
    else{
        const idUsuario = await obtenerCorreo();

        console.log(nombre);
        console.log(descripcion);
        console.log(lugar);
        console.log(fecha);
        console.log(duracion);
        console.log(prioridad);
        console.log(idUsuario);
        limpiarpagina();

        fetch('../php/crud_tareas.php', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json'
            },
            body: JSON.stringify({
            nombre,
            fecha,
            lugar,
            duracion,
            descripcion,
            prioridad,
            idUsuario,
            accion : 'create'
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            if(data.Respuesta === 1){
            alert("Los datos se han guardado exitosamente");
            limpiarpagina();
            //actionRead();
            }
            else{
            alert("Fallo al guardar los datos");
            }
        });
    }    
}

//Limpiar las variables del formulario
function limpiarpagina()
{
    document.getElementById("nombreTarea").value = "";
    document.getElementById("fecha").value = "";
    document.getElementById("lugar").value = "";
    document.getElementById("duracion").value = "";
    document.getElementById("descripcion").value = "";
    //document.getElementById("prioridad").value = "";
}

//Leemos el correo de la sesion
async function obtenerCorreo() {
    //const response = await fetch("../php/session.php");
    //const data = await response.json();
    //const user = data.idUsuario;
    //return user;
    return 1;
}

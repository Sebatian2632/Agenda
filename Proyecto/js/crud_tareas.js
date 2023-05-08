
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
    //let prioridad = 1;

    // Obtener los elementos input por su ID
    let botonBaja = document.getElementById("option_a1");
    let botonMedia = document.getElementById("option_a2");
    let botonAlta = document.getElementById("option_a3");

    // Leer el valor de cada botón
    let prioridad = (botonAlta.checked && 3) || (botonMedia.checked && 2) || (botonBaja.checked && 1);

    // Imprimir el valor de la prioridad seleccionada
    console.log(`Prioridad seleccionada: ${prioridad}`);

    // VALIDACIONES NOT NULL
    if(nombre === null || descripcion === null || lugar === null || fecha === null || duracion === null || prioridad === null){
        if(prioridad === undefined){
            console.log('me diste un click');
            alert("Favor de llenar todos los campos");
        }
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
}

//Leemos el correo de la sesion
async function obtenerCorreo() {
    //const response = await fetch("../php/session.php");
    //const data = await response.json();
    //const user = data.idUsuario;
    //return user;
    return 1;
}

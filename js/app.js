//Función para mostrar los buscadores según la opción seleccionada
function mostrarBuscador(opcion) {
    // Ocultar todos los buscadores por defecto
    document.querySelectorAll('.buscador').forEach(function (buscador) {
      buscador.style.display = 'none';
    });

    // Mostrar el buscador correspondiente
    document.getElementById('buscador-' + opcion).style.display = 'inline';

    //Mostrar el botón para realizar la búsqueda 
    document.getElementById('boton-buscar').style.display = 'inline';

    // Establecer el valor del campo oculto tipo_busqueda
    document.getElementById('tipo_busqueda').value = opcion;
}

//Obtener el formulario y añadirle una escucha cuando se envía
const formulario = document.getElementById("formulario-busqueda");
formulario.addEventListener("submit", enviarFormulario)

function enviarFormulario(evento){
  //Comprobar que los datos del formulario están correctos
  if (!validarDatos()) {
    //alert("Error con el formulario");
    //Prevenir el envío del formulario
    evento.preventDefault();
  }
}

function validarDatos() {
  //Obtener el tipo de búsqueda 
  let tipoBusqueda =document.getElementById("tipo_busqueda").value;

  //Validar datos dependiendo del tipo de búsqueda
  switch (tipoBusqueda) {

    //Si se ha buscado por nombre de película
    case "peliculas":
      //Comprobar que la longitud del texto introducido es mayor a 0
      if (document.getElementById("pelicula").value.length > 0) {
        return true;
      } else {
        alert("El campo de búsqueda no puede estar vacío");
        return false;
      }
      break;

    //Si se ha buscado por género
    case "genero":
      //Comprobar que la longitud del texto introducido es mayor a 0
      if (document.getElementById("genero").value.length > 0) {
        return true;
      } else {
        alert("El campo de búsqueda no puede estar vacío");
        return false;
      }
      break;

    //Si se ha buscado por año
    case "ano":
      //Comprobar que el valor introducido es un número
      if (!isNaN(document.getElementById("ano").value) && document.getElementById("ano").value.length >= 0) {
        return true;
      } else {
        alert("El campo debe contener un número.");
        return false;
      }
      break;

    //Si se ha buscado por actor
    case "actores":
      //Comprobar que no se ha seleccionado la opción por defecto
      if (document.getElementById("actor").value !== "default") {
        return true;
      } else {
        alert("No puedes seleccionar la opción por defecto");
        return false;
      }
      break;

    //Si se ha buscado por director
    case "directores":
      //Comprobar que no se ha seleccionado la opción por defecto
      if (document.getElementById("director").value !== "defecto") {
        return true;
      } else {
        alert("No puedes seleccionar la opción por defecto");
        return false;
      }
      break;
  
    default:
      alert("Se ha producido un error: " + tipoBusqueda);
      break;
  }
  
}
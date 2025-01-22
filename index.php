<!-- Conectarse a la base de datos -->
<?php
//traer el archivo de configuración
require_once './sql/config.php';

//traer el archivo de conexión
$archivoConexion = './sql/conexion.php';
if (!file_exists($archivoConexion)) {
  die("ERROR: El archivo de conexión no existe.");
}
require_once $archivoConexion;

//conexión MysqlI con la base de datos
$db = getConexion();
?>

<!doctype html>
<html class="no-js" lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Videoclub</title>
  <link rel="stylesheet" type="text/css" href="./css/style.css">
  <meta name="description" content="">

  <meta property="og:title" content="">
  <meta property="og:type" content="">
  <meta property="og:url" content="">
  <meta property="og:image" content="">
  <meta property="og:image:alt" content="">

  <link rel="icon" href="./movie.ico" sizes="any">
  <link rel="icon" href="./movie.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="movie.png">

  <link rel="manifest" href="site.webmanifest">
  <meta name="theme-color" content="#fafafa">
</head>

<body>

  <h1>Buscar por...</h1>

  <!-- Lista con elementos que llamarán a un script para mostrar la opción de búsqueda dependiendo de la elegida -->
  <ul>
    <li><a href="#" onclick="mostrarBuscador('peliculas')">Películas</a></li>
    <li><a href="#" onclick="mostrarBuscador('actores')">Actores</a></li>
    <li><a href="#" onclick="mostrarBuscador('directores')">Directores</a></li>
    <li><a href="#" onclick="mostrarBuscador('genero')">Género</a></li>
    <li><a href="#" onclick="mostrarBuscador('ano')">Año</a></li>
  </ul>

  <!-- Formulario para realizar la búsqueda -->
  <form action="" method="post" id="formulario-busqueda">
    <input type="hidden" name="tipo_busqueda" id="tipo_busqueda" value="" />

    <!-- Buscar por nombre de película -->
    <div id="buscador-peliculas" class="buscador" style="display:none;">
      <label for="pelicula">Película: </label>
      <input type="text" name="pelicula" id="pelicula" />
    </div>

    <!-- Buscar por género -->
    <div id="buscador-genero" class="buscador" style="display:none;">
      <label for="genero">Género: </label>
      <input type="text" name="genero" id="genero" />
    </div>

    <!-- Buscar por año -->
    <div id="buscador-ano" class="buscador" style="display:none;">
      <label for="ano">Año: </label>
      <input type="number" name="ano" id="ano" />
    </div>

    <!-- Buscar por actor -->
    <div id="buscador-actores" class="buscador" style="display:none;">
      <label for="actor">Nombre del actor: </label>
      <select name="actor" id="actor">
        <option value="default">Selecciona un actor</option>
        <?php
        // Consulta para obtener los actores
        $obtenerActores = "SELECT nombre, apellidos FROM actores;";
        $resultado = $db->query($obtenerActores);

        if ($resultado) {
          // Verificar si hay al menos un actor en la base de datos
          if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
              // Generar opciones para el desplegable
              $nombreCompleto = $fila['nombre'] . " " . $fila['apellidos'];
              echo "<option value='" . htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') . "</option>";
            }
          } else {
            // Mensaje en caso de que no haya actores
            echo "<option value=''>No hay actores en la base de datos</option>";
          }
        } else {
          // Mensaje en caso de error en la consulta
          echo "<option value=''>Error al obtener actores</option>";
        }
        ?>
      </select>
    </div>


    <!-- Buscar por director -->
    <div id="buscador-directores" class="buscador" style="display:none;">
      <label for="director">Nombre del director: </label>
      <select name="director" id="director">
        <option value="defecto">Selecciona un director</option>
        <?php
        // Consulta para obtener los directores
        $obtenerDirectores = "SELECT nombre, apellidos FROM director;";
        $resultado = $db->query($obtenerDirectores);

        if ($resultado) {
          // Verificar si hay al menos un director en la base de datos
          if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
              // Generar opciones para el desplegable
              $nombreCompleto = $fila['nombre'] . " " . $fila['apellidos'];
              echo "<option value='" . htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') . "</option>";
            }
          } else {
            // Mensaje en caso de que no haya directores
            echo "<option value=''>No hay directores en la base de datos</option>";
          }
        } else {
          // Mensaje en caso de error en la consulta
          echo "<option value=''>Error al obtener directores</option>";
        }
        ?>
      </select>
    </div>

    <!-- Botón para enviar la consulta -->
    <input type="submit" name="buscar" value="Buscar" id="boton-buscar" onclick="enviarFormulario()" style="display:none;" />

  </form>

  <!-- Código php -->
  <?php
  //comprobar que se ha pulsado el botón de envío
  if (isset($_POST['buscar'])) {
    //obtener los valores de los input
    $tipo_busqueda = $_POST['tipo_busqueda'] ?? '';
    $pelicula = $_POST['pelicula'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $ano = $_POST['ano'] ?? '';
    $actor = $_POST['actor'] ?? '';
    $director = $_POST['director'] ?? '';

    //consultas dependiendo del valor elegido
    switch ($tipo_busqueda) {
      case 'peliculas': //aparentemente funciona
        //consulta para buscar las películas
        $consultaPeliculas = "SELECT  
                        peliculas.nombre AS nombre_pelicula, 
                        peliculas.genero, 
                        peliculas.ano, 
                        CONCAT(director.nombre, ' ', director.apellidos) AS nombre_director,
                        GROUP_CONCAT(CONCAT(actores.nombre, ' ', actores.apellidos) SEPARATOR ', ') AS nombres_actores
                    FROM 
                        peliculas
                    JOIN 
                        director ON peliculas.ID_director = director.ID_director
                    LEFT JOIN 
                        participa ON peliculas.ID_pelicula = participa.ID_pelicula
                    LEFT JOIN 
                        actores ON participa.ID_actor = actores.ID_actor
                    WHERE peliculas.nombre LIKE ?
                    GROUP BY 
                        peliculas.ID_pelicula;
                     ";

        //preparar la consulta
        if ($stmt = $db->prepare($consultaPeliculas)) {
          //blindar el parámetro
          $param = "%" . $pelicula . "%";
          $stmt->bind_param("s", $param);

          //ejecutar la consulta
          $stmt->execute();

          //obtener el resultado
          $resultado = $stmt->get_result();

          //comprobar que hay al menos 1 resultado
          if ($resultado->num_rows > 0) {
            //abrir la tabla
            echo "<table>";
            echo "<tr><th>Nombre buscado o parecido</th><th>Género</th><th>Año</th><th>Actores</th><th>Director</th></tr>";

            //condición mientras que haya resultados
            while ($fila = $resultado->fetch_assoc()) {
              //añadir los datos a la tabla
              echo "<tr>";
              echo "<td>" . $fila['nombre_pelicula'] . "</td>";
              echo "<td>" . $fila['genero'] . "</td>";
              echo "<td>" . $fila['ano'] . "</td>";
              echo "<td>" . $fila['nombres_actores'] . "</td>"; // Lista de actores
              echo "<td>" . $fila['nombre_director'] . "</td>";
              echo "</tr>";
            }

            //cerrar la tabla
            echo "</table>";
          } else {
            echo "<strong>La búsqueda no devolvió ningún resultado</strong>";
          }


          //cerrar la consulta
          $stmt->close();
        } else {
          echo "ERROR: No se pudo preparar la consulta.";
        }
        break;

      case 'genero': //aparentemente funciona
        //consulta para buscar las películas por género
        $consultaGenero = "SELECT  
                        peliculas.nombre AS nombre_pelicula, 
                        peliculas.genero, 
                        peliculas.ano, 
                        CONCAT(director.nombre, ' ', director.apellidos) AS nombre_director,
                        GROUP_CONCAT(CONCAT(actores.nombre, ' ', actores.apellidos) SEPARATOR ', ') AS nombres_actores
                    FROM 
                        peliculas
                    JOIN 
                        director ON peliculas.ID_director = director.ID_director
                    LEFT JOIN 
                        participa ON peliculas.ID_pelicula = participa.ID_pelicula
                    LEFT JOIN 
                        actores ON participa.ID_actor = actores.ID_actor
                    WHERE peliculas.genero LIKE ?
                    GROUP BY 
                        peliculas.ID_pelicula;";

        //preparar la consulta
        if ($stmt = $db->prepare($consultaGenero)) {
          //blindar el parámetro
          $param = "%" . $genero . "%";
          $stmt->bind_param("s", $param);

          //ejecutar la consulta
          $stmt->execute();

          //obtener el resultado
          $resultado = $stmt->get_result();

          //comprobar que hay al menos 1 resultado
          if ($resultado->num_rows > 0) {
            //abrir la tabla
            echo "<table>";
            echo "<tr><th>Película</th><th>Género buscado</th><th>Año</th><th>Actores</th><th>Director</th></tr>";

            //condición mientras que haya resultados
            while ($fila = $resultado->fetch_assoc()) {
              //añadir los datos a la tabla
              echo "<tr>";
              echo "<td>" . $fila['nombre_pelicula'] . "</td>";
              echo "<td>" . $fila['genero'] . "</td>";
              echo "<td>" . $fila['ano'] . "</td>";
              echo "<td>" . $fila['nombres_actores'] . "</td>"; // Lista de actores
              echo "<td>" . $fila['nombre_director'] . "</td>";
              echo "</tr>";
            }

            //cerrar la tabla
            echo "</table>";
          } else {
            echo "<strong>La búsqueda no devolvió ningún resultado</strong>";
          }


          //cerrar la consulta
          $stmt->close();
        } else {
          echo "ERROR: No se pudo preparar la consulta.";
        }
        break;

      case 'ano': //aparentemente funciona
        //consulta para buscar las películas por año
        $consultaAno = "SELECT  
                        peliculas.nombre AS nombre_pelicula, 
                        peliculas.genero, 
                        peliculas.ano, 
                        CONCAT(director.nombre, ' ', director.apellidos) AS nombre_director,
                        GROUP_CONCAT(CONCAT(actores.nombre, ' ', actores.apellidos) SEPARATOR ', ') AS nombres_actores
                    FROM 
                        peliculas
                    JOIN 
                        director ON peliculas.ID_director = director.ID_director
                    LEFT JOIN 
                        participa ON peliculas.ID_pelicula = participa.ID_pelicula
                    LEFT JOIN 
                        actores ON participa.ID_actor = actores.ID_actor
                    WHERE peliculas.ano LIKE ? /* sí, lo sé */
                    GROUP BY 
                        peliculas.ID_pelicula;";

        //preparar la consulta
        if ($stmt = $db->prepare($consultaAno)) { //cuanto más lo escribo es peor
          //blindar el parámetro
          $param = "%" . $ano . "%";
          $stmt->bind_param("s", $param);

          //ejecutar la consulta
          $stmt->execute();

          //obtener el resultado
          $resultado = $stmt->get_result();

          //comprobar que hay al menos 1 resultado
          if ($resultado->num_rows > 0) {
            //abrir la tabla
            echo "<table>";
            echo "<tr><th>Película</th><th>Género</th><th>Año buscado</th><th>Actores</th><th>Director</th></tr>";
            //condición mientras que haya resultados
            while ($fila = $resultado->fetch_assoc()) {
              //añadir los datos a la tabla
              echo "<tr>";
              echo "<td>" . $fila['nombre_pelicula'] . "</td>";
              echo "<td>" . $fila['genero'] . "</td>";
              echo "<td>" . $fila['ano'] . "</td>";
              echo "<td>" . $fila['nombres_actores'] . "</td>"; // Lista de actores
              echo "<td>" . $fila['nombre_director'] . "</td>";
              echo "</tr>";
            }

            //cerrar la tabla
            echo "</table>";
          } else {
            echo "<strong>La búsqueda no devolvió ningún resultado</strong>";
          }


          //cerrar la consulta
          $stmt->close();
        } else {
          echo "ERROR: No se pudo preparar la consulta.";
        }
        break;

      case 'actores': //aparentemente funciona
        // Consulta para buscar las películas por actor
        $consultaActor = "SELECT  
                              peliculas.nombre AS nombre_pelicula, 
                              peliculas.genero, 
                              peliculas.ano, 
                              CONCAT(director.nombre, ' ', director.apellidos) AS nombre_director,
                              GROUP_CONCAT(CONCAT(actores.nombre, ' ', actores.apellidos) SEPARATOR ', ') AS nombres_actores
                            FROM 
                              peliculas
                            JOIN 
                              director ON peliculas.ID_director = director.ID_director
                            LEFT JOIN 
                              participa ON peliculas.ID_pelicula = participa.ID_pelicula
                            LEFT JOIN 
                              actores ON participa.ID_actor = actores.ID_actor
                            WHERE CONCAT(actores.nombre, ' ', actores.apellidos) LIKE ?
                            GROUP BY 
                              peliculas.ID_pelicula;";

        // Preparar la consulta
        if ($stmt = $db->prepare($consultaActor)) {
          // Blindar el parámetro
          $param = "%" . $actor . "%";
          $stmt->bind_param("s", $param);

          // Ejecutar la consulta
          $stmt->execute();

          // Obtener el resultado
          $resultado = $stmt->get_result();

          // Comprobar que hay al menos 1 resultado
          if ($resultado->num_rows > 0) {
            // Abrir la tabla
            echo "<table>";
            echo "<tr><th>Película</th><th>Género</th><th>Año</th><th>Director</th><th>Actor buscado</th></tr>";

            // Iterar por los resultados
            while ($fila = $resultado->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($fila['nombre_pelicula']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['genero']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['ano']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['nombre_director']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['nombres_actores']) . "</td>";
              echo "</tr>";
            }

            // Cerrar la tabla
            echo "</table>";
          } else {
            echo "<strong>No se encontraron películas para el actor seleccionado.</strong>";
          }

          // Cerrar la consulta
          $stmt->close();
        } else {
          echo "ERROR: No se pudo preparar la consulta. " . $db->error;
        }
        break;



      case 'directores': //aparentemente funciona
        //consulta para buscar las películas por director
        $consultaDirector = "SELECT  
        peliculas.nombre AS nombre_pelicula, 
        peliculas.genero, 
        peliculas.ano, 
        CONCAT(director.nombre, ' ', director.apellidos) AS nombre_director,
        GROUP_CONCAT(CONCAT(actores.nombre, ' ', actores.apellidos) SEPARATOR ', ') AS nombres_actores
      FROM 
        peliculas
      JOIN 
        director ON peliculas.ID_director = director.ID_director
      LEFT JOIN 
        participa ON peliculas.ID_pelicula = participa.ID_pelicula
      LEFT JOIN 
        actores ON participa.ID_actor = actores.ID_actor
      WHERE CONCAT(director.nombre, ' ', director.apellidos) LIKE ?
      GROUP BY 
        peliculas.ID_pelicula;";

        // Preparar la consulta
        if ($stmt = $db->prepare($consultaDirector)) {
          // Blindar el parámetro
          $param = "%" . $director . "%";
          $stmt->bind_param("s", $param);

          // Ejecutar la consulta
          $stmt->execute();

          // Obtener el resultado
          $resultado = $stmt->get_result();

          // Comprobar que hay al menos 1 resultado
          if ($resultado->num_rows > 0) {
            // Abrir la tabla
            echo "<table>";
            echo "<tr><th>Película</th><th>Género</th><th>Año</th><th>Director buscado</th><th>Actores</th></tr>";

            // Iterar por los resultados
            while ($fila = $resultado->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($fila['nombre_pelicula']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['genero']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['ano']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['nombre_director']) . "</td>";
              echo "<td>" . htmlspecialchars($fila['nombres_actores']) . "</td>";
              echo "</tr>";
            }

            // Cerrar la tabla
            echo "</table>";
          } else {
            echo "<strong>No se encontraron películas para el director seleccionado.</strong>";
          }

          // Cerrar la consulta
          $stmt->close();
        } else {
          echo "ERROR: No se pudo preparar la consulta. " . $db->error;
        }
        break;

      default:
        echo "Tipo de búsqueda no reconocido: " . htmlspecialchars($tipo_busqueda, ENT_QUOTES, 'UTF-8');
        break;
    }
  }
  ?>

  <!-- Ruta con el código javascript que ejecuta la página -->
  <script src="./js/app.js"></script>

  <hr/>

  <main>
    <!-- Generar tablas con las películas -->

    <h2>Todas las películas</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Género</th>
        <th>Año</th>
        <th>Actores</th>
        <th>Director</th>
      </tr>

      <?php
      //consulta para obtener los valores de las películas
      $sqlPelis = "
                    SELECT 
                        peliculas.ID_pelicula, 
                        peliculas.nombre AS nombre_pelicula, 
                        peliculas.genero, 
                        peliculas.ano, 
                        CONCAT(director.nombre, ' ', director.apellidos) AS nombre_director,
                        GROUP_CONCAT(CONCAT(actores.nombre, ' ', actores.apellidos) SEPARATOR ', ') AS nombres_actores
                    FROM 
                        peliculas
                    JOIN 
                        director ON peliculas.ID_director = director.ID_director
                    LEFT JOIN 
                        participa ON peliculas.ID_pelicula = participa.ID_pelicula
                    LEFT JOIN 
                        actores ON participa.ID_actor = actores.ID_actor
                    GROUP BY 
                        peliculas.ID_pelicula;
                    ";



      //resultado de la consulta
      $resultadoPelis = $db->query($sqlPelis);

      //comprobar que hay al menos 1 resultado
      if ($resultadoPelis->num_rows > 0) {
        while ($fila = $resultadoPelis->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $fila['ID_pelicula'] . "</td>";
          echo "<td>" . $fila['nombre_pelicula'] . "</td>";
          echo "<td>" . $fila['genero'] . "</td>";
          echo "<td>" . $fila['ano'] . "</td>";
          echo "<td>" . $fila['nombres_actores'] . "</td>"; // Lista de actores
          echo "<td>" . $fila['nombre_director'] . "</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='6'>No hay películas añadidas a la base de datos</td></tr>";
      }

      ?>
    </table>
  </main>

</body>

</html>
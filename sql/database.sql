/*Crear la base de datos si no existe */
CREATE DATABASE IF NOT EXISTS `videoclub`;

/* Usar la base de datos */
USE `videoclub`;

/*Crear la tabla director */
CREATE TABLE `director`(
    ID_director INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    apellidos VARCHAR(200),
    fecha_nacimiento DATE
);

/*Crear la tabla películas */
CREATE TABLE `peliculas`(
    ID_pelicula INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR (200),
    genero VARCHAR (100),
    ano INT,
    ID_director INT,
    FOREIGN KEY (ID_director) REFERENCES director(ID_director)
);

/*Crear la tabla actores */
CREATE TABLE `actores`(
    ID_actor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR (100),
    apellidos VARCHAR (200),
    nacionalidad VARCHAR (100),
    fecha_nacimiento DATE
);

/*Crear tabla "participa", salida de la relación N a N de películas y actor */
CREATE TABLE `participa`(
    ID_pelicula INT,
    ID_actor INT,
    PRIMARY KEY (ID_pelicula, ID_actor),
    FOREIGN KEY (ID_pelicula) REFERENCES peliculas(ID_pelicula),
    FOREIGN KEY (ID_actor) REFERENCES actores(ID_actor)
);

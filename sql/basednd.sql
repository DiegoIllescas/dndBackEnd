DROP DATABASE IF EXISTS basednd;

CREATE DATABASE basednd DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE basednd;

CREATE TABLE Usuario (
    id_usuario int unsigned not null primary key auto_increment,
    correo varchar(50) unique not null,
    nombre varchar(50),
    foto_url varchar(150),
    descripcion text,
    estado int(2),
    clave varchar(256)
);

CREATE TABLE Personaje (
    id_personaje int unsigned not null primary key auto_increment,
    id_usuario int unsigned not null,
    nombre varchar(100),
    raza varchar(50),
    subraza varchar(50),
    clase varchar(50),
    alineacion varchar(50),
    historia text,
    ideales text,
    edad int(100),
    altura int(100),
    peso int(100),
    color_ojos varchar(20),
    color_piel varchar(20),
    color_pelo varchar(20),
    traits text,
    bonds text,
    flaws text,
    fuerza int(100),
    destreza int(100),
    constitucion int(100),
    inteligencia int(100),
    sabiduria int(100),
    carisma int(100),
    foreign key (id_usuario) references Usuario(id_usuario) ON DELETE CASCADE
);


CREATE TABLE Travesia (
    id_travesia int unsigned not null primary key auto_increment,
    id_usuario int unsigned not null,
    nombre varchar(100),
    fecha_creacion date,
    hora_creacion time,
    foreign key (id_usuario) references Usuario(id_usuario) ON DELETE CASCADE
);

CREATE TABLE PersonajesPorTravesia (
    id_travesia int unsigned not null,
    id_personaje int unsigned not null,
    estado varchar(30),
    foreign key (id_travesia) references Travesia(id_travesia) ON DELETE CASCADE,
    foreign key (id_personaje) references Personaje(id_personaje) ON DELETE CASCADE
);

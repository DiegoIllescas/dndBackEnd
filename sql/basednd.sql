DROP DATABASE IF EXISTS basednd;

CREATE DATABASE basednd DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE basednd;

CREATE TABLE Usuario {
    id_usuario int unsigned not null primary key auto_increment,
    correo nvarchar(50) unique not null,
    nombre nvarchar(50) unique,
    clave nvarchar(256)
};

CREATE TABLE Personaje {
    id_personaje int unsigned not null primary key auto_increment,
    id_usuario int unsigned not null,
    nombre nvarchar(100),
    raza nvarchar(50),
    subraza nvarchar(50),
    clase nvarchar(50),
    subraza nvarchar(50),
    alineacion nvarchar(50),
    historia text,
    ideales text,
    edad int(100)
    altura int(100),
    peso int(100),
    color_ojos nvarchar(20),
    color_piel nvarchar(20),
    color_pelo nvarchar(20),
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
}

--Travesia es campaña pero para no tener que usar la ñ lo dejo como travesia
CREATE TABLE travesia {
    id_travesia int unsigned not null primary key auto_increment,
    id_usuario int unsigned not null,
    nombre nvarchar(100),
    fecha_creacion date,
    hora_creacion time,
    foreign key (id_usuario) references Usuario(id_usuario) ON DELETE CASCADE
}

CREATE TABLE PersonajesPorTravesia {
    id_travesia int unsigned not null,
    id_personaje int unsigned not null,
    estado nvarchar(30),
    foreign key (id_travesia) references Travesia(id_travesia) ON DELETE CASCADE,
    foreign key (id_personaje) references Personaje(id_personaje) ON DELETE CASCADE
}

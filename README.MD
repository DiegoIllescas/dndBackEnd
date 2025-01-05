# Backend para aplicacion web DnD

## Requisitos
Tener un servidor de Apache instalado ademas de mySQL, ya sea por XAMPP,WAMPP,LAMPP.... \
Ademas del controlador de dependencias de PHP: Composer \

Composer: \
`https://getcomposer.org/download/` Para distribuciones Linux \
`https://getcomposer.org/doc/00-intro.md#installation-windows` Para Windows \

## Pasos de instalacion

Copiar unicamente esta carpeta `/Back` en la carpeta de htdocs de XAMPP \

Migrar la base de datos `basednd.sql` que se encuentra dentro de la carpeta `sql`. Para hacerlo con XAMPP, inicia los procesos de APache y mySQL desde su interfaz, luego dirigete a un navegador y entra en `https://localhost/phpmyadmin/index.php` y selecciona la opcion `[Importar]` y selecciona el archivo antes mencionado. \

Dentro de `config.php` modifica el puerto y contraseña que hayas especificado al instalar XAMPP. \

## Pasos finales

Ya tienes el servidor casi completo, finalmente ejecuta dentro de esta carpeta desde una terminal el comando: \
`composer install` y espera a que las dependencias se instalen. \

Finalmente ya tienes el servidor corriendo \

Para hacerle peticiones unicamente en React realiza un fetch() a la direccion siguiente: \
`localhost/Back/archivo.php` siendo archivo los endpoints del sistema que se organizan de la siguiente manera: \

### Endpoints

/user.php \

Acepta peticiones POST y DELETE para registrar usuarios o eliminarlos(soft delete). \
Para la peticion DELETE debes incluir a la peticion el header `Authorization` con el contenido `Bearer token` donde el token lo obtienes al iniciar sesion. \

/auth.php \

Unicamente acepta peticion POST y es el responsable del Login y retorna el token necesario para las demas funciones \

/user/profile.php \

Acepta peticiones GET y devuelve la foto en base64, el nombre de usuario y la descripcion del mismo. \
Requiere del token en los headers al igual que en DELETE de user.php \

/user/profile.php y /user/description.php \

Aceptan peticiones PUT para actualizar estos datos del usuario. \
Requieren del token \

#### En desarrollo

/personajes \

Deberia aceptar peticion POST y GET para crear personajes y consultarlos \
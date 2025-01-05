<?php
    include "config.php";
    include "utils.php";
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Content-Type: application/json; charset=utf-8");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 3600'); 

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }

    //Validar si la db esta en linea
    $dbConn = connect($db);
    if(!$dbConn) {
        header("HTTP/1.1 503 Service Unavailable");
        echo json_encode(['success' => false, 'error' => 'Servicio no disponible']);
        exit();
    }

    //GET para mostrar perfil
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "SELECT nombre, correo, foto_url, descripcion FROM Usuario WHERE id_usuario = ?";
        return;
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    //POST para registrar al usuario
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(!isset($data['correo'], $data['clave'], $data['nombre'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['success' => false, 'message' => 'Parametros faltantes']);
            return;
        }

        //Comprobar si el usuario ya existe tanto en correo como nombre de usuario
        $query = "SELECT * FROM Usuario WHERE correo = ?";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(1, $data['correo']);
        $stmt->execute();

        if($stmt->rowCount() === 0) {
            //No existe el correo en el sistema
            $query = "INSERT INTO Usuario (correo, nombre, clave, estado, foto_url) VALUE (?, ?, ?, ?, ?)";
            $stmt = $dbConn->prepare($query);
            $stmt->bindValue(1, $data['correo']);
            $stmt->bindValue(2, $data['nombre']);
            $stmt->bindValue(3, password_hash($data['clave'], PASSWORD_DEFAULT));
            $stmt->bindValue(4, 1);
            $stmt->bindValue(5, "./pictures/default.jpg");
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Cuenta creada con exito']);
        }else {
            echo json_encode(['success' => false, 'message' => 'Ya existe una cuenta vinculada al correo ingresado']);
        }
    }
    //DELETE para borrar cuenta (softdelete)

    //PATCH para actualizar datos como clave o foto
?>
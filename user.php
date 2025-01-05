<?php
    include "config.php";
    include "utils.php";
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Content-Type: application/json; charset=utf-8");
    header('Access-Control-Allow-Methods: POST, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 3600'); 

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        return;
    }

    //Validar si la db esta en linea
    $dbConn = connect($db);
    if(!$dbConn) {
        header("HTTP/1.1 503 Service Unavailable");
        echo json_encode(['success' => false, 'message' => 'Servicio no disponible']);
        return;
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if(!$data) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['success' => false, 'message' => 'Falta el JSON']);
        return;
    }
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

        if($stmt->rowCount() > 0) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['success' => false, 'message' => 'Ya existe una cuenta vinculada al correo ingresado']);
            return;
        }
        
        $query = "INSERT INTO Usuario (correo, nombre, clave, estado, foto_url) VALUE (?, ?, ?, ?, ?)";
        $stmt = $dbConn->prepare($query);
        $stmt->bindValue(1, $data['correo']);
        $stmt->bindValue(2, $data['nombre']);
        $stmt->bindValue(3, password_hash($data['clave'], PASSWORD_DEFAULT));
        $stmt->bindValue(4, 1);
        $stmt->bindValue(5, "./pictures/default.jpg");
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Cuenta creada con exito']);
        return;
    }

    $headers = apache_request_headers();
    $isAuth = isAuth($headers, $keypass);

    if($isAuth['status'] == 432) {
        header("HTTP/1.1 308 Session Expired");
        echo json_encode(['success' => false, 'message' => 'Sesion expirada']);
        return;
    }

    //Error si no incluye el Token de Autenticacion
    if($isAuth['status'] == 401) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(['success' => false, 'message' => 'No estas logueado']);
        return;
    }
    
    $userData = $isAuth['payload'];


    //DELETE para borrar cuenta (softdelete)
    if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        if(!isset($data['correo'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['success' => false, 'message' => 'Parametros faltantes']);
            return; 
        }

        $query = "UPDATE Usuario SET estado = :estado WHERE correo = :correo";
        $stmt = $dbConn->prepare($query);
        $stmt->bindValue(":estado", 0);
        $stmt->bindValue(":correo", $data['correo']);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Cuenta borrada con exito']);
        return;
    }

?>
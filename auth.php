<?php
    include "config.php";
    include "utils.php";
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Content-Type: application/json; charset=utf-8");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 3600'); // 1 hour cache

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    //Error cuando no mandan un json bien formado
    if(!$data) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['success' => false, 'error' => 'Falta el JSON']);
        exit();
    }

    $dbConn = connect($db);
    if(!$dbConn) {
        header("HTTP/1.1 503 Service Unavailable");
        echo json_encode(['success' => false, 'error' => 'Servicio no disponible']);
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(!isset($data['correo'], $data['clave'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['success' => false, 'message' => 'Parametros faltantes']);
            return;
        }

        $query = "SELECT clave, id_usuario FROM Usuario WHERE correo = ? AND estado = 1";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(1, $data['correo']);
        $stmt->execute();

        if($stmt->rowCount() === 0) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrecto']);
            return;
        }

        $res = $stmt->fetch();
        if(!password_verify($data['password'], $res['clave'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrecto']);
            return;
        }

        $payload = [
            'exp' => time() + 10800,
            'id_usuario' => $res['id_usuario']
        ];

        $token = genToken($payload, $keypass);
        echo json_encode(['success' => true, 'message' => 'Inicio de sesion exitoso', 'token' => $token]);
        return;
    }
?>
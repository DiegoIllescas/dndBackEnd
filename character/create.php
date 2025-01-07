<?php
    include "../config.php";
    include "../utils.php";

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Content-Type: application/json; charset=utf-8");
    header('Access-Control-Allow-Methods: POST');
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

    
?>
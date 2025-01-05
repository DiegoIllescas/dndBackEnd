<?php
    include "../config.php";
    include "../utils.php";

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Content-Type: application/json; charset=utf-8");
    header('Access-Control-Allow-Methods: GET');
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

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "SELECT nombre, foto_url, descripcion FROM Usuario WHERE id_usuario = ?";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(1, $userData['id_usuario']);
        $stmt->execute();

        $info = $stmt->fetch();

        $foto = file_get_contents($info['foto_url']);
        $response = [
            "nombre" => $info['nombre'],
            "foto" => $foto,
            "descripcion" => $info['descripcion']
        ];

        echo json_encode(['success' => true, 'message' => 'Datos encotrados', 'data' => $response]);
        return;
    }
?>
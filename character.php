<?php
    include "./config.php";
    include "./utils.php";

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Content-Type: application/json; charset=utf-8");
    header('Access-Control-Allow-Methods: POST, GET, PUT');
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
        if(!isset($_GET['id_personaje'])) {
            $query = "SELECT * FROM Personaje WHERE id_usuario = ?";
            $stmt = $dbConn->prepare($query);
            $stmt->bindParam(1, $userData['id_usuario']);
            $stmt->execute();

            $res = $stmt->fetchAll();
            echo json_encode(['success' => true, 'message' => 'Personaje encontrado', 'data' => $res]);
            return;
        }
        $id = $_GET['id_personaje'];
        $query = "SELECT * FROM Personaje WHERE id_usuario = ? AND id_personaje = ?";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(1, $userData['id_usuario']);
        $stmt->bindParam(2, $id);
        $stmt->execute();

        if($stmt->rowCount() == 0) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(['success' => false, 'message' => 'El personaje no existe']);
            return;
        }

        $res = $stmt->fetch();

        echo json_encode(['success' => true, 'message' => 'Personaje encontrado', 'data' => $res]);
        return;
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if(!$data) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['success' => false, 'message' => 'Falta el JSON']);
        return;
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(!isset($data['nombre'], $data['raza'], $data['clase'], $data['alineacion'], $data['historia'], $data['ideales'], $data['edad'], $data['altura'], $data['peso'], $data['color_ojos'], $data['color_piel'], $data['color_pelo'], $data['traits'], $data['bonds'], $data['flaws'], $data['fuerza'], $data['destreza'], $data['constitucion'], $data['inteligencia'], $data['sabiduria'], $data['carisma'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['success' => false, 'message' => 'Parametros faltantes']);
            return; 
        }

        $query = "INSERT INTO Personaje (id_usuario, nombre, raza, clase, alineacion, historia, ideales, edad, altura, peso, color_ojos, color_piel, color_pelo, traits, bonds, flaws, fuerza, destreza, constitucion, inteligencia, sabiduria, carisma) VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dbConn->prepare($query);
        $stmt->bindValue(1, $userData['id_usuario']);
        $stmt->bindValue(2, $data['nombre']);
        $stmt->bindValue(3, $data['raza']);
        $stmt->bindValue(4, $data['clase']);
        $stmt->bindValue(5, $data['alineacion']);
        $stmt->bindValue(6, $data['historia']);
        $stmt->bindValue(7, $data['ideales']);
        $stmt->bindValue(8, $data['edad']);
        $stmt->bindValue(9, $data['altura']);
        $stmt->bindValue(10, $data['peso']);
        $stmt->bindValue(11, $data['color_ojos']);
        $stmt->bindValue(12, $data['color_piel']);
        $stmt->bindValue(13, $data['color_pelo']);
        $stmt->bindValue(14, $data['traits']);
        $stmt->bindValue(15, $data['bonds']);
        $stmt->bindValue(16, $data['flaws']);
        $stmt->bindValue(17, $data['fuerza']);
        $stmt->bindValue(18, $data['destreza']);
        $stmt->bindValue(19, $data['constitucion']);
        $stmt->bindValue(20, $data['inteligencia']);
        $stmt->bindValue(21, $data['sabiduria']);
        $stmt->bindValue(22, $data['carisma']);

        $stmt->execute();

        $id = $dbConn->lastInsertId();

        echo json_encode(['success' => true, 'message' => 'Personaje creado con exito', 'id_personaje' => $id]);
        return;
    }

    
?>
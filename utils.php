<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    require 'vendor/autoload.php';

    function connect($db) {
        try {
            $dsn = "mysql:host={$db['host']};dbname={$db['db']};charset=UTF8;port={$db['port']}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ];
            $conn = new PDO($dsn, $db['user'], $db['pass'], $options);
            return $conn;
        } catch (PDOException $e) {
            return null;
        }
    }

    function getToken($header) {
        $auth = explode(' ', $header);
        return $auth[1];
    } 

    function isAuth($headers, $key) {
        if (array_key_exists('Authorization', $headers)) {
            $token = getToken($headers['Authorization']);
            try {
                $payload = (array) JWT::decode($token, new Key($key, 'HS256'));
                return ['status' => 200, 'payload' => $payload];
            } catch (\Throwable $th) {
                return ['status' => 432];
            }
        } else {
            return ['status' => 401];
        }
    }

    function genToken($payload, $key) {
        return JWT::encode($payload,$key, 'HS256');
    }
?>
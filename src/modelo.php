<?php
function logError($err){
    $fp = fopen("./errores.log","a");
    fwrite($fp, date("Y-m-d H:i") . " "); // Añadir la fecha y hora antes del mensaje de error
    fwrite($fp, $err);
    fwrite($fp, "\n");
    fclose($fp);
}

function openConn() {
    $dbname = "pintores";
    $host = "localhost";
    $user = "gaspi";
    $password = "1234";
    $resState = "";
    $dbh = null;

    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
        $dbh = new PDO($dsn, $user, $password); 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $resState .= "\nConexion exitosa";
    } catch (PDOException $e) {
        $resState .= "\n" . $e->getMessage();
        logError($resState); // Logea el error
    }

    if ($dbh) {
        return $dbh;
    } else {
        echo json_encode(['error' => 'Conexion a la Base de Datos fallida: ' . $resState]);
    }
}

function runFilterQuery($query, $params) {
    $dbConn = openConn();
    if ($dbConn) {
        try {
            $stmt = $dbConn->prepare($query);

            if($params['id']){
                $stmt->bindParam(':id', $params['id']);
            }
            if($params['nombre']){
                $stmt->bindParam(':nombre', $params['nombre']);
            }
            if($params['epoca']){
                $stmt->bindParam(':epoca', $params['epoca']);
            }
            if($params['estilo']){
                $stmt->bindParam(':estilo', $params['estilo']);
            }

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            return $stmt->fetchAll(); 
        } catch (PDOException $e) {
            logError("Error ejecutando consulta: " . $e->getMessage());
            echo json_encode(['error' => 'Error ejecutando consulta']);
        } finally {
            $dbConn = null;
        }
    } else {
        echo json_encode(['error' => 'Conexion a la Base de Datos fallida']);
    }
}

function readPintores($params) {
    $query = "SELECT * FROM pintores ";

    $whereClauses = [];
    $bindParams = [];

    if (!empty($params['id'])) {
        $whereClauses[] = "id LIKE CONCAT('%', :id, '%')";
        $bindParams[':id'] = $params['id'];
    }
    if (!empty($params['nombre'])) {
        $whereClauses[] = "nombre LIKE CONCAT('%', :nombre, '%')";
        $bindParams[':nombre'] = $params['nombre'];
    }
    if (!empty($params['epoca'])) {
        $whereClauses[] = "epoca LIKE CONCAT('%', :epoca, '%')";
        $bindParams[':epoca'] = $params['epoca'];
    }
    if (!empty($params['estilo'])) {
        $whereClauses[] = "estilo LIKE CONCAT('%', :estilo, '%')";
        $bindParams[':estilo'] = $params['estilo'];
    }

    if (!empty($whereClauses)) {
        $query .= "WHERE " . implode(" AND ", $whereClauses) . " ";
    }

    if (!empty($params['order'])) {
        $order = $params['order'];
        if ($order == 'Estilo') {
            $query .= "ORDER BY estilo";
        } else {
            $query .= "ORDER BY $order";
        }
    }

    $rawData = runQuery($query, $bindParams);

    $res = [];
    if ($rawData) {
        foreach ($rawData as $row) {
            $res[] = makeObj($row);
        }
    }

    echo json_encode($res);
}

function runQuery($query, $bindParams = []) {
    $dbConn = openConn();
    if ($dbConn) {
        try {
            $stmt = $dbConn->prepare($query);

            foreach ($bindParams as $key => $value) {
                if (isset($bindParams[$key])) {
                    $stmt->bindValue($key, $value);
                }
            }
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $stmt->execute();

            if (stripos($query, 'SELECT') === 0) {
                return $stmt->fetchAll();
            } else {
                return true;
            }

        } catch (PDOException $e) {
            logError("Error ejecutando consulta: " . $e->getMessage());
            return false;
        } finally {
            $dbConn = null;
        }
    } else {
        echo json_encode(['error' => 'Conexión a la Base de Datos fallida']);
        return false;
    }
}

function readEstilos() {
    $rawData = runQuery("SELECT * FROM estilos");
    $res = [];

    if ($rawData) {
        foreach ($rawData as $row) {
            $resObj = new stdClass();
            $resObj->idEstilo = $row['idEstilo'];
            $resObj->estilo = $row['estilo'];

            array_push($res, $resObj);
        }
    }

    $JSONData = json_encode($res);
    echo $JSONData;
}

function deletePintor($params){
    $query = "DELETE FROM pintores WHERE id = :id";

    $bindParams = [':id' => $params['id']];
    
    $result = runQuery($query, $bindParams);
    
    if ($result !== false) {
        echo json_encode(['success' => 'Pintor eliminado']);
    } else {
        echo json_encode(['error' => 'Error eliminando pintor']);
    }
}

function updatePintor($params){
    $query = "UPDATE pintores SET nombre = :nombre, salario = :salario, epoca = :epoca, estilo = :estilo WHERE id = :id";
    $bindParams = [
        ':id' => $params['id'],
        ':nombre' => $params['nombre'],
        ':salario' => $params['salario'],
        ':epoca' => $params['epoca'],
        ':estilo' => $params['estilo']
    ];
    
    $result = runQuery($query, $bindParams);
    
    if ($result !== false) {
        echo json_encode(['success' => 'Pintor actualizado']);
    } else {
        echo json_encode(['error' => 'Error actualizando pintor']);
    }
}

function createPintor($params) {
    $query = "INSERT INTO pintores (nombre, salario, epoca, estilo) VALUES (:nombre, :salario, :epoca, :estilo)";

    $bindParams = [
        ':nombre' => $params['nombre'],
        ':salario' => $params['salario'],
        ':epoca' => $params['epoca'],
        ':estilo' => $params['estilo'],
    ];

    $result = runQuery($query, $bindParams);

    if ($result !== false) {
        echo json_encode(['success' => 'Pintor creado']);
    } else {
        echo json_encode(['error' => 'Error creando pintor']);
    }
}
function makeObj($params){
    $resObj = new stdClass();
    $resObj->id = $params['id'];
    $resObj->nombre = $params['nombre'];
    $resObj->salario = $params['salario']; // Asegúrate de que 'salario' esté en tu consulta
    $resObj->epoca = $params['epoca'];
    $resObj->estilo = $params['estilo'];
    return $resObj;
}

?>

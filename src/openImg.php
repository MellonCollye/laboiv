<?php
$dbname = "pintores";
$host = "localhost";
$user = "gaspi";
$password = "1234";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    
    $archivoId = $_POST['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM pintores WHERE id = :id");
    $stmt->bindParam(':id', $archivoId);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $contenido = $row['cuadro'];

            // Devolver los datos binarios base64 directamente
            header("Content-type: text/plain");
            echo base64_encode($contenido);
        } else {
            echo 'No se encontró ningún archivo con el ID especificado.';
        }
    } else {
        echo 'Error al ejecutar la consulta.';
    }
} catch (PDOException $e) {
    echo 'Error en la base de datos: ' . $e->getMessage();
}
?>
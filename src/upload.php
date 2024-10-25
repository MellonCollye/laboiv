<?php
if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileContent = file_get_contents($fileTmpPath);

    $id = $_POST['idPdf'];
    $dbname = "pintores";
    $host = "localhost";
    $user = "gaspi";
    $password = "1234";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $stmt = $pdo->prepare("UPDATE pintores SET cuadro = :img WHERE id = :id");
        $stmt->bindParam(':img', $fileContent, PDO::PARAM_LOB);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error en la subida del archivo']);
}
?>
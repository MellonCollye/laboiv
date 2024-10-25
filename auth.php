<?php

function auth($name, $password){
    $dbname = "pintores";
    $host = "localhost";
    $dbuser = "gaspi";
    $dbpassword = "1234";
    try {
     
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpassword);
    
        $hashed_password = hash('sha256', $password); 
    
        $sql = "SELECT * FROM users WHERE login = :name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
           
            if ($hashed_password === $user['password']) {
                
                $updateQuery = "UPDATE users SET sessionCount = :sessionCount WHERE login =:name";
                $stmt = $pdo->prepare($updateQuery);

                $_SESSION['name'] = $name;
                $stmt->bindParam(':name', $name);

                $sessionCount = $user['sessionCount'] + 1 ;
                $stmt->bindParam(':sessionCount', $sessionCount);
                
                $stmt->execute();

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo $e;
    }
}

function getSessionCount($name){
    $dbname = "pintores";
    $host = "localhost";
    $dbuser = "gaspi";
    $dbpassword = "1234";
    
    try{
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpassword);
        $sql = "SELECT sessionCount FROM users WHERE login = :name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $sessionCount = $stmt->fetch(PDO::FETCH_ASSOC);
        return $sessionCount;
    }
    catch (PDOException $e) {
        return false;
    }
}
?> 
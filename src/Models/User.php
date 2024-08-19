<?php

namespace App\Models;

use PDO;

class User
{
    private $pdo;

    public function __construct($db, $connectionName = 'default')
    {
        $this->pdo = $db->getConnection($connectionName);
    }
    
    // Buscar un usuario por nombre de usuario
    public function findByUsername($username)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Verificar la contraseña del usuario
    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }
}

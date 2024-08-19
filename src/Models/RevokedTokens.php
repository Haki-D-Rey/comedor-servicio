<?php

namespace App\Models;

use PDO;

class RevokedTokens
{
    private $pdo;

    public function __construct($db, $connectionName = 'default')
    {
        $this->pdo = $db->getConnection($connectionName);
    }

    // Añadir un token a la lista negra
    public function addToken($token)
    {
        $stmt = $this->pdo->prepare("INSERT INTO revoked_tokens (token) VALUES (:token)");
        $stmt->execute(['token' => $token]);
    }

    // Verificar si un token está en la lista negra
    public function isTokenRevoked($token)
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM revoked_tokens WHERE token = :token");
        $stmt->execute(['token' => $token]);
        return $stmt->fetchColumn() !== false;
    }
}

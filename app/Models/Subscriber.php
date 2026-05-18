<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Subscriber
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM subscribers WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        $subscriber = $stmt->fetch();

        return $subscriber ?: null;
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM subscribers WHERE unsubscribe_token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);

        $subscriber = $stmt->fetch();

        return $subscriber ?: null;
    }

    public function create(string $email, string $token): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO subscribers (email, status, unsubscribe_token)
            VALUES (:email, 'active', :token)
        ");

        return $stmt->execute([
            'email' => $email,
            'token' => $token
        ]);
    }

    public function reactivate(string $email, string $token): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE subscribers
            SET status = 'active',
                unsubscribe_token = :token,
                unsubscribed_at = NULL
            WHERE email = :email
        ");

        return $stmt->execute([
            'email' => $email,
            'token' => $token
        ]);
    }

    public function unsubscribeByToken(string $token): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE subscribers
            SET status = 'unsubscribed',
                unsubscribed_at = NOW()
            WHERE unsubscribe_token = :token
        ");

        return $stmt->execute(['token' => $token]);
    }
}

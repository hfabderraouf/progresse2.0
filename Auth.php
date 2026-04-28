<?php

class Auth
{
    private $conn;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function user()
    {
        $id = $_SESSION['user_id'] ?? null;

        if (!$id) return null;

        $type = $_SESSION['user_type'];

        $r = $this->conn->execute_query("SELECT * FROM {$type}s WHERE id = ?", [$id]);
        $user = $r->fetch_assoc();

        if ($user) $user['type'] = $type;

        return $user;
    }

    function exigerRole(string $role)
    {
        $user = $this->user();

        if (!$user || $user['type'] !== $role) {
            header("Location: /login.php?t={$role}");
            die();
        }
        if ($user['type'] !== 'admin') {
            $this->conn->execute_query(
                "UPDATE {$user['type']}s SET last_active = NOW() WHERE id = ?",
                [$user['id']]
            );
        }
    }

    public function login(string $type, string $username, string $password)
    {
        $r = $this->conn->execute_query("SELECT * FROM {$type}s WHERE username = ?", [$username]);
        $user = $r->fetch_assoc();

        if (!$user) return null;

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $type;

            return $user;
        }

        return null;
    }

    public function logout()
    {
        session_destroy();
        header("Location: /");
        die();
    }
}

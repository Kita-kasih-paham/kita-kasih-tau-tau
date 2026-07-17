<?php

namespace Models;

use Core\Model;

class UserModel extends Model
{
    protected string $table = 'users';

    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function isUsernameTaken(string $username, int $excludeId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $excludeId]);
        return (bool) $stmt->fetch();
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE role = ?");
        $stmt->execute([$role]);
        $result = $stmt->fetch();
        return (int) $result['count'];
    }
}

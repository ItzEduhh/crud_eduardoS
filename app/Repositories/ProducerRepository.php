<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Producer;
use PDO;

class ProducerRepository
{
    public function countAll(): int
    {
        $stmt = Database::getConnection()->query("SELECT COUNT(*) FROM producers");
        return (int)$stmt->fetchColumn();
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = Database::getConnection()->prepare("SELECT * FROM producers ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM producers WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(Producer $producer): int
    {
        $stmt = Database::getConnection()->prepare("INSERT INTO producers (name, text) VALUES (?, ?)");
        $stmt->execute([$producer->name, $producer->text]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function update(Producer $producer): bool
    {
        $stmt = Database::getConnection()->prepare("UPDATE producers SET name = ?, text = ? WHERE id = ?");
        return $stmt->execute([$producer->name, $producer->text, $producer->id]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM producers WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findAll(): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM producers ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

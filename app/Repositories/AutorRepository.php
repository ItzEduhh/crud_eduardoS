<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Autor;
use PDO;

class AutorRepository
{
    public function countAll(): int
    {
        $stmt = Database::getConnection()->query("SELECT COUNT(*) FROM autors");
        return (int)$stmt->fetchColumn();
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = Database::getConnection()->prepare("SELECT * FROM autors ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM autors WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(Autor $autor): int
    {
        $stmt = Database::getConnection()->prepare("INSERT INTO autors (name, text) VALUES (?, ?)");
        $stmt->execute([$autor->name, $autor->text]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function update(Autor $autor): bool
    {
        $stmt = Database::getConnection()->prepare("UPDATE autors SET name = ?, text = ? WHERE id = ?");
        return $stmt->execute([$autor->name, $autor->text, $autor->id]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM autors WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findAll(): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM autors ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Music;
use PDO;

class MusicRepository
{
    public function countAll(): int
    {
        $stmt = Database::getConnection()->query("SELECT COUNT(*) FROM musics");
        return (int)$stmt->fetchColumn();
    }

    public function paginate(int $page, int $perPage): array
{
    $offset = ($page - 1) * $perPage;

    $sql = "
        SELECT 
            m.*, 
            a.name AS autor_name, 
            p.name AS producer_name
        FROM musics m
        LEFT JOIN autors a ON m.autor_id = a.id
        LEFT JOIN producers p ON m.producer_id = p.id
        ORDER BY m.id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = Database::getConnection()->prepare($sql);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function findById(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM musics WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(Music $music): int
    {
        $stmt = Database::getConnection()->prepare(
            "INSERT INTO musics (autor_id, producer_id, name, text) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$music->autor_id, $music->producer_id, $music->name, $music->text]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function update(Music $music): bool
    {
        $stmt = Database::getConnection()->prepare("UPDATE musics SET autor_id = ?, producer_id = ?, name = ?, text = ? WHERE id = ?");
        return $stmt->execute([$p->autor_id, $p->producer_id, $music->name, $music->text, $music->id]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM musics WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findByAutorId(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM products WHERE autor_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: [];
    }

    public function findByProducerId(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM products WHERE producer_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: [];
    }

    public function findAll(): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM musics ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

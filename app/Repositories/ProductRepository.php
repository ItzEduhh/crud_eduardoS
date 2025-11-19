<?php
namespace App\Repositories;

use App\Core\Database;
use App\Models\Product;
use PDO;

class ProductRepository {
    public function countAll(): int {
        $stmt = Database::getConnection()->query("SELECT COUNT(*) FROM products");
        return (int)$stmt->fetchColumn();
    }
    public function paginate(int $page, int $perPage): array {
    $offset = ($page - 1) * $perPage;

    $sql = "
        SELECT 
        p.*, 
        c.name AS category_name, 
        a.name AS autor_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN autors a ON p.autor_id = a.id
        ORDER BY p.id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = Database::getConnection()->prepare($sql);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find(int $id): ?array
    {
        $sql = "
            SELECT 
                p.*,
                c.name AS category_name,
                a.name AS autor_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN autors a ON a.id = p.autor_id
            WHERE p.id = ?
        ";

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(Product $p): int {
    $stmt = Database::getConnection()->prepare(
        "INSERT INTO products (autor_id, category_id, name, price, image_path) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([$p->autor_id, $p->category_id, $p->name, $p->price, $p->image_path]);
    return (int)Database::getConnection()->lastInsertId();
    }
    public function update(Product $p): bool {
        $stmt = Database::getConnection()->prepare("UPDATE products SET autor_id = ?, category_id = ?, name = ?, price = ?, image_path = ? WHERE id = ?");
        return $stmt->execute([$p->autor_id, $p->category_id, $p->name, $p->price, $p->image_path, $p->id]);
    }
    public function delete(int $id): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function findByCategoryId(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: [];
    }
    public function findByAutorId(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM products WHERE autor_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: [];
    }
}

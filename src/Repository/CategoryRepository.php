<?php

declare(strict_types=1);

class CategoryRepository
{
    private const REQUIRED_TABLES = ['categories', 'posts', 'post_category'];

    public function __construct(private PDO $pdo)
    {
        try {
            // Проверяем подключение к базе данных
            $this->pdo->query('SELECT 1')->fetch();

            // Проверяем существование таблиц
            $stmt = $this->pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $missingTables = array_diff(self::REQUIRED_TABLES, $tables);

            if (!empty($missingTables)) {
//                throw new RuntimeException('Отсутствуют необходимые таблицы: ' . implode(', ', $missingTables));
                self::createTables();
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Ошибка подключения к базе данных: ' . $e->getMessage());
        }
    }

    const POSTS_PER_CATEGORY = 3;
    const POSTS_PER_PAGE = 6;



    /**
     * @param int $limitPerCategory
     * @return array
     */
    public function getCategoriesWithRecentPosts(int $limitPerCategory = self::POSTS_PER_CATEGORY): array
    {
        $sql = "
            SELECT c.id, c.name, c.description,
                   p.id   AS post_id,
                   p.title AS post_title,
                   p.short_description,
                   p.image,
                   p.published_at
            FROM categories c
            JOIN post_category pc ON pc.category_id = c.id
            JOIN posts p ON p.id = pc.post_id
            WHERE p.published_at IS NOT NULL
            ORDER BY c.name, p.published_at DESC
        ";

        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $catId = (int)$row['id'];
            if (!isset($result[$catId])) {
                $result[$catId] = [
                    'id'          => $catId,
                    'name'        => $row['name'],
                    'description' => $row['description'],
                    'posts'       => [],
                ];
            }

            if (count($result[$catId]['posts']) < $limitPerCategory) {
                $result[$catId]['posts'][] = [
                    'id'                => (int)$row['post_id'],
                    'title'             => $row['post_title'],
                    'short_description' => $row['short_description'],
                    'image'             => $row['image'],
                    'published_at'      => $row['published_at'],
                ];
            }
        }

        return array_values($result);
    }


    /**
     * @param int $id
     * @return array|null
     */
    public function getCategoryById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, description FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function createTables() {
        $pdo = get_pdo();

        $pdo->exec('CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    image VARCHAR(255) NULL,
    short_description TEXT NULL,
    content TEXT NOT NULL,
    views INT NOT NULL DEFAULT 0,
    published_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS post_category (
    post_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (post_id, category_id),
    CONSTRAINT fk_pc_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_pc_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        header('Location: /');
        exit('перенаправляем');
    }
}

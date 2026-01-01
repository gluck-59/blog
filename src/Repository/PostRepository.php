<?php

declare(strict_types=1);

class PostRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @param int $categoryId
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getPostsByCategory(int $categoryId, string $sort, int $limit, int $offset): array
    {
        $sortColumn = $sort === 'views' ? 'p.views' : 'p.published_at';

        $sql = "
            SELECT p.*
            FROM posts p
            JOIN post_category pc ON pc.post_id = p.id
            WHERE pc.category_id = :category_id
            ORDER BY {$sortColumn} DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    /**
     * @param int $categoryId
     * @return int
     */
    public function countPostsByCategory(int $categoryId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM posts p JOIN post_category pc ON pc.post_id = p.id WHERE pc.category_id = :category_id');
        $stmt->execute(['category_id' => $categoryId]);

        return (int) $stmt->fetchColumn();
    }


    /**
     * @param int $id
     * @return array|null
     */
    public function getPostById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }



    /**
     * @param int $postId
     * @return array
     */
    public function getCategoriesByPostId(int $postId): array
    {
        $sql = 'SELECT c.* FROM categories c JOIN post_category pc ON pc.category_id = c.id WHERE pc.post_id = :post_id ORDER BY c.name';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll();
    }


    /**
     * @param int $id
     * @return void
     */
    public function incrementViews(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }


    /**
     * @param int $postId
     * @param int $limit
     * @return array
     */
    public function getSimilarPosts(int $postId, int $limit = 3): array
    {
        $sql = "
            SELECT DISTINCT p2.*
            FROM posts p1
            JOIN post_category pc1 ON pc1.post_id = p1.id
            JOIN post_category pc2 ON pc2.category_id = pc1.category_id
            JOIN posts p2 ON p2.id = pc2.post_id
            WHERE p1.id = :post_id AND p2.id <> :post_id
            ORDER BY p2.published_at DESC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

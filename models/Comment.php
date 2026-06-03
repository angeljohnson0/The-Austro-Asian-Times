<?php
class Comment
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM comments WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Approved comments shown to the public below an article.
    public function getApprovedByArticle(int $articleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM comments
             WHERE article_id = ? AND status = "approved"
             ORDER BY created_at ASC'
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll();
    }

    // Pending comments visible to the article author and the editor.
    public function getPendingByArticle(int $articleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM comments
             WHERE article_id = ? AND status = "pending"
             ORDER BY created_at ASC'
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll();
    }

    // All pending comments across all articles (for the editor dashboard).
    public function getAllPending(): array
    {
        $stmt = $this->db->query(
            'SELECT c.*, a.title AS article_title, a.author_id AS article_author_id
             FROM comments c
             JOIN articles a ON c.article_id = a.id
             WHERE c.status = "pending"
             ORDER BY c.created_at ASC'
        );
        return $stmt->fetchAll();
    }

    public function create(int $articleId, string $name, string $email, string $body): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO comments (article_id, name, email, body, status, created_at)
             VALUES (?, ?, ?, ?, "pending", NOW())'
        );
        $stmt->execute([$articleId, $name, $email, $body]);
    }

    public function approve(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE comments SET status = "approved" WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM comments WHERE id = ?');
        $stmt->execute([$id]);
    }
}

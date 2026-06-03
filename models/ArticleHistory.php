<?php
class ArticleHistory
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Record a workflow event for an article.
    public function add(int $articleId, int $userId, string $action, ?string $note = null): void
    {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO article_history (article_id, user_id, action, note, created_at)
                 VALUES (?, ?, ?, ?, NOW())'
            );
            $stmt->execute([$articleId, $userId, $action, $note]);
        } catch (\PDOException $e) {
            // Silently skip if table does not exist yet.
        }
    }

    // Fetch full history for one article, oldest event first.
    // Returns empty array gracefully if the table does not exist yet.
    public function getByArticle(int $articleId): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT h.*, u.full_name AS actor_name, u.role AS actor_role
                 FROM article_history h
                 JOIN users u ON h.user_id = u.id
                 WHERE h.article_id = ?
                 ORDER BY h.created_at ASC'
            );
            $stmt->execute([$articleId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }
}

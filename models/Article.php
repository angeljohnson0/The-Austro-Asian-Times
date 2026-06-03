<?php
class Article
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Top 5 most-recently updated published articles for the front page.
    public function getTopFive(): array
    {
        $stmt = $this->db->query(
            'SELECT a.*, u.full_name AS author_name
             FROM articles a
             JOIN users u ON a.author_id = u.id
             WHERE a.status = "published"
             ORDER BY a.updated_at DESC
             LIMIT 5'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, u.full_name AS author_name
             FROM articles a
             JOIN users u ON a.author_id = u.id
             WHERE a.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // All articles by a journalist, newest first.
    public function getByAuthor(int $authorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM articles
             WHERE author_id = ?
             ORDER BY updated_at DESC'
        );
        $stmt->execute([$authorId]);
        return $stmt->fetchAll();
    }

    // Articles awaiting editorial review.
    public function getPending(): array
    {
        $stmt = $this->db->query(
            'SELECT a.*, u.full_name AS author_name
             FROM articles a
             JOIN users u ON a.author_id = u.id
             WHERE a.status = "pending"
             ORDER BY a.updated_at ASC'
        );
        return $stmt->fetchAll();
    }

    // All published articles ordered by date for the archive.
    public function getArchive(): array
    {
        $stmt = $this->db->query(
            'SELECT a.id, a.title, a.updated_at, u.full_name AS author_name,
                    DATE_FORMAT(a.updated_at, "%Y-%m")  AS month_key,
                    DATE_FORMAT(a.updated_at, "%M %Y")  AS month_label
             FROM articles a
             JOIN users u ON a.author_id = u.id
             WHERE a.status = "published"
             ORDER BY a.updated_at DESC'
        );
        return $stmt->fetchAll();
    }

    // Latest published articles for the RSS feed.
    public function getLatestPublished(int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, u.full_name AS author_name
             FROM articles a
             JOIN users u ON a.author_id = u.id
             WHERE a.status = "published"
             ORDER BY a.updated_at DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    // Dynamic search across published articles.
    // Supports keyword (title+body), tag name, and date range filters.
    // Only the filters that carry a non-empty value are applied.
    public function search(string $keyword, string $tag, string $dateFrom, string $dateTo): array
    {
        $conditions = ['a.status = "published"'];
        $params     = [];

        if ($keyword !== '') {
            $conditions[] = '(a.title LIKE ? OR a.body LIKE ?)';
            $like = '%' . $keyword . '%';
            $params[] = $like;
            $params[] = $like;
        }

        if ($tag !== '') {
            $conditions[] = 'EXISTS (
                SELECT 1 FROM article_tags art
                JOIN tags t ON art.tag_id = t.id
                WHERE art.article_id = a.id AND t.name LIKE ?
            )';
            $params[] = '%' . $tag . '%';
        }

        if ($dateFrom !== '') {
            $conditions[] = 'DATE(a.updated_at) >= ?';
            $params[] = $dateFrom;
        }

        if ($dateTo !== '') {
            $conditions[] = 'DATE(a.updated_at) <= ?';
            $params[] = $dateTo;
        }

        $sql = 'SELECT a.*, u.full_name AS author_name
                FROM articles a
                JOIN users u ON a.author_id = u.id
                WHERE ' . implode(' AND ', $conditions) . '
                ORDER BY a.updated_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(int $authorId, string $title, string $body, ?string $imagePath): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO articles (author_id, title, body, image_path, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, "draft", NOW(), NOW())'
        );
        $stmt->execute([$authorId, $title, $body, $imagePath]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $title, string $body, ?string $imagePath): void
    {
        if ($imagePath !== null) {
            $stmt = $this->db->prepare(
                'UPDATE articles SET title = ?, body = ?, image_path = ?, updated_at = NOW() WHERE id = ?'
            );
            $stmt->execute([$title, $body, $imagePath, $id]);
        } else {
            $stmt = $this->db->prepare(
                'UPDATE articles SET title = ?, body = ?, updated_at = NOW() WHERE id = ?'
            );
            $stmt->execute([$title, $body, $id]);
        }
    }

    // Move a draft or rejected article into the moderation queue.
    public function submitForReview(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE articles SET status = "pending", rejection_note = NULL, updated_at = NOW() WHERE id = ?'
        );
        $stmt->execute([$id]);
    }

    // Editor approves or rejects an article; optional rejection note returned to journalist.
    public function updateStatus(int $id, string $status, ?string $rejectionNote = null): void
    {
        $stmt = $this->db->prepare(
            'UPDATE articles SET status = ?, rejection_note = ?, updated_at = NOW() WHERE id = ?'
        );
        $stmt->execute([$status, $rejectionNote, $id]);
    }

    public function toggleComments(int $id, int $enabled): void
    {
        $stmt = $this->db->prepare('UPDATE articles SET comments_enabled = ? WHERE id = ?');
        $stmt->execute([$enabled, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM articles WHERE id = ?');
        $stmt->execute([$id]);
    }
}

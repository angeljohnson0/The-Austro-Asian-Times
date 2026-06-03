<?php
class Tag
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // All tags attached to a given article.
    public function getByArticle(int $articleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*
             FROM tags t
             JOIN article_tags art ON t.id = art.tag_id
             WHERE art.article_id = ?
             ORDER BY t.name'
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll();
    }

    // Published articles that carry a specific tag name.
    public function getArticlesByTag(string $tagName): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, u.full_name AS author_name
             FROM articles a
             JOIN users u ON a.author_id = u.id
             JOIN article_tags art ON a.id = art.article_id
             JOIN tags t ON art.tag_id = t.id
             WHERE t.name = ? AND a.status = "published"
             ORDER BY a.updated_at DESC'
        );
        $stmt->execute([$tagName]);
        return $stmt->fetchAll();
    }

    // Replaces all tags on an article with the given comma-separated string.
    public function syncTags(int $articleId, string $tagsInput): void
    {
        $stmt = $this->db->prepare('DELETE FROM article_tags WHERE article_id = ?');
        $stmt->execute([$articleId]);

        if (trim($tagsInput) === '') {
            return;
        }

        $tagNames = array_unique(
            array_filter(array_map('trim', explode(',', strtolower($tagsInput))))
        );

        foreach ($tagNames as $name) {
            // Insert tag name if it does not exist yet.
            $stmt = $this->db->prepare('INSERT IGNORE INTO tags (name) VALUES (?)');
            $stmt->execute([$name]);

            $stmt = $this->db->prepare('SELECT id FROM tags WHERE name = ?');
            $stmt->execute([$name]);
            $tag = $stmt->fetch();

            $stmt = $this->db->prepare('INSERT IGNORE INTO article_tags (article_id, tag_id) VALUES (?, ?)');
            $stmt->execute([$articleId, $tag['id']]);
        }
    }

    // Returns tags for an article as a comma-separated string (for pre-filling the edit form).
    public function getTagsAsString(int $articleId): string
    {
        $tags = $this->getByArticle($articleId);
        return implode(', ', array_column($tags, 'name'));
    }
}

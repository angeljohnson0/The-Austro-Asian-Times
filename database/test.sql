-- =============================================================
-- The Austro-Asian Times: CRUD Test Script (test.sql)
-- Run AFTER load.sql. Tests all CRUD operations on every table.
-- The assessor can run this independently to verify the schema.
-- =============================================================

USE austro_asian_times;

-- =============================================================
-- USERS TABLE
-- =============================================================

SELECT '--- USERS: READ all ---' AS test;
SELECT id, username, role, full_name FROM users;

SELECT '--- USERS: CREATE test user ---' AS test;
INSERT INTO users (username, password_hash, role, full_name, bio)
VALUES ('test_journalist', 'testhash', 'journalist', 'Test Reporter', 'Test bio for CRUD testing.');

SELECT '--- USERS: READ test user ---' AS test;
SELECT id, username, role, full_name FROM users WHERE username = 'test_journalist';

SELECT '--- USERS: UPDATE test user bio ---' AS test;
UPDATE users SET bio = 'Updated bio text' WHERE username = 'test_journalist';
SELECT bio FROM users WHERE username = 'test_journalist';

SELECT '--- USERS: DELETE test user ---' AS test;
DELETE FROM users WHERE username = 'test_journalist';
SELECT COUNT(*) AS should_be_zero FROM users WHERE username = 'test_journalist';

-- =============================================================
-- ARTICLES TABLE
-- =============================================================

SELECT '--- ARTICLES: READ all published ---' AS test;
SELECT id, title, status, author_id FROM articles WHERE status = 'published';

SELECT '--- ARTICLES: CREATE draft article ---' AS test;
INSERT INTO articles (title, body, status, author_id)
VALUES ('CRUD Test Article', 'Body text for CRUD test.', 'draft', 2);

SET @test_article_id = LAST_INSERT_ID();

SELECT '--- ARTICLES: READ test article ---' AS test;
SELECT id, title, status FROM articles WHERE id = @test_article_id;

SELECT '--- ARTICLES: UPDATE status to pending ---' AS test;
UPDATE articles SET status = 'pending' WHERE id = @test_article_id;
SELECT id, status FROM articles WHERE id = @test_article_id;

SELECT '--- ARTICLES: UPDATE status to published ---' AS test;
UPDATE articles SET status = 'published' WHERE id = @test_article_id;
SELECT id, status FROM articles WHERE id = @test_article_id;

SELECT '--- ARTICLES: DELETE test article ---' AS test;
DELETE FROM articles WHERE id = @test_article_id;
SELECT COUNT(*) AS should_be_zero FROM articles WHERE id = @test_article_id;

-- =============================================================
-- TAGS TABLE
-- =============================================================

SELECT '--- TAGS: READ all ---' AS test;
SELECT id, name FROM tags ORDER BY name;

SELECT '--- TAGS: CREATE test tag ---' AS test;
INSERT INTO tags (name) VALUES ('crud_test_tag');

SELECT '--- TAGS: READ test tag ---' AS test;
SELECT id, name FROM tags WHERE name = 'crud_test_tag';

SELECT '--- TAGS: UPDATE test tag name ---' AS test;
UPDATE tags SET name = 'crud_test_tag_updated' WHERE name = 'crud_test_tag';
SELECT id, name FROM tags WHERE name = 'crud_test_tag_updated';

SELECT '--- TAGS: DELETE test tag ---' AS test;
DELETE FROM tags WHERE name = 'crud_test_tag_updated';
SELECT COUNT(*) AS should_be_zero FROM tags WHERE name = 'crud_test_tag_updated';

-- =============================================================
-- ARTICLE_TAGS TABLE
-- =============================================================

SELECT '--- ARTICLE_TAGS: READ tags for article 1 ---' AS test;
SELECT a.title, t.name AS tag
FROM articles a
JOIN article_tags art ON a.id = art.article_id
JOIN tags t ON art.tag_id = t.id
WHERE a.id = 1;

SELECT '--- ARTICLE_TAGS: CREATE link ---' AS test;
INSERT INTO article_tags (article_id, tag_id) VALUES (1, 6);

SELECT '--- ARTICLE_TAGS: READ updated tags for article 1 ---' AS test;
SELECT t.name FROM tags t
JOIN article_tags art ON t.id = art.tag_id
WHERE art.article_id = 1;

SELECT '--- ARTICLE_TAGS: DELETE test link ---' AS test;
DELETE FROM article_tags WHERE article_id = 1 AND tag_id = 6;
SELECT COUNT(*) AS should_be_zero FROM article_tags WHERE article_id = 1 AND tag_id = 6;

-- =============================================================
-- COMMENTS TABLE
-- =============================================================

SELECT '--- COMMENTS: READ approved comments for article 1 ---' AS test;
SELECT id, name, status FROM comments WHERE article_id = 1 AND status = 'approved';

SELECT '--- COMMENTS: CREATE test comment ---' AS test;
INSERT INTO comments (article_id, name, email, body, status)
VALUES (1, 'CRUD Tester', 'crud@test.com', 'This is a CRUD test comment.', 'pending');

SET @test_comment_id = LAST_INSERT_ID();

SELECT '--- COMMENTS: READ test comment ---' AS test;
SELECT id, name, status FROM comments WHERE id = @test_comment_id;

SELECT '--- COMMENTS: UPDATE status to approved ---' AS test;
UPDATE comments SET status = 'approved' WHERE id = @test_comment_id;
SELECT id, name, status FROM comments WHERE id = @test_comment_id;

SELECT '--- COMMENTS: DELETE test comment ---' AS test;
DELETE FROM comments WHERE id = @test_comment_id;
SELECT COUNT(*) AS should_be_zero FROM comments WHERE id = @test_comment_id;

-- =============================================================
-- REFERENTIAL INTEGRITY TEST
-- Deleting a user should cascade-delete their articles.
-- =============================================================

SELECT '--- CASCADE: Insert temp user and article ---' AS test;
INSERT INTO users (username, password_hash, role, full_name)
VALUES ('cascade_test_user', 'hash', 'journalist', 'Cascade Test');
SET @cascade_user_id = LAST_INSERT_ID();

INSERT INTO articles (title, body, author_id)
VALUES ('Cascade Test Article', 'Body.', @cascade_user_id);
SET @cascade_article_id = LAST_INSERT_ID();

SELECT '--- CASCADE: Delete user, article should disappear ---' AS test;
DELETE FROM users WHERE id = @cascade_user_id;
SELECT COUNT(*) AS should_be_zero FROM articles WHERE id = @cascade_article_id;

-- =============================================================
-- ARTICLE_HISTORY TABLE
-- =============================================================

SELECT '--- HISTORY: READ history for article 1 ---' AS test;
SELECT h.id, h.action, u.full_name AS actor, h.note, h.created_at
FROM article_history h
JOIN users u ON h.user_id = u.id
WHERE h.article_id = 1
ORDER BY h.created_at ASC;

SELECT '--- HISTORY: CREATE test history entry ---' AS test;
INSERT INTO article_history (article_id, user_id, action, note)
VALUES (1, 2, 'resubmitted', NULL);
SET @test_history_id = LAST_INSERT_ID();

SELECT '--- HISTORY: READ test entry ---' AS test;
SELECT id, article_id, action FROM article_history WHERE id = @test_history_id;

SELECT '--- HISTORY: UPDATE test entry note ---' AS test;
UPDATE article_history SET note = 'Test note update' WHERE id = @test_history_id;
SELECT note FROM article_history WHERE id = @test_history_id;

SELECT '--- HISTORY: DELETE test entry ---' AS test;
DELETE FROM article_history WHERE id = @test_history_id;
SELECT COUNT(*) AS should_be_zero FROM article_history WHERE id = @test_history_id;

SELECT '--- HISTORY: CASCADE - delete article should remove its history ---' AS test;
INSERT INTO articles (title, body, author_id) VALUES ('Cascade History Test', 'Body.', 2);
SET @ch_article_id = LAST_INSERT_ID();
INSERT INTO article_history (article_id, user_id, action) VALUES (@ch_article_id, 2, 'submitted');
DELETE FROM articles WHERE id = @ch_article_id;
SELECT COUNT(*) AS should_be_zero FROM article_history WHERE article_id = @ch_article_id;

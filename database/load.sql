-- =============================================================
-- The Austro-Asian Times: Seed Data (load.sql)
-- Run AFTER create.sql to populate the database with test data.
-- All test accounts use the password: password
-- (bcrypt hash generated with PHP password_hash('password', PASSWORD_BCRYPT))
-- =============================================================

USE austro_asian_times;

-- -------------------------------------------------------------
-- Test Users
-- editor1   / password  (role: editor)
-- journalist1 / password  (role: journalist)
-- journalist2 / password  (role: journalist)
-- -------------------------------------------------------------
INSERT INTO users (username, password_hash, role, full_name, bio) VALUES
(
    'editor1',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'editor',
    'Sarah Mitchell',
    'Senior editor at The Austro-Asian Times with over 15 years of experience in regional journalism.'
),
(
    'journalist1',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'journalist',
    'James Wong',
    'James covers Southeast Asian politics and economics from our Singapore bureau.'
),
(
    'journalist2',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'journalist',
    'Priya Sharma',
    'Priya reports on Northern Australian affairs, based in Darwin.'
);

-- -------------------------------------------------------------
-- Published Articles (visible on front page)
-- image_path values match the files in uploads/ distributed
-- with the project so images persist across installations.
-- -------------------------------------------------------------
INSERT INTO articles (title, body, status, author_id, comments_enabled, image_path, created_at, updated_at) VALUES
(
    'Darwin Port Sees Record Shipping Activity in Q1',
    'The Port of Darwin has recorded its highest quarterly shipping volume in over a decade, with officials attributing the growth to increased trade with Southeast Asian partners.\n\nPort authority spokesperson Tom Nguyen confirmed that container throughput rose by 28 percent compared to the same period last year. "We are seeing strong demand from Indonesian and Malaysian exporters in particular," he said.\n\nThe Darwin City Council has welcomed the figures, indicating that infrastructure investment plans may be brought forward in response.',
    'published',
    2,
    1,
    'seed_article_1.jpg',
    '2026-05-20 09:00:00',
    '2026-05-20 09:00:00'
),
(
    'Singapore Summit Addresses Regional Climate Policy',
    'Representatives from twelve nations gathered in Singapore this week for a two-day summit focused on coordinating climate policy across the Indo-Pacific region.\n\nThe conference, hosted by the ASEAN Secretariat, produced a joint declaration committing member states to reduce deforestation by 40 percent before 2035.\n\nEnvironmental groups cautiously welcomed the pledge while calling for binding enforcement mechanisms.',
    'published',
    3,
    1,
    'seed_article_2.jpg',
    '2026-05-22 14:30:00',
    '2026-05-22 14:30:00'
),
(
    'NT Government Announces Remote Housing Funding Package',
    'The Northern Territory government has unveiled a $180 million funding package aimed at addressing the chronic housing shortage in remote Indigenous communities.\n\nMinister for Housing Rebecca Cole said the funds would support construction of 320 new homes across 14 communities over the next three years.\n\nIndigenous community leaders expressed cautious optimism but called for local employment requirements to be embedded in contracts.',
    'published',
    3,
    0,
    'seed_article_3.jpg',
    '2026-05-25 11:00:00',
    '2026-05-25 11:00:00'
),
(
    'Indonesian Tech Startups Eye Darwin as Regional Hub',
    'A delegation of Indonesian technology entrepreneurs visited Darwin this month to explore opportunities for establishing regional operations in the Northern Territory.\n\nThe group, comprising representatives from eight Jakarta-based startups, met with Territory government officials and CDU researchers during a two-day visit.\n\nChief Minister spokesperson Dana Reid said the Territory is positioning itself as a gateway for Southeast Asian technology investment.',
    'published',
    2,
    1,
    'seed_article_4.jpg',
    '2026-05-28 08:15:00',
    '2026-05-28 08:15:00'
),
(
    'Kakadu Tourism Numbers Rebound Post-Pandemic',
    'Visitor numbers to Kakadu National Park have returned to pre-pandemic levels for the first time since 2019, according to data released by Parks Australia.\n\nA total of 214,000 visitors entered the park in the first four months of 2026, with international arrivals accounting for a growing share of the total.\n\nPark management is reviewing infrastructure capacity to handle the increased load while protecting sensitive ecological areas.',
    'published',
    2,
    1,
    'seed_article_5.jpg',
    '2026-05-30 10:45:00',
    '2026-05-30 10:45:00'
);

-- -------------------------------------------------------------
-- Draft and Pending Articles (for testing workflow)
-- -------------------------------------------------------------
INSERT INTO articles (title, body, status, author_id, created_at, updated_at) VALUES
(
    'Draft: Northern Territory Budget Preview',
    'Early indications suggest the upcoming Territory budget will prioritise infrastructure and health spending...',
    'draft',
    2,
    '2026-05-31 07:00:00',
    '2026-05-31 07:00:00'
),
(
    'Malaysia Flood Response Draws International Praise',
    'The Malaysian government has been commended internationally for its swift response to flooding that affected three states in the south of the peninsula last week.\n\nOver 40,000 residents were evacuated within 48 hours, with military and civilian emergency services coordinating closely.\n\nThe UN Office for the Coordination of Humanitarian Affairs called the response a model for disaster management in the region.',
    'pending',
    3,
    '2026-05-31 09:30:00',
    '2026-05-31 09:30:00'
);

-- -------------------------------------------------------------
-- Tags
-- -------------------------------------------------------------
INSERT INTO tags (name) VALUES
('darwin'), ('northern territory'), ('singapore'), ('indonesia'),
('malaysia'), ('climate'), ('trade'), ('tourism'), ('housing'),
('technology'), ('kakadu'), ('asean');

-- -------------------------------------------------------------
-- Article-Tag Links
-- -------------------------------------------------------------
-- Article 1: Darwin Port
INSERT INTO article_tags (article_id, tag_id) VALUES (1, 1), (1, 2), (1, 8);
-- Article 2: Singapore Summit
INSERT INTO article_tags (article_id, tag_id) VALUES (2, 3), (2, 6), (2, 12);
-- Article 3: NT Housing
INSERT INTO article_tags (article_id, tag_id) VALUES (3, 1), (3, 2), (3, 9);
-- Article 4: Indonesian Tech
INSERT INTO article_tags (article_id, tag_id) VALUES (4, 1), (4, 4), (4, 10);
-- Article 5: Kakadu Tourism
INSERT INTO article_tags (article_id, tag_id) VALUES (5, 2), (5, 11), (5, 8);
-- Article 7 (pending): Malaysia Floods
INSERT INTO article_tags (article_id, tag_id) VALUES (7, 5), (7, 6);

-- -------------------------------------------------------------
-- Sample Comments (for articles with comments_enabled = 1)
-- -------------------------------------------------------------
INSERT INTO comments (article_id, name, email, body, status, created_at) VALUES
(1, 'Michael Tan', 'mtan@example.com', 'Great to see Darwin growing as a regional trade hub. The port expansion was long overdue.', 'approved', '2026-05-21 10:30:00'),
(1, 'Helen Park', 'hpark@example.com', 'I live near the port and the increased traffic has been noticeable. Hope noise mitigation is considered.', 'approved', '2026-05-21 14:00:00'),
(1, 'Anon Reader', 'reader@example.com', 'Will this affect local fishing access?', 'pending', '2026-05-22 08:00:00'),
(4, 'Li Wei', 'liwei@example.com', 'This is exciting news for Southeast Asian entrepreneurs looking to expand into Australia.', 'approved', '2026-05-29 09:00:00'),
(5, 'Tourism Fan', 'tfan@example.com', 'Kakadu is one of the most beautiful places on earth. Glad to see the numbers recovering.', 'approved', '2026-05-31 12:00:00');

-- -------------------------------------------------------------
-- Article History (workflow events for published articles)
-- user 2 = journalist1, user 3 = journalist2, user 1 = editor1
-- -------------------------------------------------------------
INSERT INTO article_history (article_id, user_id, action, note, created_at) VALUES
-- Article 1: Darwin Port (straight approval)
(1, 2, 'submitted',  NULL,                                           '2026-05-20 07:30:00'),
(1, 1, 'approved',   NULL,                                           '2026-05-20 09:00:00'),
-- Article 2: Singapore Summit (rejected once, then resubmitted and approved)
(2, 3, 'submitted',  NULL,                                           '2026-05-22 08:00:00'),
(2, 1, 'rejected',   'Please add quotes from ASEAN representatives.','2026-05-22 10:00:00'),
(2, 3, 'resubmitted',NULL,                                           '2026-05-22 13:00:00'),
(2, 1, 'approved',   NULL,                                           '2026-05-22 14:30:00'),
-- Article 3: NT Housing (straight approval)
(3, 3, 'submitted',  NULL,                                           '2026-05-25 09:00:00'),
(3, 1, 'approved',   NULL,                                           '2026-05-25 11:00:00'),
-- Article 4: Indonesian Tech (straight approval)
(4, 2, 'submitted',  NULL,                                           '2026-05-28 07:00:00'),
(4, 1, 'approved',   NULL,                                           '2026-05-28 08:15:00'),
-- Article 5: Kakadu Tourism (straight approval)
(5, 2, 'submitted',  NULL,                                           '2026-05-30 09:00:00'),
(5, 1, 'approved',   NULL,                                           '2026-05-30 10:45:00'),
-- Article 7: Malaysia Floods (pending, awaiting review)
(7, 3, 'submitted',  NULL,                                           '2026-05-31 09:30:00');

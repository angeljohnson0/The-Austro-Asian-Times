-- =============================================================
-- The Austro-Asian Times: Database Schema (create.sql)
-- Run this file first to create the database and all tables.
-- Requires: MySQL 8 | Charset: utf8mb4
-- =============================================================

CREATE DATABASE IF NOT EXISTS austro_asian_times
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE austro_asian_times;

-- -------------------------------------------------------------
-- Table: users
-- Stores both journalists and the editor.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id            INT          AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('journalist','editor') NOT NULL DEFAULT 'journalist',
    full_name     VARCHAR(100) NOT NULL,
    bio           TEXT,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Table: articles
-- Core content table. status drives the moderation workflow.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS articles (
    id               INT          AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(255) NOT NULL,
    body             TEXT         NOT NULL,
    status           ENUM('draft','pending','published','rejected') NOT NULL DEFAULT 'draft',
    author_id        INT          NOT NULL,
    comments_enabled TINYINT(1)   NOT NULL DEFAULT 0,
    image_path       VARCHAR(255)          DEFAULT NULL,
    rejection_note   TEXT                  DEFAULT NULL,
    created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Table: tags
-- Folksonomy tag names; unique to avoid duplicates.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tags (
    id   INT         AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Table: article_tags
-- Many-to-many pivot linking articles to tags.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS article_tags (
    article_id INT NOT NULL,
    tag_id     INT NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id)     REFERENCES tags(id)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Table: comments
-- Reader comments per article, held in pending until approved.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comments (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    article_id INT          NOT NULL,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    body       TEXT         NOT NULL,
    status     ENUM('pending','approved') NOT NULL DEFAULT 'pending',
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Table: article_history
-- Tracks each workflow event: submitted, approved, rejected, resubmitted.
-- Visible to the article author and the editor.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS article_history (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    article_id INT          NOT NULL,
    user_id    INT          NOT NULL,
    action     ENUM('submitted','resubmitted','approved','rejected') NOT NULL,
    note       TEXT                  DEFAULT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

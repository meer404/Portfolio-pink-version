-- Run this if you already have portfolio_db set up
USE portfolio_db;

CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_en VARCHAR(100) NOT NULL,
    name_ku VARCHAR(100) NOT NULL,
    category_en VARCHAR(100) DEFAULT 'General',
    category_ku VARCHAR(100) DEFAULT 'گشتی',
    level INT DEFAULT 80,
    icon VARCHAR(10) DEFAULT '⚡',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_en VARCHAR(255) NOT NULL,
    title_ku VARCHAR(255) NOT NULL,
    description_en TEXT,
    description_ku TEXT,
    image VARCHAR(255) DEFAULT '',
    tech_stack VARCHAR(500) DEFAULT '',
    project_url VARCHAR(255) DEFAULT '#',
    github_url VARCHAR(255) DEFAULT '#',
    featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add stat columns to about (ignore errors if they already exist)
ALTER TABLE about ADD COLUMN stat_years INT DEFAULT 5;
ALTER TABLE about ADD COLUMN stat_projects INT DEFAULT 50;
ALTER TABLE about ADD COLUMN stat_clients INT DEFAULT 30;

-- Seed skills if empty
INSERT INTO skills (name_en, name_ku, category_en, category_ku, level, icon, sort_order)
SELECT * FROM (SELECT 'HTML & CSS' AS a, 'HTML و CSS' AS b, 'Frontend' AS c, 'فرۆنتێند' AS d, 95 AS e, '🎨' AS f, 1 AS g) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM skills LIMIT 1);

INSERT INTO projects (title_en, title_ku, description_en, description_ku, tech_stack, featured, sort_order)
SELECT * FROM (SELECT 'E-Commerce Platform' AS a, 'پلاتفۆرمی بازرگانی ئەلیکترۆنی' AS b, 'A full-featured online store with cart, payments, and admin dashboard.' AS c, 'فرۆشگایەکی تەواو لەسەر ئینتەرنێت.' AS d, 'PHP, MySQL, JavaScript' AS e, 1 AS f, 1 AS g) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM projects LIMIT 1);

-- ============================================
-- Portfolio Website Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

-- ============================================
-- 1. Admins Table
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: admin / admin123
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ============================================
-- 2. Site Settings (Hero Section)
-- ============================================
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    greeting_en VARCHAR(255) DEFAULT 'Hello, I''m',
    greeting_ku VARCHAR(255) DEFAULT 'سڵاو، من',
    name_en VARCHAR(255) DEFAULT 'Hana Ahmed',
    name_ku VARCHAR(255) DEFAULT 'هانا ئەحمەد',
    title_en VARCHAR(255) DEFAULT 'Full-Stack Developer & UI/UX Designer',
    title_ku VARCHAR(255) DEFAULT 'پڕۆگرامەر و دیزاینەری UI/UX',
    cta_text_en VARCHAR(255) DEFAULT 'View My Work',
    cta_text_ku VARCHAR(255) DEFAULT 'کارەکانم ببینە',
    cta_link VARCHAR(255) DEFAULT '#about',
    hero_subtitle_en TEXT DEFAULT 'I craft beautiful digital experiences with clean code and elegant design.',
    hero_subtitle_ku TEXT DEFAULT 'من ئەزموونی دیجیتاڵی جوان دروست دەکەم بە کۆدی پاک و دیزاینی ئێلیگانت.',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO site_settings (id) VALUES (1);

-- ============================================
-- 3. About Section
-- ============================================
CREATE TABLE IF NOT EXISTS about (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_en VARCHAR(255) DEFAULT 'About Me',
    title_ku VARCHAR(255) DEFAULT 'دەربارەی من',
    bio_en TEXT,
    bio_ku TEXT,
    profile_image VARCHAR(255) DEFAULT '',
    stat_years INT DEFAULT 5,
    stat_projects INT DEFAULT 50,
    stat_clients INT DEFAULT 30,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO about (bio_en, bio_ku) VALUES (
    'I am a passionate Full-Stack Developer and UI/UX Designer with over 5 years of experience creating beautiful, user-friendly web applications. I specialize in crafting elegant digital solutions that combine stunning visual design with robust functionality.\n\nMy journey in tech began with a love for art and design, which naturally evolved into a career in web development. I believe that great design is not just about aesthetics — it''s about creating meaningful experiences that connect with people.\n\nWhen I''m not coding, you can find me exploring new design trends, reading about the latest technologies, or enjoying a good cup of coffee while sketching new ideas.',
    'من پڕۆگرامەرێکی تەواو و دیزاینەری UI/UX م بە زیاتر لە ٥ ساڵ ئەزموون لە دروستکردنی ئەپڵیکەیشنی وێبی جوان و ئاسان بۆ بەکارهێنەر. من پسپۆڕم لە دروستکردنی چارەسەری دیجیتاڵی ئێلیگانت کە دیزاینی بینایی نایاب لەگەڵ کارایی بەهێز تێکەڵ دەکات.\n\nگەشتی من لە تەکنەلۆژیا بە خۆشەویستی هونەر و دیزاینەوە دەستی پێکرد، کە بە شێوەیەکی سروشتی بوو بە کاری پڕۆگرامکردنی وێب. من باوەڕم وایە کە دیزاینی باش تەنها سەبارەت بە جوانی نییە — سەبارەت بە دروستکردنی ئەزموونی واتادار کە پەیوەندی بە خەڵکیەوە دەبەستێت.\n\nکاتێک کۆد نانووسم، دەتوانیت بمدۆزیتەوە لە گەڕان بەدوای ئاڕاستەی نوێی دیزاین، خوێندنەوە لەسەر نوێترین تەکنەلۆژیاکان، یان چێژوەرگرتن لە فنجانێک قاوەی باش لە کاتی وێنەکێشانی بیرۆکەی نوێ.'
);

-- ============================================
-- 4. Experience Table
-- ============================================
CREATE TABLE IF NOT EXISTS experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_en VARCHAR(255) NOT NULL,
    company_ku VARCHAR(255) NOT NULL,
    role_en VARCHAR(255) NOT NULL,
    role_ku VARCHAR(255) NOT NULL,
    date_start VARCHAR(50) NOT NULL,
    date_end VARCHAR(50) DEFAULT 'Present',
    description_en TEXT,
    description_ku TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO experience (company_en, company_ku, role_en, role_ku, date_start, date_end, description_en, description_ku, sort_order) VALUES
('TechBloom Agency', 'ئاژانسی تێکبلووم', 'Senior Full-Stack Developer', 'پڕۆگرامەری سینیەری تەواو', '2022', 'Present', 'Led the development of 15+ client projects using modern web technologies. Mentored junior developers and established coding standards that improved team productivity by 40%.', 'سەرپەرشتیکردنی گەشەپێدانی ١٥+ پڕۆژەی کلایەنت بە بەکارهێنانی تەکنەلۆژیای وێبی مۆدێرن. ڕاهێنانی پڕۆگرامەرە نوێکان و دانانی ستانداردی کۆدنووسین کە بەرهەمدارێتی تیمەکەی ٤٠٪ زیادکرد.', 1),
('PixelCraft Studio', 'ستودیۆی پیکسڵکرافت', 'UI/UX Designer & Developer', 'دیزاینەر و پڕۆگرامەری UI/UX', '2020', '2022', 'Designed and developed responsive web applications for e-commerce and SaaS clients. Created design systems that reduced development time by 30%.', 'دیزاین و گەشەپێدانی ئەپڵیکەیشنی وێبی ڕێسپۆنسیڤ بۆ کلایەنتی بازرگانی ئەلیکترۆنی و SaaS. دروستکردنی سیستەمی دیزاین کە کاتی گەشەپێدان ٣٠٪ کەمکردەوە.', 2),
('Starter Web Co.', 'کۆمپانیای ستارتەر وێب', 'Junior Web Developer', 'پڕۆگرامەری وێبی تازەکار', '2018', '2020', 'Built and maintained WordPress and custom PHP websites for small businesses. Learned modern JavaScript frameworks and responsive design principles.', 'دروستکردن و پاراستنی وێبسایتی وۆردپرێس و PHP ی تایبەت بۆ کاروباری بچووک. فێربوونی فرێمۆرکی جاڤاسکریپتی مۆدێرن و بنەماکانی دیزاینی ڕێسپۆنسیڤ.', 3);

-- ============================================
-- 5. Education Table
-- ============================================
CREATE TABLE IF NOT EXISTS education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    institution_en VARCHAR(255) NOT NULL,
    institution_ku VARCHAR(255) NOT NULL,
    degree_en VARCHAR(255) NOT NULL,
    degree_ku VARCHAR(255) NOT NULL,
    date_start VARCHAR(50) NOT NULL,
    date_end VARCHAR(50) NOT NULL,
    description_en TEXT,
    description_ku TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO education (institution_en, institution_ku, degree_en, degree_ku, date_start, date_end, description_en, description_ku, sort_order) VALUES
('University of Sulaimani', 'زانکۆی سلێمانی', 'Bachelor of Computer Science', 'بەکالۆریۆسی زانستی کۆمپیوتەر', '2014', '2018', 'Graduated with honors. Focused on software engineering and web technologies. Completed a capstone project on responsive web application design.', 'دەرچوون بە ڕێزەوە. تمرکوزکردن لەسەر ئەندازیاری سۆفتوێر و تەکنەلۆژیای وێب. تەواوکردنی پڕۆژەی کۆتایی لەسەر دیزاینی ئەپڵیکەیشنی وێبی ڕێسپۆنسیڤ.', 1),
('Google UX Design Certificate', 'بڕوانامەی دیزاینی UX ی گووگڵ', 'Professional Certificate', 'بڕوانامەی پیشەیی', '2021', '2021', 'Completed Google''s comprehensive UX design program covering user research, wireframing, prototyping, and usability testing.', 'تەواوکردنی بەرنامەی تەواوی دیزاینی UX ی گووگڵ کە لێکۆڵینەوەی بەکارهێنەر، وایەرفرەیمینگ، پرۆتۆتایپینگ، و تاقیکردنەوەی بەکارهێنان لەخۆ دەگرێت.', 2);

-- ============================================
-- 6. Contact Info Table
-- ============================================
CREATE TABLE IF NOT EXISTS contact_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) DEFAULT 'hana@example.com',
    phone VARCHAR(50) DEFAULT '+964 750 123 4567',
    location_en VARCHAR(255) DEFAULT 'Sulaimani, Kurdistan Region, Iraq',
    location_ku VARCHAR(255) DEFAULT 'سلێمانی، هەرێمی کوردستان، عێراق',
    linkedin VARCHAR(255) DEFAULT '#',
    github VARCHAR(255) DEFAULT '#',
    twitter VARCHAR(255) DEFAULT '#',
    instagram VARCHAR(255) DEFAULT '#',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO contact_info (id) VALUES (1);

-- ============================================
-- 7. Skills Table
-- ============================================
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

INSERT INTO skills (name_en, name_ku, category_en, category_ku, level, icon, sort_order) VALUES
('HTML & CSS', 'HTML و CSS', 'Frontend', 'فرۆنتێند', 95, '🎨', 1),
('JavaScript', 'جاڤاسکریپت', 'Frontend', 'فرۆنتێند', 90, '⚡', 2),
('React / Vue', 'ڕیاکت / ڤیو', 'Frontend', 'فرۆنتێند', 85, '⚛️', 3),
('PHP', 'PHP', 'Backend', 'باکێند', 88, '🐘', 4),
('MySQL', 'MySQL', 'Backend', 'باکێند', 85, '🗄️', 5),
('UI/UX Design', 'دیزاینی UI/UX', 'Design', 'دیزاین', 92, '✨', 6),
('Figma', 'فیگما', 'Design', 'دیزاین', 90, '🎯', 7),
('Git & DevOps', 'گیت و DevOps', 'Tools', 'ئامراز', 80, '🔧', 8);

-- ============================================
-- 8. Projects Table
-- ============================================
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

INSERT INTO projects (title_en, title_ku, description_en, description_ku, tech_stack, project_url, github_url, featured, sort_order) VALUES
('E-Commerce Platform', 'پلاتفۆرمی بازرگانی ئەلیکترۆنی', 'A full-featured online store with cart, payments, and admin dashboard. Built with modern responsive design.', 'فرۆشگایەکی تەواو لەسەر ئینتەرنێت لەگەڵ سەبەتە، پارەدان، و داشبۆردی بەڕێوەبردن.', 'PHP, MySQL, JavaScript, Tailwind', '#', '#', 1, 1),
('Task Management App', 'ئەپڵیکەیشنی بەڕێوەبردنی ئەرک', 'Collaborative project management tool with real-time updates, drag-and-drop boards, and team chat.', 'ئامرازی بەڕێوەبردنی پڕۆژە لەگەڵ نوێکردنەوەی ڕاستەوخۆ و بۆردی دراگ-ئەن-درۆپ.', 'React, Node.js, MongoDB', '#', '#', 1, 2),
('Portfolio CMS', 'CMS ی پۆرتفۆلیۆ', 'Dynamic bilingual portfolio with admin panel — the very system powering this website.', 'پۆرتفۆلیۆی دوو زمانە لەگەڵ پانێڵی بەڕێوەبردن — هەمان سیستەم کە ئەم وێبسایتە بەهێز دەکات.', 'PHP, MySQL, Tailwind CSS', '#', '#', 0, 3),
('Brand Identity Suite', 'کۆمەڵەی ناسنامەی براند', 'Complete brand identity package including logo, color palette, typography, and social media templates.', 'پاکێجی تەواوی ناسنامەی براند لەگەڵ لۆگۆ، پاڵێتی ڕەنگ، تایپۆگرافی، و قاڵبی میدیای کۆمەڵایەتی.', 'Figma, Illustrator', '#', '#', 0, 4);

-- ============================================
-- 9. Contact Messages
-- ============================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

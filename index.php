<?php
/**
 * Portfolio Frontend — Main Page
 * Bilingual (EN/KU) with dynamic content from MySQL
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db_connect.php';

$lang = getCurrentLang();
$dir = getDir();
$langAttr = getLangAttr();
$s = langSuffix(); // '_en' or '_ku'

// Fetch all data
$hero = $pdo->query("SELECT * FROM site_settings WHERE id = 1")->fetch();
$about = $pdo->query("SELECT * FROM about WHERE id = 1")->fetch();
$experiences = $pdo->query("SELECT * FROM experience ORDER BY sort_order ASC")->fetchAll();
$educations = $pdo->query("SELECT * FROM education ORDER BY sort_order ASC")->fetchAll();
$contact = $pdo->query("SELECT * FROM contact_info WHERE id = 1")->fetch();
$skills = [];
$projects = [];
$skillGroups = [];

try {
    $skills = $pdo->query("SELECT * FROM skills ORDER BY sort_order ASC")->fetchAll();
    $projects = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC")->fetchAll();
    foreach ($skills as $skill) {
        $cat = $skill['category' . $s] ?? $skill['category_en'];
        $skillGroups[$cat][] = $skill;
    }
} catch (PDOException $e) {
    // Tables may not exist yet — run upgrade.sql
}

// Nav labels
$nav = [
    'home'       => t('Home', 'سەرەتا'),
    'about'      => t('About', 'دەربارە'),
    'skills'     => t('Skills', 'شارەزایی'),
    'experience' => t('Experience', 'ئەزموون'),
    'projects'   => t('Projects', 'پڕۆژەکان'),
    'education'  => t('Education', 'خوێندن'),
    'contact'    => t('Contact', 'پەیوەندی'),
];

$navSections = [
    ['id' => 'hero', 'label' => $nav['home']],
    ['id' => 'about', 'label' => $nav['about']],
];

if (!empty($skillGroups)) {
    $navSections[] = ['id' => 'skills', 'label' => $nav['skills']];
}

$navSections[] = ['id' => 'experience', 'label' => $nav['experience']];

if (!empty($projects)) {
    $navSections[] = ['id' => 'projects', 'label' => $nav['projects']];
}

$navSections[] = ['id' => 'education', 'label' => $nav['education']];
$navSections[] = ['id' => 'contact', 'label' => $nav['contact']];
?>
<!DOCTYPE html>
<html lang="<?= $langAttr ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= sanitize($hero['title' . $s] ?? '') ?> — <?= sanitize($hero['name' . $s] ?? '') ?>">
    <meta name="theme-color" content="#E91E63">
    <meta property="og:title" content="<?= sanitize($hero['name' . $s] ?? 'Portfolio') ?>">
    <meta property="og:description" content="<?= sanitize($hero['hero_subtitle' . $s] ?? '') ?>">
    <meta property="og:type" content="website">
    <title><?= sanitize($hero['name' . $s] ?? 'Portfolio') ?> — <?= sanitize($hero['title' . $s] ?? '') ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pink: {
                            50: '#FFF0F5', 100: '#FFE4EF', 200: '#FFBDD6',
                            300: '#FF8FB8', 400: '#FF6B9D', 500: '#FF4081',
                            600: '#E91E63', 700: '#C2185B',
                        },
                        rose: { gold: '#B76E79' },
                        dark: { DEFAULT: '#2D1B2E', light: '#4A3050', card: '#3A2340' },
                    },
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        arabic: ['Vazirmatn', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= $lang === 'ku' ? 'font-arabic' : 'font-outfit' ?>">

    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="preloader-logo"><?= sanitize(mb_substr($hero['name_en'] ?? 'P', 0, 1)) ?></div>
            <div class="preloader-bar"><span></span></div>
        </div>
    </div>

    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop" aria-label="<?= t('Back to top', 'گەڕانەوە بۆ سەرەوە') ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75 7.5 18.75 12 14.25l4.5 4.5 3-3m-6-9L12 2.25 15 5.25"/></svg>
    </button>

    <!-- ========== NAVBAR ========== -->
    <nav class="navbar px-4 sm:px-6 lg:px-8 py-4" id="navbar">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Logo -->
            <a href="#hero" class="text-xl font-bold font-en" style="background: var(--gradient-pink); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                <?= sanitize($hero['name_en'] ?? 'Portfolio') ?>
            </a>

            <!-- Desktop Nav -->
            <div class="desktop-nav">
                <?php foreach ($navSections as $item): ?>
                <a href="#<?= sanitize($item['id']) ?>" class="nav-link" data-section="<?= sanitize($item['id']) ?>"><?= sanitize($item['label']) ?></a>
                <?php endforeach; ?>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Theme Toggle -->
                <button class="theme-toggle" id="themeToggle" aria-label="<?= t('Toggle theme', 'گۆڕینی ڕووکار') ?>">
                    <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
                    <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/></svg>
                </button>

                <!-- Language Switcher -->
                <div class="lang-switcher">
                    <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>">EN</a>
                    <a href="?lang=ku" class="lang-btn <?= $lang === 'ku' ? 'active' : '' ?>">کوردی</a>
                </div>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn" aria-label="<?= t('Toggle menu', 'کردنەوەی مێنیو') ?>" aria-controls="mobileMenu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
        <?php foreach ($navSections as $item): ?>
        <a href="#<?= sanitize($item['id']) ?>" class="nav-link" data-section="<?= sanitize($item['id']) ?>"><?= sanitize($item['label']) ?></a>
        <?php endforeach; ?>
    </div>

    <!-- ========== HERO SECTION ========== -->
    <section class="hero-section" id="hero">
        <canvas class="particle-canvas" id="particleCanvas"></canvas>
        <!-- Decorative shapes -->
        <div class="hero-shape hero-shape-1"></div>
        <div class="hero-shape hero-shape-2"></div>
        <div class="hero-shape hero-shape-3"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full hero-content">
            <div class="max-w-2xl">
                <p class="hero-greeting" style="animation: fadeInUp 0.8s ease forwards;">
                    <?= sanitize($hero['greeting' . $s] ?? '') ?>
                </p>
                <h1 class="hero-name" style="animation: fadeInUp 0.8s ease 0.2s both;">
                    <?= sanitize($hero['name' . $s] ?? '') ?>
                </h1>
                <p class="hero-title" style="animation: fadeInUp 0.8s ease 0.4s both;">
                    <span class="typing-text" data-text="<?= sanitize($hero['title' . $s] ?? '') ?>"></span>
                    <span class="typing-cursor"></span>
                </p>
                <p class="hero-subtitle" style="animation: fadeInUp 0.8s ease 0.6s both;">
                    <?= sanitize($hero['hero_subtitle' . $s] ?? '') ?>
                </p>
                <a href="<?= sanitize($hero['cta_link'] ?? '#about') ?>" class="hero-cta" style="animation: fadeInUp 0.8s ease 0.8s both;">
                    <?= sanitize($hero['cta_text' . $s] ?? '') ?>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <span></span>
        </div>
    </section>

    <!-- ========== ABOUT SECTION ========== -->
    <section class="about-section py-20 lg:py-28" id="about">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center reveal">
                <h2 class="section-title"><?= sanitize(dbField($about, 'title' . $s, 'name' . $s) ?: t('About Me', 'دەربارەی من')) ?></h2>
                <div class="section-divider"></div>
                <p class="section-subtitle"><?= t('Get to know me better', 'زیاتر بمناسە') ?></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Image -->
                <div class="flex justify-center reveal-left">
                    <div class="about-image-wrapper">
                        <?php $profileImg = mediaSrc($about['profile_image'] ?? ''); ?>
                        <?php if ($profileImg && mediaExists($about['profile_image'] ?? '')): ?>
                            <img src="<?= sanitize($profileImg) ?>" alt="Profile" class="about-image">
                        <?php else: ?>
                            <div class="placeholder-image">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:80px;height:80px;opacity:0.6;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Text -->
                <div class="reveal-right">
                    <p class="about-text"><?= nl2br(sanitize($about['bio' . $s] ?? '')) ?></p>

                    <div class="about-stats">
                        <div class="stat-card">
                            <span class="stat-number font-en" data-count="<?= (int)($about['stat_years'] ?? 5) ?>">0</span><span class="stat-suffix font-en">+</span>
                            <span class="stat-label"><?= t('Years Experience', 'ساڵ ئەزموون') ?></span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number font-en" data-count="<?= (int)($about['stat_projects'] ?? 50) ?>">0</span><span class="stat-suffix font-en">+</span>
                            <span class="stat-label"><?= t('Projects Done', 'پڕۆژە تەواوکراو') ?></span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number font-en" data-count="<?= (int)($about['stat_clients'] ?? 30) ?>">0</span><span class="stat-suffix font-en">+</span>
                            <span class="stat-label"><?= t('Happy Clients', 'کلایەنتی دڵخۆش') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== SKILLS SECTION ========== -->
    <?php if (!empty($skillGroups)): ?>
    <section class="skills-section py-20 lg:py-28" id="skills">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center reveal">
                <h2 class="section-title"><?= t('Skills & Expertise', 'شارەزایی و پسپۆڕی') ?></h2>
                <div class="section-divider"></div>
                <p class="section-subtitle"><?= t('Technologies I work with', 'تەکنەلۆژیاکانی کارم پێ دەکەم') ?></p>
            </div>

            <div class="skills-grid">
                <?php foreach ($skillGroups as $category => $groupSkills): ?>
                <div class="skill-category reveal">
                    <h3 class="skill-category-title"><?= sanitize($category) ?></h3>
                    <div class="skill-list">
                        <?php foreach ($groupSkills as $skill): ?>
                        <div class="skill-item">
                            <div class="skill-header">
                                <span class="skill-icon"><?= sanitize($skill['icon']) ?></span>
                                <span class="skill-name"><?= sanitize($skill['name' . $s]) ?></span>
                                <span class="skill-percent font-en" data-level="<?= (int)$skill['level'] ?>">0%</span>
                            </div>
                            <div class="skill-bar">
                                <div class="skill-bar-fill" data-level="<?= (int)$skill['level'] ?>"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========== EXPERIENCE SECTION ========== -->
    <section class="timeline-section py-20 lg:py-28" id="experience">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center reveal">
                <h2 class="section-title"><?= t('Work Experience', 'ئەزموونی کار') ?></h2>
                <div class="section-divider"></div>
                <p class="section-subtitle"><?= t('My professional journey', 'گەشتی پیشەییم') ?></p>
            </div>

            <div class="timeline">
                <?php foreach ($experiences as $i => $exp): ?>
                <div class="timeline-item reveal" style="transition-delay: <?= $i * 0.15 ?>s;">
                    <div class="timeline-dot"></div>
                    <div class="timeline-card">
                        <span class="timeline-date font-en"><?= sanitize($exp['date_start']) ?> — <?= sanitize($exp['date_end']) ?></span>
                        <h3 class="timeline-title"><?= sanitize($exp['role' . $s]) ?></h3>
                        <p class="timeline-company"><?= sanitize($exp['company' . $s]) ?></p>
                        <p class="timeline-desc"><?= sanitize($exp['description' . $s]) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ========== PROJECTS SECTION ========== -->
    <?php if (!empty($projects)): ?>
    <section class="projects-section py-20 lg:py-28" id="projects">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center reveal">
                <h2 class="section-title"><?= t('Featured Projects', 'پڕۆژە تایبەتەکان') ?></h2>
                <div class="section-divider"></div>
                <p class="section-subtitle"><?= t('A selection of my recent work', 'هەڵبژاردەیەک لە کارە نوێیەکانم') ?></p>
            </div>

            <div class="projects-grid">
                <?php foreach ($projects as $i => $project):
                    $projImg = mediaSrc($project['image'] ?? $project['thumbnail'] ?? '');
                    $projUrl = $project['project_url'] ?? $project['demo_url'] ?? '#';
                    $projTags = $project['tech_stack'] ?? $project['tags'] ?? '';
                    $projFeatured = !empty($project['featured']) || !empty($project['is_featured']);
                ?>
                <article class="project-card reveal <?= $projFeatured ? 'featured' : '' ?>" style="transition-delay: <?= $i * 0.1 ?>s;">
                    <div class="project-image-wrap">
                        <?php if ($projImg && mediaExists($project['image'] ?? $project['thumbnail'] ?? '')): ?>
                            <img src="<?= sanitize($projImg) ?>" alt="<?= sanitize($project['title' . $s]) ?>" class="project-image">
                        <?php else: ?>
                            <div class="project-placeholder">
                                <span class="project-placeholder-icon">✦</span>
                            </div>
                        <?php endif; ?>
                        <div class="project-overlay">
                            <?php if (!empty($projUrl) && $projUrl !== '#'): ?>
                            <a href="<?= sanitize($projUrl) ?>" target="_blank" rel="noopener noreferrer" class="project-link" aria-label="<?= t('View project', 'بینینی پڕۆژە') ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($project['github_url']) && $project['github_url'] !== '#'): ?>
                            <a href="<?= sanitize($project['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="project-link" aria-label="GitHub">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php if ($projFeatured): ?>
                        <span class="project-badge"><?= t('Featured', 'تایبەت') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="project-content">
                        <h3 class="project-title"><?= sanitize($project['title' . $s]) ?></h3>
                        <p class="project-desc"><?= sanitize($project['description' . $s]) ?></p>
                        <?php if (!empty($projTags)): ?>
                        <div class="project-tags">
                            <?php foreach (array_map('trim', explode(',', $projTags)) as $tag): ?>
                            <span class="project-tag font-en"><?= sanitize($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========== EDUCATION SECTION ========== -->
    <section class="timeline-section py-20 lg:py-28" id="education" style="background: var(--white);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center reveal">
                <h2 class="section-title"><?= t('Education', 'خوێندن') ?></h2>
                <div class="section-divider"></div>
                <p class="section-subtitle"><?= t('My academic background', 'پاشخانی ئەکادیمیم') ?></p>
            </div>

            <div class="timeline">
                <?php foreach ($educations as $i => $edu): ?>
                <div class="timeline-item reveal" style="transition-delay: <?= $i * 0.15 ?>s;">
                    <div class="timeline-dot"></div>
                    <div class="timeline-card">
                        <span class="timeline-date font-en"><?= sanitize($edu['date_start']) ?> — <?= sanitize($edu['date_end']) ?></span>
                        <h3 class="timeline-title"><?= sanitize($edu['degree' . $s]) ?></h3>
                        <p class="timeline-company"><?= sanitize($edu['institution' . $s]) ?></p>
                        <p class="timeline-desc"><?= sanitize($edu['description' . $s]) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ========== CONTACT SECTION ========== -->
    <section class="contact-section py-20 lg:py-28" id="contact">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center reveal">
                <h2 class="section-title" style="background: linear-gradient(135deg, #fff 0%, #FFBDD6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <?= t('Get In Touch', 'پەیوەندیمان پێوە بکە') ?>
                </h2>
                <div class="section-divider"></div>
                <p class="section-subtitle" style="color: var(--pink-200);"><?= t("Let's work together", 'با پێکەوە کار بکەین') ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <!-- Email -->
                <div class="contact-card reveal" style="transition-delay: 0s;">
                    <div class="contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                        </svg>
                    </div>
                    <span class="contact-label"><?= t('Email', 'ئیمەیڵ') ?></span>
                    <span class="contact-value">
                        <a href="mailto:<?= sanitize($contact['email'] ?? '') ?>"><?= sanitize($contact['email'] ?? '') ?></a>
                    </span>
                </div>

                <!-- Phone -->
                <div class="contact-card reveal" style="transition-delay: 0.1s;">
                    <div class="contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/>
                        </svg>
                    </div>
                    <span class="contact-label"><?= t('Phone', 'ژمارەی مۆبایل') ?></span>
                    <span class="contact-value">
                        <a href="tel:<?= sanitize($contact['phone'] ?? '') ?>" class="font-en"><?= sanitize($contact['phone'] ?? '') ?></a>
                    </span>
                </div>

                <!-- Location -->
                <div class="contact-card reveal" style="transition-delay: 0.2s;">
                    <div class="contact-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                        </svg>
                    </div>
                    <span class="contact-label"><?= t('Location', 'شوێن') ?></span>
                    <span class="contact-value"><?= sanitize($contact['location' . $s] ?? '') ?></span>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-wrap reveal">
                <h3 class="contact-form-title"><?= t('Send a Message', 'پەیامێک بنێرە') ?></h3>
                <form class="contact-form" id="contactForm">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contactName"><?= t('Your Name', 'ناوت') ?></label>
                            <input type="text" id="contactName" name="name" required placeholder="<?= t('John Doe', 'ناوی تۆ') ?>">
                        </div>
                        <div class="form-group">
                            <label for="contactEmail"><?= t('Your Email', 'ئیمەیڵەکەت') ?></label>
                            <input type="email" id="contactEmail" name="email" required placeholder="you@example.com" class="font-en">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contactMessage"><?= t('Message', 'پەیام') ?></label>
                        <textarea id="contactMessage" name="message" rows="5" required placeholder="<?= t('Tell me about your project...', 'پێم بڵێ دەربارەی پڕۆژەکەت...') ?>"></textarea>
                    </div>
                    <button type="submit" class="contact-submit">
                        <span class="submit-text"><?= t('Send Message', 'ناردنی پەیام') ?></span>
                        <span class="submit-loading hidden">
                            <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="31.4" stroke-linecap="round"/></svg>
                        </span>
                        <svg class="submit-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                    </button>
                    <div class="form-feedback" id="formFeedback"></div>
                </form>
            </div>

            <!-- Social Links -->
            <div class="social-links reveal">
                <?php if (!empty($contact['linkedin']) && $contact['linkedin'] !== '#'): ?>
                <a href="<?= sanitize($contact['linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="LinkedIn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
                <?php endif; ?>
                <?php if (!empty($contact['github']) && $contact['github'] !== '#'): ?>
                <a href="<?= sanitize($contact['github']) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="GitHub">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                </a>
                <?php endif; ?>
                <?php if (!empty($contact['twitter']) && $contact['twitter'] !== '#'): ?>
                <a href="<?= sanitize($contact['twitter']) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Twitter / X">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <?php endif; ?>
                <?php if (!empty($contact['instagram']) && $contact['instagram'] !== '#'): ?>
                <a href="<?= sanitize($contact['instagram']) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->
    <footer class="footer">
        <p>
            <?= t('Made with', 'دروستکراوە بە') ?>
            <span class="footer-heart">♥</span>
            <?= t('by', 'لەلایەن') ?>
            <strong class="font-en"><?= sanitize($hero['name_en'] ?? '') ?></strong>
            &copy; <?= date('Y') ?>
        </p>
    </footer>

    <!-- JavaScript -->
    <script>window.CSRF_TOKEN = <?= json_encode(csrf_token()) ?>;</script>
    <script src="js/main.js"></script>
</body>
</html>

<?php
/**
 * Admin — Hero Section Editor
 */
$pageTitle = 'Hero Section';
$activePage = 'hero';

require_once __DIR__ . '/../db_connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../functions.php';
    requireLogin();

    if (verify_csrf()) {
        $stmt = $pdo->prepare("UPDATE site_settings SET 
            greeting_en = ?, greeting_ku = ?,
            name_en = ?, name_ku = ?,
            title_en = ?, title_ku = ?,
            cta_text_en = ?, cta_text_ku = ?,
            cta_link = ?,
            hero_subtitle_en = ?, hero_subtitle_ku = ?
            WHERE id = 1");

        $stmt->execute([
            trim($_POST['greeting_en']),
            trim($_POST['greeting_ku']),
            trim($_POST['name_en']),
            trim($_POST['name_ku']),
            trim($_POST['title_en']),
            trim($_POST['title_ku']),
            trim($_POST['cta_text_en']),
            trim($_POST['cta_text_ku']),
            trim($_POST['cta_link']),
            trim($_POST['hero_subtitle_en']),
            trim($_POST['hero_subtitle_ku']),
        ]);

        setFlash('success', 'Hero section updated successfully!');
    } else {
        setFlash('error', 'Security token mismatch. Please try again.');
    }

    header('Location: hero.php');
    exit;
}

require_once 'header.php';

$hero = $pdo->query("SELECT * FROM site_settings WHERE id = 1")->fetch();
?>

<form method="POST" action="">
    <?= csrf_field() ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Greeting & Name</h2>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group">
                <label class="form-label">Greeting <span class="lang-tag en">EN</span></label>
                <input type="text" name="greeting_en" class="form-input" value="<?= sanitize($hero['greeting_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Greeting <span class="lang-tag ku">KU</span></label>
                <input type="text" name="greeting_ku" class="form-input" dir="rtl" value="<?= sanitize($hero['greeting_ku'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Full Name <span class="lang-tag en">EN</span></label>
                <input type="text" name="name_en" class="form-input" value="<?= sanitize($hero['name_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Full Name <span class="lang-tag ku">KU</span></label>
                <input type="text" name="name_ku" class="form-input" dir="rtl" value="<?= sanitize($hero['name_ku'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Title & Subtitle</h2>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group">
                <label class="form-label">Job Title <span class="lang-tag en">EN</span></label>
                <input type="text" name="title_en" class="form-input" value="<?= sanitize($hero['title_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Job Title <span class="lang-tag ku">KU</span></label>
                <input type="text" name="title_ku" class="form-input" dir="rtl" value="<?= sanitize($hero['title_ku'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Subtitle <span class="lang-tag en">EN</span></label>
                <textarea name="hero_subtitle_en" class="form-textarea" rows="3"><?= sanitize($hero['hero_subtitle_en'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Subtitle <span class="lang-tag ku">KU</span></label>
                <textarea name="hero_subtitle_ku" class="form-textarea" dir="rtl" rows="3"><?= sanitize($hero['hero_subtitle_ku'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Call to Action Button</h2>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group">
                <label class="form-label">Button Text <span class="lang-tag en">EN</span></label>
                <input type="text" name="cta_text_en" class="form-input" value="<?= sanitize($hero['cta_text_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Button Text <span class="lang-tag ku">KU</span></label>
                <input type="text" name="cta_text_ku" class="form-input" dir="rtl" value="<?= sanitize($hero['cta_text_ku'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Button Link (URL or #section)</label>
            <input type="text" name="cta_link" class="form-input" value="<?= sanitize($hero['cta_link'] ?? '') ?>" placeholder="#about">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        Save Changes
    </button>
</form>

<?php require_once 'footer.php'; ?>

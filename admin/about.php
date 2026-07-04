<?php
/**
 * Admin — About Section Editor
 */
$pageTitle = 'About Me';
$activePage = 'about';

require_once __DIR__ . '/../db_connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../functions.php';
    requireLogin();

    if (verify_csrf()) {
        // Handle image upload
        $profileImage = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fileType = mime_content_type($_FILES['profile_image']['tmp_name']);

            if (in_array($fileType, $allowed)) {
                $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $filename = 'profile_' . time() . '.' . $ext;
                $uploadDir = __DIR__ . '/../uploads/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadDir . $filename)) {
                    // Delete old image
                    $old = $pdo->query("SELECT profile_image FROM about WHERE id = 1")->fetchColumn();
                    if ($old && file_exists($uploadDir . $old)) {
                        unlink($uploadDir . $old);
                    }
                    $profileImage = $filename;
                }
            } else {
                setFlash('error', 'Invalid image format. Use JPG, PNG, WebP, or GIF.');
                header('Location: about.php');
                exit;
            }
        }

        $sql = "UPDATE about SET bio_en = ?, bio_ku = ?, stat_years = ?, stat_projects = ?, stat_clients = ?";
        $params = [
            trim($_POST['bio_en']),
            trim($_POST['bio_ku']),
            max(0, intval($_POST['stat_years'] ?? 5)),
            max(0, intval($_POST['stat_projects'] ?? 50)),
            max(0, intval($_POST['stat_clients'] ?? 30)),
        ];

        // Support both title_* and name_* column schemas
        $aboutCols = $pdo->query("SHOW COLUMNS FROM about")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('title_en', $aboutCols, true)) {
            $sql = "UPDATE about SET title_en = ?, title_ku = ?, bio_en = ?, bio_ku = ?, stat_years = ?, stat_projects = ?, stat_clients = ?";
            array_unshift($params, trim($_POST['title_en']), trim($_POST['title_ku']));
        } elseif (in_array('name_en', $aboutCols, true)) {
            $sql = "UPDATE about SET name_en = ?, name_ku = ?, bio_en = ?, bio_ku = ?, stat_years = ?, stat_projects = ?, stat_clients = ?";
            array_unshift($params, trim($_POST['title_en']), trim($_POST['title_ku']));
        }

        if ($profileImage) {
            $sql .= ", profile_image = ?";
            $params[] = $profileImage;
        }

        $sql .= " WHERE id = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        setFlash('success', 'About section updated successfully!');
    } else {
        setFlash('error', 'Security token mismatch. Please try again.');
    }

    header('Location: about.php');
    exit;
}

require_once 'header.php';

$about = $pdo->query("SELECT * FROM about WHERE id = 1")->fetch();
?>

<form method="POST" action="" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Section Title</h2>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group">
                <label class="form-label">Title <span class="lang-tag en">EN</span></label>
                <input type="text" name="title_en" class="form-input" value="<?= sanitize($about['title_en'] ?? $about['name_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Title <span class="lang-tag ku">KU</span></label>
                <input type="text" name="title_ku" class="form-input" dir="rtl" value="<?= sanitize($about['title_ku'] ?? $about['name_ku'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Profile Image</h2>
        </div>
        <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
            <?php $profileSrc = mediaSrc($about['profile_image'] ?? ''); ?>
            <?php if ($profileSrc): ?>
                <img src="../<?= sanitize($profileSrc) ?>" alt="Current Profile" style="width:100px;height:100px;object-fit:cover;border-radius:12px;border:2px solid var(--admin-pink-200);">
            <?php endif; ?>
            <div class="form-group" style="flex:1;margin-bottom:0;">
                <label class="form-label">Upload New Image (JPG, PNG, WebP, GIF)</label>
                <input type="file" name="profile_image" class="form-input" accept="image/*">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Stats (About Section)</h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
            <div class="form-group">
                <label class="form-label">Years Experience</label>
                <input type="number" name="stat_years" class="form-input" min="0" value="<?= (int)($about['stat_years'] ?? 5) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Projects Done</label>
                <input type="number" name="stat_projects" class="form-input" min="0" value="<?= (int)($about['stat_projects'] ?? 50) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Happy Clients</label>
                <input type="number" name="stat_clients" class="form-input" min="0" value="<?= (int)($about['stat_clients'] ?? 30) ?>">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Bio / Description</h2>
        </div>
        <div class="form-group">
            <label class="form-label">Bio <span class="lang-tag en">EN</span></label>
            <textarea name="bio_en" class="form-textarea" rows="8"><?= sanitize($about['bio_en'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Bio <span class="lang-tag ku">KU</span></label>
            <textarea name="bio_ku" class="form-textarea" dir="rtl" rows="8"><?= sanitize($about['bio_ku'] ?? '') ?></textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        Save Changes
    </button>
</form>

<?php require_once 'footer.php'; ?>

<?php
/**
 * Admin — Skills CRUD
 */
$pageTitle = 'Skills';
$activePage = 'skills';

require_once __DIR__ . '/../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../functions.php';
    requireLogin();

    if (!verify_csrf()) {
        setFlash('error', 'Security token mismatch.');
        header('Location: skills.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $data = [
            trim($_POST['name_en']),
            trim($_POST['name_ku']),
            trim($_POST['category_en']),
            trim($_POST['category_ku']),
            min(100, max(0, intval($_POST['level'] ?? 80))),
            trim($_POST['icon'] ?? '⚡'),
            intval($_POST['sort_order'] ?? 0),
        ];

        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO skills (name_en, name_ku, category_en, category_ku, level, icon, sort_order) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute($data);
            setFlash('success', 'Skill added successfully!');
        } else {
            $data[] = intval($_POST['id']);
            $stmt = $pdo->prepare("UPDATE skills SET name_en=?, name_ku=?, category_en=?, category_ku=?, level=?, icon=?, sort_order=? WHERE id=?");
            $stmt->execute($data);
            setFlash('success', 'Skill updated successfully!');
        }
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
        $stmt->execute([intval($_POST['delete_id'])]);
        setFlash('success', 'Skill deleted.');
    }

    header('Location: skills.php');
    exit;
}

require_once 'header.php';

$skills = $pdo->query("SELECT * FROM skills ORDER BY sort_order ASC")->fetchAll();
$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editItem = $stmt->fetch();
}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title"><?= $editItem ? 'Edit Skill' : 'Add New Skill' ?></h2>
        <?php if ($editItem): ?>
            <a href="skills.php" class="btn btn-secondary btn-sm">✕ Cancel Edit</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="<?= $editItem ? 'update' : 'create' ?>">
        <?php if ($editItem): ?>
            <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group">
                <label class="form-label">Name <span class="lang-tag en">EN</span></label>
                <input type="text" name="name_en" class="form-input" required value="<?= sanitize($editItem['name_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Name <span class="lang-tag ku">KU</span></label>
                <input type="text" name="name_ku" class="form-input" dir="rtl" required value="<?= sanitize($editItem['name_ku'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Category <span class="lang-tag en">EN</span></label>
                <input type="text" name="category_en" class="form-input" required value="<?= sanitize($editItem['category_en'] ?? 'General') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Category <span class="lang-tag ku">KU</span></label>
                <input type="text" name="category_ku" class="form-input" dir="rtl" required value="<?= sanitize($editItem['category_ku'] ?? 'گشتی') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Level (0–100)</label>
                <input type="number" name="level" class="form-input" min="0" max="100" value="<?= (int)($editItem['level'] ?? 80) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Icon (emoji)</label>
                <input type="text" name="icon" class="form-input" maxlength="4" value="<?= sanitize($editItem['icon'] ?? '⚡') ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-input" style="max-width:120px;" value="<?= (int)($editItem['sort_order'] ?? 0) ?>">
        </div>

        <button type="submit" class="btn btn-primary">
            <?= $editItem ? 'Update' : 'Add' ?> Skill
        </button>
    </form>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">All Skills (<?= count($skills) ?>)</h2>
    </div>

    <?php if (empty($skills)): ?>
        <p style="color:var(--admin-dark-light);opacity:0.6;text-align:center;padding:2rem;">No skills yet.</p>
    <?php else: ?>
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($skills as $skill): ?>
                <tr>
                    <td><?= $skill['sort_order'] ?></td>
                    <td><?= sanitize($skill['icon']) ?></td>
                    <td><?= sanitize($skill['name_en']) ?></td>
                    <td><?= sanitize($skill['category_en']) ?></td>
                    <td><?= (int)$skill['level'] ?>%</td>
                    <td>
                        <div class="table-actions">
                            <a href="skills.php?edit=<?= $skill['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <button type="button" class="btn btn-danger btn-sm" data-delete-id="<?= $skill['id'] ?>">Delete</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <h3 class="modal-title">Delete Skill?</h3>
        <p class="modal-text">This action cannot be undone.</p>
        <form method="POST" action="" id="deleteForm">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="delete_id" value="">
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>

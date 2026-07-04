<?php
/**
 * Admin — Experience CRUD
 */
$pageTitle = 'Work Experience';
$activePage = 'experience';

require_once __DIR__ . '/../db_connect.php';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../functions.php';
    requireLogin();

    if (!verify_csrf()) {
        setFlash('error', 'Security token mismatch.');
        header('Location: experience.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $data = [
            trim($_POST['company_en']),
            trim($_POST['company_ku']),
            trim($_POST['role_en']),
            trim($_POST['role_ku']),
            trim($_POST['date_start']),
            trim($_POST['date_end']),
            trim($_POST['description_en']),
            trim($_POST['description_ku']),
            intval($_POST['sort_order'] ?? 0),
        ];

        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO experience (company_en, company_ku, role_en, role_ku, date_start, date_end, description_en, description_ku, sort_order) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->execute($data);
            setFlash('success', 'Experience entry added successfully!');
        } else {
            $data[] = intval($_POST['id']);
            $stmt = $pdo->prepare("UPDATE experience SET company_en=?, company_ku=?, role_en=?, role_ku=?, date_start=?, date_end=?, description_en=?, description_ku=?, sort_order=? WHERE id=?");
            $stmt->execute($data);
            setFlash('success', 'Experience entry updated successfully!');
        }
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM experience WHERE id = ?");
        $stmt->execute([intval($_POST['delete_id'])]);
        setFlash('success', 'Experience entry deleted.');
    }

    header('Location: experience.php');
    exit;
}

require_once 'header.php';

$experiences = $pdo->query("SELECT * FROM experience ORDER BY sort_order ASC")->fetchAll();
$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editItem = $stmt->fetch();
}
?>

<!-- Add / Edit Form -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title"><?= $editItem ? 'Edit Experience' : 'Add New Experience' ?></h2>
        <?php if ($editItem): ?>
            <a href="experience.php" class="btn btn-secondary btn-sm">✕ Cancel Edit</a>
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
                <label class="form-label">Role / Position <span class="lang-tag en">EN</span></label>
                <input type="text" name="role_en" class="form-input" required value="<?= sanitize($editItem['role_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Role / Position <span class="lang-tag ku">KU</span></label>
                <input type="text" name="role_ku" class="form-input" dir="rtl" required value="<?= sanitize($editItem['role_ku'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Company <span class="lang-tag en">EN</span></label>
                <input type="text" name="company_en" class="form-input" required value="<?= sanitize($editItem['company_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Company <span class="lang-tag ku">KU</span></label>
                <input type="text" name="company_ku" class="form-input" dir="rtl" required value="<?= sanitize($editItem['company_ku'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Start Date</label>
                <input type="text" name="date_start" class="form-input" placeholder="e.g. 2022" required value="<?= sanitize($editItem['date_start'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">End Date</label>
                <input type="text" name="date_end" class="form-input" placeholder="e.g. Present" required value="<?= sanitize($editItem['date_end'] ?? 'Present') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Description <span class="lang-tag en">EN</span></label>
                <textarea name="description_en" class="form-textarea" rows="3"><?= sanitize($editItem['description_en'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Description <span class="lang-tag ku">KU</span></label>
                <textarea name="description_ku" class="form-textarea" dir="rtl" rows="3"><?= sanitize($editItem['description_ku'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Sort Order (lower = first)</label>
            <input type="number" name="sort_order" class="form-input" style="max-width:120px;" value="<?= sanitize($editItem['sort_order'] ?? '0') ?>">
        </div>

        <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            <?= $editItem ? 'Update' : 'Add' ?> Experience
        </button>
    </form>
</div>

<!-- List -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">All Experience (<?= count($experiences) ?>)</h2>
    </div>

    <?php if (empty($experiences)): ?>
        <p style="color:var(--admin-dark-light);opacity:0.6;text-align:center;padding:2rem;">No experience entries yet.</p>
    <?php else: ?>
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Role</th>
                    <th>Company</th>
                    <th>Period</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($experiences as $exp): ?>
                <tr>
                    <td><?= $exp['sort_order'] ?></td>
                    <td><?= sanitize($exp['role_en']) ?></td>
                    <td><?= sanitize($exp['company_en']) ?></td>
                    <td><?= sanitize($exp['date_start']) ?> — <?= sanitize($exp['date_end']) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="experience.php?edit=<?= $exp['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <button type="button" class="btn btn-danger btn-sm" data-delete-id="<?= $exp['id'] ?>">Delete</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <div class="modal-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
        </div>
        <h3 class="modal-title">Delete Experience?</h3>
        <p class="modal-text">This action cannot be undone. Are you sure?</p>
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

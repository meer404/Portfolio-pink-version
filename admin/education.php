<?php
/**
 * Admin — Education CRUD
 */
$pageTitle = 'Education';
$activePage = 'education';

require_once __DIR__ . '/../db_connect.php';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../functions.php';
    requireLogin();

    if (!verify_csrf()) {
        setFlash('error', 'Security token mismatch.');
        header('Location: education.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $data = [
            trim($_POST['institution_en']),
            trim($_POST['institution_ku']),
            trim($_POST['degree_en']),
            trim($_POST['degree_ku']),
            trim($_POST['date_start']),
            trim($_POST['date_end']),
            trim($_POST['description_en']),
            trim($_POST['description_ku']),
            intval($_POST['sort_order'] ?? 0),
        ];

        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO education (institution_en, institution_ku, degree_en, degree_ku, date_start, date_end, description_en, description_ku, sort_order) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->execute($data);
            setFlash('success', 'Education entry added successfully!');
        } else {
            $data[] = intval($_POST['id']);
            $stmt = $pdo->prepare("UPDATE education SET institution_en=?, institution_ku=?, degree_en=?, degree_ku=?, date_start=?, date_end=?, description_en=?, description_ku=?, sort_order=? WHERE id=?");
            $stmt->execute($data);
            setFlash('success', 'Education entry updated successfully!');
        }
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM education WHERE id = ?");
        $stmt->execute([intval($_POST['delete_id'])]);
        setFlash('success', 'Education entry deleted.');
    }

    header('Location: education.php');
    exit;
}

require_once 'header.php';

$educations = $pdo->query("SELECT * FROM education ORDER BY sort_order ASC")->fetchAll();
$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM education WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editItem = $stmt->fetch();
}
?>

<!-- Add / Edit Form -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title"><?= $editItem ? 'Edit Education' : 'Add New Education' ?></h2>
        <?php if ($editItem): ?>
            <a href="education.php" class="btn btn-secondary btn-sm">✕ Cancel Edit</a>
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
                <label class="form-label">Degree / Certificate <span class="lang-tag en">EN</span></label>
                <input type="text" name="degree_en" class="form-input" required value="<?= sanitize($editItem['degree_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Degree / Certificate <span class="lang-tag ku">KU</span></label>
                <input type="text" name="degree_ku" class="form-input" dir="rtl" required value="<?= sanitize($editItem['degree_ku'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Institution <span class="lang-tag en">EN</span></label>
                <input type="text" name="institution_en" class="form-input" required value="<?= sanitize($editItem['institution_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Institution <span class="lang-tag ku">KU</span></label>
                <input type="text" name="institution_ku" class="form-input" dir="rtl" required value="<?= sanitize($editItem['institution_ku'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Start Date</label>
                <input type="text" name="date_start" class="form-input" placeholder="e.g. 2018" required value="<?= sanitize($editItem['date_start'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">End Date</label>
                <input type="text" name="date_end" class="form-input" placeholder="e.g. 2022" required value="<?= sanitize($editItem['date_end'] ?? '') ?>">
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
            <?= $editItem ? 'Update' : 'Add' ?> Education
        </button>
    </form>
</div>

<!-- List -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">All Education (<?= count($educations) ?>)</h2>
    </div>

    <?php if (empty($educations)): ?>
        <p style="color:var(--admin-dark-light);opacity:0.6;text-align:center;padding:2rem;">No education entries yet.</p>
    <?php else: ?>
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Degree</th>
                    <th>Institution</th>
                    <th>Period</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($educations as $edu): ?>
                <tr>
                    <td><?= $edu['sort_order'] ?></td>
                    <td><?= sanitize($edu['degree_en']) ?></td>
                    <td><?= sanitize($edu['institution_en']) ?></td>
                    <td><?= sanitize($edu['date_start']) ?> — <?= sanitize($edu['date_end']) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="education.php?edit=<?= $edu['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <button type="button" class="btn btn-danger btn-sm" data-delete-id="<?= $edu['id'] ?>">Delete</button>
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
        <h3 class="modal-title">Delete Education?</h3>
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

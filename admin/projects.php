<?php
/**
 * Admin — Projects CRUD (compatible with legacy DB schema)
 */
$pageTitle = 'Projects';
$activePage = 'projects';

require_once __DIR__ . '/../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../functions.php';
    requireLogin();

    if (!verify_csrf()) {
        setFlash('error', 'Security token mismatch.');
        header('Location: projects.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);
            if (in_array($fileType, $allowed)) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageName = 'project_' . time() . '.' . $ext;
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName) && $action === 'update') {
                    $old = $pdo->prepare("SELECT thumbnail FROM projects WHERE id = ?");
                    $old->execute([intval($_POST['id'])]);
                    $oldImg = basename((string) $old->fetchColumn());
                    if ($oldImg && file_exists($uploadDir . $oldImg)) unlink($uploadDir . $oldImg);
                }
            }
        }

        $data = [
            trim($_POST['title_en']),
            trim($_POST['title_ku']),
            trim($_POST['description_en']),
            trim($_POST['description_ku']),
            trim($_POST['tech_stack']),
            trim($_POST['project_url']),
            trim($_POST['github_url']),
            isset($_POST['featured']) ? 1 : 0,
            intval($_POST['sort_order'] ?? 0),
        ];

        if ($action === 'create') {
            array_splice($data, 4, 0, [$imageName ?? '']);
            $stmt = $pdo->prepare("INSERT INTO projects (title_en, title_ku, description_en, description_ku, thumbnail, tags, demo_url, github_url, is_featured, sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute($data);
            setFlash('success', 'Project added successfully!');
        } else {
            $id = intval($_POST['id']);
            if ($imageName) {
                $data[] = $imageName;
                $data[] = $id;
                $stmt = $pdo->prepare("UPDATE projects SET title_en=?, title_ku=?, description_en=?, description_ku=?, tags=?, demo_url=?, github_url=?, is_featured=?, sort_order=?, thumbnail=? WHERE id=?");
            } else {
                $data[] = $id;
                $stmt = $pdo->prepare("UPDATE projects SET title_en=?, title_ku=?, description_en=?, description_ku=?, tags=?, demo_url=?, github_url=?, is_featured=?, sort_order=? WHERE id=?");
            }
            $stmt->execute($data);
            setFlash('success', 'Project updated successfully!');
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['delete_id']);
        $stmt = $pdo->prepare("SELECT thumbnail FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $img = basename((string) $stmt->fetchColumn());
        if ($img && file_exists(__DIR__ . '/../uploads/' . $img)) unlink(__DIR__ . '/../uploads/' . $img);
        $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
        setFlash('success', 'Project deleted.');
    }

    header('Location: projects.php');
    exit;
}

require_once 'header.php';

$projects = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC")->fetchAll();
$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editItem = $stmt->fetch();
}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title"><?= $editItem ? 'Edit Project' : 'Add New Project' ?></h2>
        <?php if ($editItem): ?>
            <a href="projects.php" class="btn btn-secondary btn-sm">✕ Cancel Edit</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="<?= $editItem ? 'update' : 'create' ?>">
        <?php if ($editItem): ?>
            <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group">
                <label class="form-label">Title <span class="lang-tag en">EN</span></label>
                <input type="text" name="title_en" class="form-input" required value="<?= sanitize($editItem['title_en'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Title <span class="lang-tag ku">KU</span></label>
                <input type="text" name="title_ku" class="form-input" dir="rtl" required value="<?= sanitize($editItem['title_ku'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Description <span class="lang-tag en">EN</span></label>
                <textarea name="description_en" class="form-textarea" rows="3"><?= sanitize($editItem['description_en'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Description <span class="lang-tag ku">KU</span></label>
                <textarea name="description_ku" class="form-textarea" dir="rtl" rows="3"><?= sanitize($editItem['description_ku'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Tech Stack (comma-separated)</label>
                <input type="text" name="tech_stack" class="form-input" placeholder="PHP, MySQL, React" value="<?= sanitize($editItem['tags'] ?? $editItem['tech_stack'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Project Image</label>
                <?php $thumb = mediaSrc($editItem['thumbnail'] ?? $editItem['image'] ?? ''); ?>
                <?php if ($thumb): ?>
                    <img src="../<?= sanitize($thumb) ?>" alt="" style="width:80px;height:50px;object-fit:cover;border-radius:8px;margin-bottom:0.5rem;display:block;">
                <?php endif; ?>
                <input type="file" name="image" class="form-input" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Project URL</label>
                <input type="url" name="project_url" class="form-input" value="<?= sanitize($editItem['demo_url'] ?? $editItem['project_url'] ?? '#') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">GitHub URL</label>
                <input type="url" name="github_url" class="form-input" value="<?= sanitize($editItem['github_url'] ?? '#') ?>">
            </div>
        </div>

        <div style="display:flex;gap:2rem;align-items:center;margin-bottom:1rem;">
            <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                <input type="checkbox" name="featured" <?= !empty($editItem['is_featured']) || !empty($editItem['featured']) ? 'checked' : '' ?>>
                Featured project
            </label>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-input" style="max-width:120px;" value="<?= (int)($editItem['sort_order'] ?? 0) ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><?= $editItem ? 'Update' : 'Add' ?> Project</button>
    </form>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">All Projects (<?= count($projects) ?>)</h2>
    </div>

    <?php if (empty($projects)): ?>
        <p style="color:var(--admin-dark-light);opacity:0.6;text-align:center;padding:2rem;">No projects yet.</p>
    <?php else: ?>
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Tech</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $proj): ?>
                <tr>
                    <td><?= $proj['sort_order'] ?></td>
                    <td><?= sanitize($proj['title_en']) ?></td>
                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= sanitize($proj['tags'] ?? $proj['tech_stack'] ?? '') ?></td>
                    <td><?= (!empty($proj['is_featured']) || !empty($proj['featured'])) ? '★' : '—' ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="projects.php?edit=<?= $proj['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <button type="button" class="btn btn-danger btn-sm" data-delete-id="<?= $proj['id'] ?>">Delete</button>
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
        <h3 class="modal-title">Delete Project?</h3>
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

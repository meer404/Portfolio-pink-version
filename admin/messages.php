<?php
/**
 * Admin — Contact Messages Inbox
 */
$pageTitle = 'Messages';
$activePage = 'messages';

require_once __DIR__ . '/../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../functions.php';
    requireLogin();

    if (!verify_csrf()) {
        setFlash('error', 'Security token mismatch.');
        header('Location: messages.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'mark_read') {
        $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([intval($_POST['id'])]);
        setFlash('success', 'Marked as read.');
    }

    if ($action === 'delete') {
        $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([intval($_POST['delete_id'])]);
        setFlash('success', 'Message deleted.');
    }

    header('Location: messages.php');
    exit;
}

require_once 'header.php';

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
$unread = array_filter($messages, fn($m) => !$m['is_read']);
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Inbox (<?= count($messages) ?><?= count($unread) ? ' — ' . count($unread) . ' unread' : '' ?>)</h2>
    </div>

    <?php if (empty($messages)): ?>
        <p style="color:var(--admin-dark-light);opacity:0.6;text-align:center;padding:2rem;">No messages yet.</p>
    <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:1rem;">
            <?php foreach ($messages as $msg): ?>
            <div style="background:<?= $msg['is_read'] ? 'var(--admin-white)' : 'var(--admin-pink-50)' ?>;border:1px solid var(--admin-pink-100);border-radius:12px;padding:1.25rem 1.5rem;<?= !$msg['is_read'] ? 'border-left:4px solid var(--admin-pink-500);' : '' ?>">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;margin-bottom:0.75rem;">
                    <div>
                        <strong style="font-size:1.05rem;"><?= sanitize($msg['name']) ?></strong>
                        <span style="color:var(--admin-dark-light);margin-left:0.75rem;font-size:0.9rem;">&lt;<?= sanitize($msg['email']) ?>&gt;</span>
                    </div>
                    <span style="font-size:0.8rem;color:var(--admin-dark-light);opacity:0.7;"><?= date('M j, Y g:i A', strtotime($msg['created_at'])) ?></span>
                </div>
                <p style="color:var(--admin-dark-light);line-height:1.7;white-space:pre-wrap;margin-bottom:1rem;"><?= sanitize($msg['message']) ?></p>
                <div style="display:flex;gap:0.5rem;">
                    <?php if (!$msg['is_read']): ?>
                    <form method="POST" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="mark_read">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <button type="submit" class="btn btn-secondary btn-sm">Mark Read</button>
                    </form>
                    <?php endif; ?>
                    <button type="button" class="btn btn-danger btn-sm" data-delete-id="<?= $msg['id'] ?>">Delete</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <h3 class="modal-title">Delete Message?</h3>
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

<?php
$title = "Admin - Utilisateurs";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();

$pdo = db();
$me = current_user();

// Actions POST (CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($id === (int)$me['id']) {
        header('Location: /lol-portal/admin/users.php?err=self');
        exit;
    }

    if ($action === 'promote') {
        $st = $pdo->prepare("UPDATE users SET role='admin' WHERE id=?");
        $st->execute([$id]);
    } elseif ($action === 'demote') {
        $st = $pdo->prepare("UPDATE users SET role='user' WHERE id=?");
        $st->execute([$id]);
    } elseif ($action === 'delete') {
        $st = $pdo->prepare("DELETE FROM users WHERE id=?");
        $st->execute([$id]);
    }

    header('Location: /lol-portal/admin/users.php');
    exit;
}

$users = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();
$err = $_GET['err'] ?? '';
?>

<div class="card">
  <h1>Gestion des utilisateurs</h1>
  <p class="muted"><a href="/lol-portal/admin/dashboard.php">← Dashboard</a></p>

  <?php if ($err === 'self'): ?>
    <div class="flash flash--error">Action refusée : tu ne peux pas modifier ton propre compte.</div>
  <?php endif; ?>

  <table class="table">
    <thead>
      <tr>
        <th>ID</th><th>Username</th><th>Email</th><th>Rôle</th><th>Créé le</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['role']) ?></td>
          <td><?= htmlspecialchars($u['created_at']) ?></td>
          <td>
            <?php if ((int)$u['id'] === (int)$me['id']): ?>
              <span class="muted">(toi)</span>
            <?php else: ?>
              <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <form method="post">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                  <?php if ($u['role'] !== 'admin'): ?>
                    <input type="hidden" name="action" value="promote">
                    <button class="btn" type="submit">Promote admin</button>
                  <?php else: ?>
                    <input type="hidden" name="action" value="demote">
                    <button class="btn" type="submit">Demote user</button>
                  <?php endif; ?>
                </form>

                <form method="post" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                  <input type="hidden" name="action" value="delete">
                  <button class="btn btn--ghost" type="submit">Supprimer</button>
                </form>
              </div>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();

$pdo = db();
$me = current_user();

// Actions POST (CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    // safety check to prevent self-demote or self-delete
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
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Admin - Utilisateurs</title></head>
<body>
<h1>Gestion des utilisateurs</h1>
<p><a href="/lol-portal/admin/dashboard.php">← Dashboard</a></p>

<?php if ($err === 'self'): ?>
  <p style="color:red;">Action refusée : tu ne peux pas modifier ton propre compte.</p>
<?php endif; ?>

<table border="1" cellpadding="6" cellspacing="0">
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
            (toi)
          <?php else: ?>
            <form method="post" style="display:inline;">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
              <?php if ($u['role'] !== 'admin'): ?>
                <input type="hidden" name="action" value="promote">
                <button type="submit">Promote admin</button>
              <?php else: ?>
                <input type="hidden" name="action" value="demote">
                <button type="submit">Demote user</button>
              <?php endif; ?>
            </form>

            <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button type="submit">Supprimer</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>

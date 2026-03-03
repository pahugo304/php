<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$pdo = db();

$mode = $_GET['mode'] ?? 'list'; // list | create | edit
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$errors = [];
$name = $_POST['name'] ?? '';
$type = $_POST['type'] ?? '';
$description = $_POST['description'] ?? '';
$image_url = $_POST['image_url'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $gid = (int)($_POST['id'] ?? 0);
        // delete achievements first (FK safe)
        $st = $pdo->prepare("DELETE FROM achievements WHERE game_id=?");
        $st->execute([$gid]);

        $st = $pdo->prepare("DELETE FROM games WHERE id=?");
        $st->execute([$gid]);

        header('Location: /lol-portal/admin/games.php');
        exit;
    }

    // create / update
    if (trim($name) === '' || strlen($name) < 2) $errors[] = "Nom invalide.";
    if (trim($type) === '' || strlen($type) < 2) $errors[] = "Type invalide.";
    if (trim($description) === '' || strlen($description) < 10) $errors[] = "Description min 10 caractères.";

    if (!$errors) {
        if ($action === 'create') {
            $st = $pdo->prepare("INSERT INTO games (name, type, description, image_url) VALUES (?, ?, ?, ?)");
            $st->execute([$name, $type, $description, $image_url]);
        } elseif ($action === 'update') {
            $gid = (int)($_POST['id'] ?? 0);
            $st = $pdo->prepare("UPDATE games SET name=?, type=?, description=?, image_url=? WHERE id=?");
            $st->execute([$name, $type, $description, $image_url, $gid]);
        }
        header('Location: /lol-portal/admin/games.php');
        exit;
    }
}

$editing = null;
if ($mode === 'edit' && $id > 0) {
    $st = $pdo->prepare("SELECT * FROM games WHERE id=?");
    $st->execute([$id]);
    $editing = $st->fetch();
    if ($editing) {
        $name = $name ?: $editing['name'];
        $type = $type ?: $editing['type'];
        $description = $description ?: $editing['description'];
        $image_url = $image_url ?: ($editing['image_url'] ?? '');
    }
}

$games = $pdo->query("SELECT id, name, type, created_at FROM games ORDER BY created_at DESC")->fetchAll();

site_header('Admin - Jeux');
?>

<section class="card">
  <div class="row row--between">
    <h1>Gestion des jeux</h1>
    <div class="row">
      <a class="btn btn--ghost" href="/lol-portal/admin/dashboard.php">← Dashboard</a>
      <a class="btn" href="/lol-portal/admin/games.php?mode=create">+ Nouveau jeu</a>
    </div>
  </div>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert--danger"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <?php if ($mode === 'create' || ($mode === 'edit' && $editing)): ?>
    <div class="card card--inner">
      <h2><?= $mode === 'create' ? 'Créer un jeu' : 'Modifier le jeu' ?></h2>
      <form method="post" class="form">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="hidden" name="action" value="<?= $mode === 'create' ? 'create' : 'update' ?>">
        <?php if ($mode === 'edit'): ?>
          <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
        <?php endif; ?>

        <label>Nom</label>
        <input name="name" value="<?= htmlspecialchars($name) ?>" required>

        <label>Type</label>
        <input name="type" value="<?= htmlspecialchars($type) ?>" required>

        <label>Description</label>
        <textarea name="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>

        <label>Image URL (optionnel)</label>
        <input name="image_url" value="<?= htmlspecialchars($image_url) ?>">

        <div class="row">
          <button class="btn" type="submit">Enregistrer</button>
          <a class="btn btn--ghost" href="/lol-portal/admin/games.php">Annuler</a>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <div class="tableWrap">
    <table class="table">
      <thead>
      <tr>
        <th>ID</th><th>Nom</th><th>Type</th><th>Créé le</th><th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($games as $g): ?>
        <tr>
          <td><?= (int)$g['id'] ?></td>
          <td><?= htmlspecialchars($g['name']) ?></td>
          <td><span class="badge"><?= htmlspecialchars($g['type']) ?></span></td>
          <td><?= htmlspecialchars($g['created_at']) ?></td>
          <td>
            <a class="btn btn--small" href="/lol-portal/admin/games.php?mode=edit&id=<?= (int)$g['id'] ?>">Edit</a>

            <form method="post" class="inline" onsubmit="return confirm('Supprimer ce jeu ? (supprime aussi ses succès)');">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
              <button class="btn btn--small btn--danger" type="submit">Delete</button>
            </form>

            <a class="btn btn--small btn--ghost" href="/lol-portal/games.php?id=<?= (int)$g['id'] ?>">Voir</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php site_footer(); ?>
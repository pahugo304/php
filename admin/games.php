<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$pdo = db();

$errors = [];
$mode = $_GET['mode'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Handle POST create/update/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');

        if ($name === '') $errors[] = "Nom requis.";
        if ($type === '') $errors[] = "Type requis.";
        if ($description === '') $errors[] = "Description requise.";

        if (!$errors) {
            if ($action === 'create') {
                $st = $pdo->prepare("INSERT INTO games (name, type, description, image_url) VALUES (?, ?, ?, ?)");
                $st->execute([$name, $type, $description, $image_url ?: null]);
            } else {
                $gid = (int)($_POST['id'] ?? 0);
                $st = $pdo->prepare("UPDATE games SET name=?, type=?, description=?, image_url=? WHERE id=?");
                $st->execute([$name, $type, $description, $image_url ?: null, $gid]);
            }
            header('Location: /lol-portal/admin/games.php');
            exit;
        } else {
            $mode = ($action === 'create') ? 'create' : 'edit';
            $id = (int)($_POST['id'] ?? 0);
        }
    }

    if ($action === 'delete') {
        $gid = (int)($_POST['id'] ?? 0);
        $st = $pdo->prepare("DELETE FROM games WHERE id=?");
        $st->execute([$gid]);
        header('Location: /lol-portal/admin/games.php');
        exit;
    }
}

// Load edit game
$game = null;
if ($mode === 'edit' && $id > 0) {
    $st = $pdo->prepare("SELECT * FROM games WHERE id=?");
    $st->execute([$id]);
    $game = $st->fetch();
    if (!$game) {
        header('Location: /lol-portal/admin/games.php');
        exit;
    }
}

$games = $pdo->query("SELECT id, name, type, created_at FROM games ORDER BY created_at DESC")->fetchAll();

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES); }
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Admin - Jeux</title></head>
<body>
<h1>Gestion des jeux</h1>
<p><a href="/lol-portal/admin/dashboard.php">← Dashboard</a></p>

<p><a href="/lol-portal/admin/games.php?mode=create">+ Ajouter un jeu</a></p>

<?php foreach ($errors as $e): ?>
  <p style="color:red;"><?= h($e) ?></p>
<?php endforeach; ?>

<?php if ($mode === 'create' || $mode === 'edit'): ?>
  <?php
    $isEdit = ($mode === 'edit');
    $valName = $isEdit ? ($game['name'] ?? '') : ($_POST['name'] ?? '');
    $valType = $isEdit ? ($game['type'] ?? '') : ($_POST['type'] ?? '');
    $valDesc = $isEdit ? ($game['description'] ?? '') : ($_POST['description'] ?? '');
    $valImg  = $isEdit ? ($game['image_url'] ?? '') : ($_POST['image_url'] ?? '');
  ?>
  <h2><?= $isEdit ? "Modifier jeu #".(int)$game['id'] : "Créer un jeu" ?></h2>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'create' ?>">
    <?php if ($isEdit): ?>
      <input type="hidden" name="id" value="<?= (int)$game['id'] ?>">
    <?php endif; ?>

    <label>Nom</label><br>
    <input name="name" value="<?= h((string)$valName) ?>"><br><br>

    <label>Type</label><br>
    <input name="type" value="<?= h((string)$valType) ?>"><br><br>

    <label>Description</label><br>
    <textarea name="description" rows="5" cols="60"><?= h((string)$valDesc) ?></textarea><br><br>

    <label>Image URL (optionnel)</label><br>
    <input name="image_url" value="<?= h((string)$valImg) ?>"><br><br>

    <button type="submit"><?= $isEdit ? 'Enregistrer' : 'Créer' ?></button>
    <a href="/lol-portal/admin/games.php">Annuler</a>
  </form>
  <hr>
<?php endif; ?>

<h2>Liste des jeux</h2>
<table border="1" cellpadding="6" cellspacing="0">
  <thead>
    <tr><th>ID</th><th>Nom</th><th>Type</th><th>Créé le</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($games as $g): ?>
      <tr>
        <td><?= (int)$g['id'] ?></td>
        <td><?= h($g['name']) ?></td>
        <td><?= h($g['type']) ?></td>
        <td><?= h($g['created_at']) ?></td>
        <td>
          <a href="/lol-portal/admin/games.php?mode=edit&id=<?= (int)$g['id'] ?>">Modifier</a>

          <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer ce jeu ? (et ses succès)');">
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
            <button type="submit">Supprimer</button>
          </form>

          <a href="/lol-portal/admin/achievements.php?game_id=<?= (int)$g['id'] ?>">Succès</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>

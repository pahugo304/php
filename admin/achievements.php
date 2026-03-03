<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$pdo = db();

$game_id = (int)($_GET['game_id'] ?? 0);
if ($game_id <= 0) {
    header('Location: /lol-portal/admin/games.php');
    exit;
}

$gameSt = $pdo->prepare("SELECT id, name FROM games WHERE id=?");
$gameSt->execute([$game_id]);
$game = $gameSt->fetch();
if (!$game) {
    header('Location: /lol-portal/admin/games.php');
    exit;
}

$errors = [];
$mode = $_GET['mode'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $points = (int)($_POST['points'] ?? 10);

        if ($title === '') $errors[] = "Titre requis.";
        if ($description === '') $errors[] = "Description requise.";
        if ($points < 0) $errors[] = "Points invalides.";

        if (!$errors) {
            if ($action === 'create') {
                $st = $pdo->prepare("INSERT INTO achievements (game_id, title, description, points) VALUES (?, ?, ?, ?)");
                $st->execute([$game_id, $title, $description, $points]);
            } else {
                $aid = (int)($_POST['id'] ?? 0);
                $st = $pdo->prepare("UPDATE achievements SET title=?, description=?, points=? WHERE id=? AND game_id=?");
                $st->execute([$title, $description, $points, $aid, $game_id]);
            }
            header('Location: /lol-portal/admin/achievements.php?game_id='.$game_id);
            exit;
        } else {
            $mode = ($action === 'create') ? 'create' : 'edit';
            $id = (int)($_POST['id'] ?? 0);
        }
    }

    if ($action === 'delete') {
        $aid = (int)($_POST['id'] ?? 0);
        $st = $pdo->prepare("DELETE FROM achievements WHERE id=? AND game_id=?");
        $st->execute([$aid, $game_id]);
        header('Location: /lol-portal/admin/achievements.php?game_id='.$game_id);
        exit;
    }
}

// Load achievement for edit
$ach = null;
if ($mode === 'edit' && $id > 0) {
    $st = $pdo->prepare("SELECT * FROM achievements WHERE id=? AND game_id=?");
    $st->execute([$id, $game_id]);
    $ach = $st->fetch();
    if (!$ach) {
        header('Location: /lol-portal/admin/achievements.php?game_id='.$game_id);
        exit;
    }
}

$achsSt = $pdo->prepare("SELECT id, title, points FROM achievements WHERE game_id=? ORDER BY id DESC");
$achsSt->execute([$game_id]);
$achs = $achsSt->fetchAll();

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES); }
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Admin - Succès</title></head>
<body>
<h1>Succès - <?= h($game['name']) ?></h1>
<p><a href="/lol-portal/admin/games.php">← Jeux</a> | <a href="/lol-portal/admin/dashboard.php">Dashboard</a></p>

<p><a href="/lol-portal/admin/achievements.php?game_id=<?= (int)$game_id ?>&mode=create">+ Ajouter un succès</a></p>

<?php foreach ($errors as $e): ?>
  <p style="color:red;"><?= h($e) ?></p>
<?php endforeach; ?>

<?php if ($mode === 'create' || $mode === 'edit'): ?>
  <?php
    $isEdit = ($mode === 'edit');
    $valTitle = $isEdit ? ($ach['title'] ?? '') : ($_POST['title'] ?? '');
    $valDesc  = $isEdit ? ($ach['description'] ?? '') : ($_POST['description'] ?? '');
    $valPts   = $isEdit ? (string)($ach['points'] ?? 10) : (string)($_POST['points'] ?? 10);
  ?>
  <h2><?= $isEdit ? "Modifier succès #".(int)$ach['id'] : "Créer un succès" ?></h2>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'create' ?>">
    <?php if ($isEdit): ?>
      <input type="hidden" name="id" value="<?= (int)$ach['id'] ?>">
    <?php endif; ?>

    <label>Titre</label><br>
    <input name="title" value="<?= h((string)$valTitle) ?>"><br><br>

    <label>Description</label><br>
    <textarea name="description" rows="5" cols="60"><?= h((string)$valDesc) ?></textarea><br><br>

    <label>Points</label><br>
    <input type="number" name="points" value="<?= h((string)$valPts) ?>"><br><br>

    <button type="submit"><?= $isEdit ? 'Enregistrer' : 'Créer' ?></button>
    <a href="/lol-portal/admin/achievements.php?game_id=<?= (int)$game_id ?>">Annuler</a>
  </form>
  <hr>
<?php endif; ?>

<h2>Liste des succès</h2>
<table border="1" cellpadding="6" cellspacing="0">
  <thead>
    <tr><th>ID</th><th>Titre</th><th>Points</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($achs as $a): ?>
      <tr>
        <td><?= (int)$a['id'] ?></td>
        <td><?= h($a['title']) ?></td>
        <td><?= (int)$a['points'] ?></td>
        <td>
          <a href="/lol-portal/admin/achievements.php?game_id=<?= (int)$game_id ?>&mode=edit&id=<?= (int)$a['id'] ?>">Modifier</a>

          <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer ce succès ?');">
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
            <button type="submit">Supprimer</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>

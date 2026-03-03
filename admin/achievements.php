<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$pdo = db();

$mode = $_GET['mode'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$games = $pdo->query("SELECT id, name FROM games ORDER BY name ASC")->fetchAll();

$errors = [];
$game_id = (int)($_POST['game_id'] ?? 0);
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$points = (int)($_POST['points'] ?? 10);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $aid = (int)($_POST['id'] ?? 0);
        $st = $pdo->prepare("DELETE FROM achievements WHERE id=?");
        $st->execute([$aid]);
        header('Location: /lol-portal/admin/achievements.php');
        exit;
    }

    if ($game_id <= 0) $errors[] = "Choisis un jeu.";
    if (trim($title) === '' || strlen($title) < 3) $errors[] = "Titre min 3 caractères.";
    if (trim($description) === '' || strlen($description) < 5) $errors[] = "Description min 5 caractères.";
    if ($points < 0 || $points > 1000) $errors[] = "Points invalides.";

    if (!$errors) {
        if ($action === 'create') {
            $st = $pdo->prepare("INSERT INTO achievements (game_id, title, description, points) VALUES (?, ?, ?, ?)");
            $st->execute([$game_id, $title, $description, $points]);
        } elseif ($action === 'update') {
            $aid = (int)($_POST['id'] ?? 0);
            $st = $pdo->prepare("UPDATE achievements SET game_id=?, title=?, description=?, points=? WHERE id=?");
            $st->execute([$game_id, $title, $description, $points, $aid]);
        }
        header('Location: /lol-portal/admin/achievements.php');
        exit;
    }
}

$editing = null;
if ($mode === 'edit' && $id > 0) {
    $st = $pdo->prepare("SELECT * FROM achievements WHERE id=?");
    $st->execute([$id]);
    $editing = $st->fetch();
    if ($editing) {
        $game_id = $game_id ?: (int)$editing['game_id'];
        $title = $title ?: $editing['title'];
        $description = $description ?: $editing['description'];
        $points = isset($_POST['points']) ? $points : (int)$editing['points'];
    }
}

$rows = $pdo->query("
  SELECT a.id, a.title, a.description, a.points, a.game_id, g.name AS game_name
  FROM achievements a
  JOIN games g ON g.id = a.game_id
  ORDER BY g.name ASC, a.points DESC, a.id DESC
")->fetchAll();

site_header('Admin - Succès');
?>

<section class="card">
  <div class="row row--between">
    <h1>Gestion des succès</h1>
    <div class="row">
      <a class="btn btn--ghost" href="/lol-portal/admin/dashboard.php">← Dashboard</a>
      <a class="btn" href="/lol-portal/admin/achievements.php?mode=create">+ Nouveau succès</a>
    </div>
  </div>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert--danger"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <?php if ($mode === 'create' || ($mode === 'edit' && $editing)): ?>
    <div class="card card--inner">
      <h2><?= $mode === 'create' ? 'Créer un succès' : 'Modifier le succès' ?></h2>

      <form method="post" class="form">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="hidden" name="action" value="<?= $mode === 'create' ? 'create' : 'update' ?>">
        <?php if ($mode === 'edit'): ?>
          <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
        <?php endif; ?>

        <label>Jeu</label>
        <select name="game_id" required>
          <option value="">-- choisir --</option>
          <?php foreach ($games as $g): ?>
            <option value="<?= (int)$g['id'] ?>" <?= ((int)$g['id'] === (int)$game_id) ? 'selected' : '' ?>>
              <?= htmlspecialchars($g['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Titre</label>
        <input name="title" value="<?= htmlspecialchars($title) ?>" required>

        <label>Description</label>
        <textarea name="description" rows="3" required><?= htmlspecialchars($description) ?></textarea>

        <label>Points</label>
        <input type="number" name="points" value="<?= (int)$points ?>" min="0" max="1000">

        <div class="row">
          <button class="btn" type="submit">Enregistrer</button>
          <a class="btn btn--ghost" href="/lol-portal/admin/achievements.php">Annuler</a>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <div class="tableWrap">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th><th>Jeu</th><th>Titre</th><th>Points</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $a): ?>
          <tr>
            <td><?= (int)$a['id'] ?></td>
            <td><?= htmlspecialchars($a['game_name']) ?></td>
            <td><?= htmlspecialchars($a['title']) ?></td>
            <td><span class="pill"><?= (int)$a['points'] ?> pts</span></td>
            <td>
              <a class="btn btn--small" href="/lol-portal/admin/achievements.php?mode=edit&id=<?= (int)$a['id'] ?>">Edit</a>
              <form method="post" class="inline" onsubmit="return confirm('Supprimer ce succès ?');">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                <button class="btn btn--small btn--danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</section>

<?php site_footer(); ?>
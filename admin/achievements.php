<?php
$title = "Admin - Succès";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$pdo = db();

$errors = [];

$game_id = (int)($_POST['game_id'] ?? 0);
$titleA = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$points = (int)($_POST['points'] ?? 10);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        if ($game_id <= 0) $errors[] = "Choisis un jeu.";
        if ($titleA === '' || strlen($titleA) < 2) $errors[] = "Titre invalide (min 2 caractères).";
        if ($points <= 0) $errors[] = "Points invalides (doit être > 0).";

        if (!$errors) {
            $st = $pdo->prepare("INSERT INTO achievements (game_id, title, description, points) VALUES (?, ?, ?, ?)");
            $st->execute([$game_id, $titleA, $description, $points]);
            header('Location: /lol-portal/admin/achievements.php');
            exit;
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM achievements WHERE id=?")->execute([$id]);
        header('Location: /lol-portal/admin/achievements.php');
        exit;
    }
}

$games = $pdo->query("SELECT id, name FROM games ORDER BY name ASC")->fetchAll();
$ach = $pdo->query("
  SELECT a.id, a.title, a.description, a.points, g.name AS game_name
  FROM achievements a
  JOIN games g ON g.id = a.game_id
  ORDER BY a.id DESC
")->fetchAll();
?>

<div class="card">
  <h1>Gestion des succès</h1>
  <p class="muted"><a href="/lol-portal/admin/dashboard.php">← Dashboard</a></p>

  <?php foreach ($errors as $e): ?>
    <div class="flash flash--error"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <h2>Ajouter un succès</h2>
  <form class="form" method="post">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
    <input type="hidden" name="action" value="create">

    <div>
      <label>Jeu</label>
      <select name="game_id">
        <option value="0">-- Choisir --</option>
        <?php foreach ($games as $g): ?>
          <option value="<?= (int)$g['id'] ?>" <?= $game_id === (int)$g['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($g['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label>Titre</label>
      <input name="title" value="<?= htmlspecialchars($titleA) ?>" placeholder="Ex: First Blood">
    </div>

    <div>
      <label>Description</label>
      <textarea name="description" rows="3" placeholder="Ex: Gagner une partie en moins de 20 minutes"><?= htmlspecialchars($description) ?></textarea>
    </div>

    <div>
      <label>Points</label>
      <input type="number" name="points" value="<?= (int)$points ?>" min="1">
    </div>

    <button class="btn" type="submit">Ajouter</button>
  </form>

  <hr>

  <h2>Liste des succès</h2>
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Jeu</th>
        <th>Titre</th>
        <th>Points</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($ach as $a): ?>
        <tr>
          <td><?= (int)$a['id'] ?></td>
          <td><?= htmlspecialchars($a['game_name']) ?></td>
          <td>
            <strong><?= htmlspecialchars($a['title']) ?></strong><br>
            <span class="muted"><?= htmlspecialchars($a['description']) ?></span>
          </td>
          <td><?= (int)$a['points'] ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Supprimer ce succès ?');">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
              <button class="btn btn--ghost" type="submit">Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
$title = "Admin - Jeux";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();
$pdo = db();

$errors = [];
$name = trim($_POST['name'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        if ($name === '' || strlen($name) < 2) {
            $errors[] = "Nom du jeu invalide (min 2 caractères).";
        } else {
            $st = $pdo->prepare("INSERT INTO games (name) VALUES (?)");
            $st->execute([$name]);
            header('Location: /lol-portal/admin/games.php');
            exit;
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);

        // si achievements dépend de game_id, il faut supprimer d'abord les achievements
        $pdo->prepare("DELETE FROM achievements WHERE game_id=?")->execute([$id]);
        $pdo->prepare("DELETE FROM games WHERE id=?")->execute([$id]);

        header('Location: /lol-portal/admin/games.php');
        exit;
    }
}

$games = $pdo->query("SELECT id, name FROM games ORDER BY id DESC")->fetchAll();
?>

<div class="card">
  <h1>Gestion des jeux</h1>
  <p class="muted"><a href="/lol-portal/admin/dashboard.php">← Dashboard</a></p>

  <?php foreach ($errors as $e): ?>
    <div class="flash flash--error"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <h2>Ajouter un jeu</h2>
  <form class="form" method="post">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
    <input type="hidden" name="action" value="create">

    <div>
      <label>Nom</label>
      <input name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Ex: Sekiro">
    </div>

    <button class="btn" type="submit">Ajouter</button>
  </form>

  <hr>

  <h2>Liste des jeux</h2>
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($games as $g): ?>
        <tr>
          <td><?= (int)$g['id'] ?></td>
          <td><?= htmlspecialchars($g['name']) ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Supprimer ce jeu ? (supprime aussi ses succès)');">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
              <button class="btn btn--ghost" type="submit">Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
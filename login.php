<?php
$title = "Connexion";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$errors = [];
$login = $_POST['login'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    $pdo = db();
    $st = $pdo->prepare("SELECT id, username, email, password_hash, role FROM users WHERE username = ? OR email = ?");
    $st->execute([$login, $login]);
    $u = $st->fetch();

    if (!$u || !password_verify($password, $u['password_hash'])) {
        $errors[] = "Identifiants invalides.";
    } else {
        $_SESSION['user'] = [
            'id' => (int)$u['id'],
            'username' => $u['username'],
            'email' => $u['email'],
            'role' => $u['role'],
        ];
        header('Location: /lol-portal/index.php');
        exit;
    }
}
?>

<div class="card">
  <h1>Connexion</h1>

  <?php foreach ($errors as $e): ?>
    <div class="flash flash--error"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <form class="form" method="post">
    <div>
      <label>Username ou Email</label>
      <input name="login" value="<?= htmlspecialchars($login) ?>" autocomplete="username">
    </div>

    <div>
      <label>Mot de passe</label>
      <input type="password" name="password" autocomplete="current-password">
    </div>

    <button class="btn" type="submit">Se connecter</button>
  </form>

  <hr>
  <p class="muted"><a href="/lol-portal/register.php">Créer un compte</a></p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php
$title = "Inscription";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$errors = [];
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (trim($username) === '' || strlen($username) < 3) $errors[] = "Username min 3 caractères.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if (strlen($password) < 6) $errors[] = "Mot de passe min 6 caractères.";
    if ($password !== $password2) $errors[] = "Les mots de passe ne matchent pas.";

    if (!$errors) {
        $pdo = db();

        $st = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $st->execute([$username, $email]);
        if ($st->fetch()) {
            $errors[] = "Username ou email déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $st = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'user')");
            $st->execute([$username, $email, $hash]);

            header('Location: /lol-portal/login.php');
            exit;
        }
    }
}
?>

<div class="card">
  <h1>Inscription</h1>

  <?php foreach ($errors as $e): ?>
    <div class="flash flash--error"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <form class="form" method="post">
    <div>
      <label>Username</label>
      <input name="username" value="<?= htmlspecialchars($username) ?>" autocomplete="username">
    </div>

    <div>
      <label>Email</label>
      <input name="email" value="<?= htmlspecialchars($email) ?>" autocomplete="email">
    </div>

    <div>
      <label>Mot de passe</label>
      <input type="password" name="password" autocomplete="new-password">
    </div>

    <div>
      <label>Confirmer</label>
      <input type="password" name="password2" autocomplete="new-password">
    </div>

    <button class="btn" type="submit">Créer le compte</button>
  </form>

  <hr>
  <p class="muted"><a href="/lol-portal/login.php">Déjà un compte ? Connexion</a></p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
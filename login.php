<?php
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/db.php';

$errors = [];
$login = $_POST['login'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    $pdo = db();
    $st = $pdo->prepare("SELECT id, username, email, password_hash, role, created_at FROM users WHERE username = ? OR email = ?");
    $st->execute([$login, $login]);
    $user = $st->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $errors[] = "Identifiants invalides.";
    } else {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'created_at' => $user['created_at'],
        ];
        header('Location: /lol-portal/index.php');
        exit;
    }
}

site_header('Connexion');
?>

<section class="card card--small">
  <h1>Connexion</h1>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert--danger"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <form method="post" class="form">
    <label>Username ou Email</label>
    <input name="login" value="<?= htmlspecialchars($login) ?>" required>

    <label>Mot de passe</label>
    <input type="password" name="password" required>

    <button class="btn" type="submit">Se connecter</button>
  </form>

  <p class="muted">Pas de compte ? <a href="/lol-portal/register.php">Créer un compte</a></p>
</section>

<?php site_footer(); ?>
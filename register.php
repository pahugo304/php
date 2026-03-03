<?php
require_once __DIR__ . '/includes/config.php';
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

        // Check unique
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
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Inscription</title></head>
<body>
<h1>Inscription</h1>

<?php foreach ($errors as $e): ?>
  <p style="color:red;"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>

<form method="post">
  <label>Username</label><br>
  <input name="username" value="<?= htmlspecialchars($username) ?>"><br><br>

  <label>Email</label><br>
  <input name="email" value="<?= htmlspecialchars($email) ?>"><br><br>

  <label>Mot de passe</label><br>
  <input type="password" name="password"><br><br>

  <label>Confirmer</label><br>
  <input type="password" name="password2"><br><br>

  <button type="submit">Créer le compte</button>
</form>

<p><a href="/lol-portal/login.php">Déjà un compte ? Login</a></p>
</body>
</html>

<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$errors = [];
$login = $_POST['login'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    $pdo = db();
    $st = $pdo->prepare("SELECT id, username, email, password_hash, role FROM users WHERE username = ? OR email = ?");
    $st->execute([$login, $login]);
    $user = $st->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $errors[] = "Identifiants invalides.";
    } else {
        // session
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        header('Location: /lol-portal/index.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Connexion</title></head>
<body>
<h1>Connexion</h1>

<?php foreach ($errors as $e): ?>
  <p style="color:red;"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>

<form method="post">
  <label>Username ou Email</label><br>
  <input name="login" value="<?= htmlspecialchars($login) ?>"><br><br>

  <label>Mot de passe</label><br>
  <input type="password" name="password"><br><br>

  <button type="submit">Se connecter</button>
</form>

<p><a href="/lol-portal/register.php">Créer un compte</a></p>
</body>
</html>

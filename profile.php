<?php
require_once __DIR__ . '/includes/layout.php';

require_login();
$user = current_user();

site_header('Profil');
?>

<section class="card card--small">
  <h1>Profil</h1>

  <p><strong>Username</strong> : <?= htmlspecialchars($user['username']) ?></p>
  <p><strong>Email</strong> : <?= htmlspecialchars($user['email']) ?></p>
  <p><strong>Rôle</strong> : <?= htmlspecialchars($user['role']) ?></p>
</section>

<?php site_footer(); ?>
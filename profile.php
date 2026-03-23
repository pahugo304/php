<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

require_login();

$pdo = db();
$user = current_user();

$st = $pdo->prepare("
    SELECT ug.id, ug.is_favorite, ug.play_time_hours, ug.added_at,
           g.id AS game_id, g.name, g.type, g.difficulty, g.image_url
    FROM user_games ug
    JOIN games g ON g.id = ug.game_id
    WHERE ug.user_id = ?
    ORDER BY ug.is_favorite DESC, ug.play_time_hours DESC
");
$st->execute([$user['id']]);
$userGames = $st->fetchAll();

$favoritesCount = 0;
$totalHours = 0;

foreach ($userGames as $ug) {
    if ((int)$ug['is_favorite'] === 1) {
        $favoritesCount++;
    }
    $totalHours += (int)$ug['play_time_hours'];
}

site_header('Profil');
?>

<section class="card card--small">
  <h1>Profil</h1>
<h2>Mes jeux</h2>

<?php if (!$userGames): ?>
  <p class="muted">Aucun jeu.</p>
<?php else: ?>
  <div class="grid grid--games">
    <?php foreach ($userGames as $g): ?>
      <div class="game-card">
        <strong><?= htmlspecialchars($g['name']) ?></strong>

        <div>
          <span class="badge"><?= htmlspecialchars($g['type']) ?></span>
          <span class="badge"><?= htmlspecialchars($g['difficulty']) ?></span>
        </div>

        <div class="muted">
          Temps de jeu : <?= (int)$g['play_time_hours'] ?>h
        </div>

        <?php if ($g['is_favorite']): ?>
          ⭐ Favori
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
  <p><strong>Username :</strong> <?= htmlspecialchars($user['username']) ?></p>
  <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
  <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></p>

  <?php if (!empty($user['created_at'])): ?>
    <p><strong>Date d’inscription :</strong> <?= htmlspecialchars($user['created_at']) ?></p>
  <?php endif; ?>

  <hr style="margin:18px 0; border-color:rgba(255,255,255,.08);">

  <div class="row" style="gap:10px;">
    <span class="pill"><?= count($userGames) ?> jeu(x)</span>
    <span class="pill"><?= $favoritesCount ?> favori(s)</span>
    <span class="pill"><?= $totalHours ?> h jouées</span>
  </div>
</section>

<section class="card" style="margin-top:20px;">
  <h2>Mes jeux</h2>

  <?php if (!$userGames): ?>
    <p class="muted">Aucun jeu associé à ce profil pour le moment.</p>
  <?php else: ?>
    <div class="grid grid--games" style="margin-top:14px;">
      <?php foreach ($userGames as $g): ?>
        <?php
        $difficultyClass = match ($g['difficulty']) {
            'Easy' => 'badge badge--easy',
            'Hard' => 'badge badge--hard',
            default => 'badge badge--medium',
        };
        ?>
        <a class="tile tile--link game-card" href="/lol-portal/games.php?name=<?= urlencode($g['name']) ?>">
          <div class="game-card__media">
            <?php if (!empty($g['image_url'])): ?>
              <img
                src="<?= htmlspecialchars($g['image_url']) ?>"
                alt="<?= htmlspecialchars($g['name']) ?>"
                loading="lazy"
                onerror="this.closest('.game-card__media').innerHTML='<div class=&quot;img-placeholder&quot;>No image</div>';"
              >
            <?php else: ?>
              <div class="img-placeholder">No image</div>
            <?php endif; ?>
          </div>

          <div class="tile__top">
            <strong><?= htmlspecialchars($g['name']) ?></strong>
          </div>

          <div class="row" style="gap:8px; margin-bottom:8px;">
            <span class="pill"><?= htmlspecialchars($g['type']) ?></span>
            <span class="<?= $difficultyClass ?>"><?= htmlspecialchars($g['difficulty']) ?></span>
            <?php if ((int)$g['is_favorite'] === 1): ?>
              <span class="badge">Favori</span>
            <?php endif; ?>
          </div>

          <div class="muted">
            Temps de jeu : <?= (int)$g['play_time_hours'] ?> h
          </div>
          <div class="muted">
            Ajouté le : <?= htmlspecialchars($g['added_at']) ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php site_footer(); ?>
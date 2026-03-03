<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

$pdo = db();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

site_header($id ? 'Détails du jeu' : 'Jeux');

// detail page
if ($id) {
    $st = $pdo->prepare("SELECT id, name, type, description, image_url FROM games WHERE id = ?");
    $st->execute([$id]);
    $game = $st->fetch();

    if (!$game) {
        echo '<div class="alert alert--danger">Jeu introuvable.</div>';
        echo '<p><a class="btn btn--ghost" href="/lol-portal/games.php">← Retour aux jeux</a></p>';
        site_footer();
        exit;
    }

    $st = $pdo->prepare("SELECT id, title, description, points
                         FROM achievements
                         WHERE game_id = ?
                         ORDER BY points DESC, id ASC");
    $st->execute([$id]);
    $achievements = $st->fetchAll();
    ?>

    <div class="card">
      <div class="row row--between" style="gap:16px;">
        <div>
          <h1 style="margin-bottom:6px;"><?= htmlspecialchars($game['name']) ?></h1>
          <div class="row" style="gap:10px;">
            <span class="badge"><?= htmlspecialchars($game['type']) ?></span>
            <a class="btn btn--ghost btn--small" href="/lol-portal/games.php">← Retour</a>
          </div>
        </div>

        <?php if (!empty($game['image_url'])): ?>
          <img class="gameHeader__img"
               src="<?= htmlspecialchars($game['image_url']) ?>"
               alt="<?= htmlspecialchars($game['name']) ?>"
               loading="lazy"
               referrerpolicy="no-referrer"
               onerror="this.style.display='none';">
        <?php endif; ?>
      </div>

      <div class="card card--inner">
        <p class="muted" style="margin:0; line-height:1.6;">
          <?= nl2br(htmlspecialchars($game['description'])) ?>
        </p>
      </div>

      <h2 style="margin-top:18px;">Succès</h2>

      <?php if (!$achievements): ?>
        <p class="muted">Aucun succès pour ce jeu.</p>
      <?php else: ?>
        <div class="tableWrap">
          <table class="table">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Description</th>
                <th>Points</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($achievements as $a): ?>
                <tr>
                  <td><?= htmlspecialchars($a['title']) ?></td>
                  <td><?= htmlspecialchars($a['description']) ?></td>
                  <td><?= (int)$a['points'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <?php
    site_footer();
    exit;
}

// Liste of games
$games = $pdo->query("SELECT id, name, type, description, image_url FROM games ORDER BY name ASC")->fetchAll();
?>

<div class="card">
  <h1>Jeux</h1>
  <p class="muted">Clique sur un jeu pour voir les détails et les succès.</p>

  <div class="grid grid--games" style="margin-top:14px;">
    <?php foreach ($games as $g): ?>
      <a class="tile tile--link game-card" href="/lol-portal/games.php?id=<?= (int)$g['id'] ?>">
        <div class="game-card__media">
          <?php if (!empty($g['image_url'])): ?>
            <img src="<?= htmlspecialchars($g['image_url']) ?>"
                 alt="<?= htmlspecialchars($g['name']) ?>"
                 loading="lazy"
                 referrerpolicy="no-referrer"
                 onerror="this.closest('.game-card__media').innerHTML='<div class=&quot;img-placeholder&quot;>No image</div>';">
          <?php else: ?>
            <div class="img-placeholder">No image</div>
          <?php endif; ?>
        </div>

        <div class="tile__top">
          <strong><?= htmlspecialchars($g['name']) ?></strong>
          <span class="pill"><?= htmlspecialchars($g['type']) ?></span>
        </div>

        <div class="muted clamp">
          <?= htmlspecialchars($g['description']) ?>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<?php site_footer(); ?>
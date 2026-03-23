<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = db();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = current_user();

site_header($id > 0 ? 'Détails du jeu' : 'Jeux');

if ($id > 0) {
    $st = $pdo->prepare("
        SELECT id, name, type, difficulty, description, image_url
        FROM games
        WHERE id = ?
    ");
    $st->execute([$id]);
    $game = $st->fetch();

    if (!$game) {
        echo '<div class="alert alert--danger">Jeu introuvable.</div>';
        echo '<p><a class="btn btn--ghost" href="/lol-portal/games.php">← Retour aux jeux</a></p>';
        site_footer();
        exit;
    }

    $st = $pdo->prepare("
        SELECT id, title, description, points
        FROM achievements
        WHERE game_id = ?
        ORDER BY points DESC, id ASC
    ");
    $st->execute([$id]);
    $achievements = $st->fetchAll();

    $difficultyClass = match ($game['difficulty']) {
        'Easy' => 'badge badge--easy',
        'Hard' => 'badge badge--hard',
        default => 'badge badge--medium',
    };

    $isAdded = false;
    $isFavorite = false;

    if ($user) {
        $st = $pdo->prepare("
            SELECT is_favorite
            FROM user_games
            WHERE user_id = ? AND game_id = ?
        ");
        $st->execute([(int)$user['id'], $id]);
        $userGame = $st->fetch();

        if ($userGame) {
            $isAdded = true;
            $isFavorite = (int)$userGame['is_favorite'] === 1;
        }
    }
    ?>
    <div class="card">
      <div class="row row--between" style="gap:16px;">
        <div>
          <h1 style="margin-bottom:6px;"><?= htmlspecialchars($game['name']) ?></h1>

          <div class="row" style="gap:10px;">
            <span class="badge"><?= htmlspecialchars($game['type']) ?></span>
            <span class="<?= $difficultyClass ?>"><?= htmlspecialchars($game['difficulty']) ?></span>
            <a class="btn btn--ghost btn--small" href="/lol-portal/games.php">← Retour</a>
          </div>

          <?php if ($user): ?>
            <div class="row" style="gap:8px; margin-top:12px;">
              <?php if (!$isAdded): ?>
                <form method="post" action="/lol-portal/add_game.php" class="inline">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                  <input type="hidden" name="game_id" value="<?= (int)$game['id'] ?>">
                  <button class="btn btn--small" type="submit">Ajouter à mon profil</button>
                </form>
              <?php else: ?>
                <form method="post" action="/lol-portal/toggle_favorite.php" class="inline">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                  <input type="hidden" name="game_id" value="<?= (int)$game['id'] ?>">
                  <button class="btn btn--small" type="submit">
                    <?= $isFavorite ? 'Retirer favori' : 'Mettre en favori' ?>
                  </button>
                </form>

                <form method="post" action="/lol-portal/remove_game.php" class="inline" onsubmit="return confirm('Retirer ce jeu de ton profil ?');">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                  <input type="hidden" name="game_id" value="<?= (int)$game['id'] ?>">
                  <button class="btn btn--small btn--danger" type="submit">Retirer du profil</button>
                </form>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>

        <?php if (!empty($game['image_url'])): ?>
          <img
            class="gameHeader__img"
            src="<?= htmlspecialchars($game['image_url']) ?>"
            alt="<?= htmlspecialchars($game['name']) ?>"
            loading="lazy"
            onerror="this.style.display='none';"
          >
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

$games = $pdo->query("
    SELECT id, name, type, difficulty, description, image_url
    FROM games
    ORDER BY name ASC
")->fetchAll();

$myGameIds = [];
$favoriteGameIds = [];

if ($user) {
    $st = $pdo->prepare("
        SELECT game_id, is_favorite
        FROM user_games
        WHERE user_id = ?
    ");
    $st->execute([(int)$user['id']]);
    $userLinks = $st->fetchAll();

    foreach ($userLinks as $link) {
        $myGameIds[] = (int)$link['game_id'];
        if ((int)$link['is_favorite'] === 1) {
            $favoriteGameIds[] = (int)$link['game_id'];
        }
    }
}
?>

<div class="card">
  <h1>Jeux</h1>
  <p class="muted">Clique sur un jeu pour voir les détails et les succès.</p>

  <?php if (!$games): ?>
    <div class="alert alert--danger">Aucun jeu trouvé dans la base.</div>
  <?php else: ?>
    <div class="grid grid--games" style="margin-top:14px;">
      <?php foreach ($games as $g): ?>
        <?php
        $difficultyClass = match ($g['difficulty']) {
            'Easy' => 'badge badge--easy',
            'Hard' => 'badge badge--hard',
            default => 'badge badge--medium',
        };

        $isAdded = in_array((int)$g['id'], $myGameIds, true);
        $isFavorite = in_array((int)$g['id'], $favoriteGameIds, true);
        ?>
        <div class="tile game-card">
          <a href="/lol-portal/games.php?id=<?= (int)$g['id'] ?>" class="tile--link" style="display:block;">
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
            </div>

            <div class="muted clamp">
              <?= htmlspecialchars($g['description']) ?>
            </div>
          </a>

          <?php if ($user): ?>
            <div class="row" style="margin-top:12px; gap:8px;">
              <a class="btn btn--small btn--ghost" href="/lol-portal/games.php?id=<?= (int)$g['id'] ?>">Voir détails</a>

              <?php if (!$isAdded): ?>
                <form method="post" action="/lol-portal/add_game.php" class="inline">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                  <input type="hidden" name="game_id" value="<?= (int)$g['id'] ?>">
                  <button class="btn btn--small" type="submit">Ajouter</button>
                </form>
              <?php else: ?>
                <form method="post" action="/lol-portal/toggle_favorite.php" class="inline">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                  <input type="hidden" name="game_id" value="<?= (int)$g['id'] ?>">
                  <button class="btn btn--small" type="submit">
                    <?= $isFavorite ? 'Retirer favori' : 'Favori' ?>
                  </button>
                </form>

                <form method="post" action="/lol-portal/remove_game.php" class="inline" onsubmit="return confirm('Retirer ce jeu de ton profil ?');">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                  <input type="hidden" name="game_id" value="<?= (int)$g['id'] ?>">
                  <button class="btn btn--small btn--danger" type="submit">Retirer</button>
                </form>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php site_footer(); ?>
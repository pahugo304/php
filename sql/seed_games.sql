INSERT INTO games (name, type, description, image_url) VALUES
('League of Legends', 'MOBA',
 '5v5 compétitif : choisis un champion, farm, prends les objectifs et détruis le Nexus.',
 'https://static.wikia.nocookie.net/leagueoflegends/images/6/6b/League_of_Legends_logo.png'),
('Overwatch 2', 'Hero Shooter',
 'FPS en équipes avec héros et rôles (tank/dps/support). Objectifs, coordination et ultimates.',
 'https://upload.wikimedia.org/wikipedia/commons/5/55/Overwatch_circle_logo.svg'),
('Marvel Rivals', 'Hero Shooter',
 'Shooter 6v6 avec super-héros Marvel, synergies et pouvoirs pour contrôler la map.',
 'https://upload.wikimedia.org/wikipedia/commons/0/0c/Marvel_Logo.svg'),
('Apex Legends', 'Battle Royale',
 'BR rapide en squads, mobilité, armes, legends et rotation sur la zone.',
 'https://upload.wikimedia.org/wikipedia/commons/d/db/Apex_legends_logo.svg'),
('Elden Ring', 'Action RPG',
 'Exploration, boss, builds et progression dans un monde ouvert dark-fantasy (souls-like).',
 'https://upload.wikimedia.org/wikipedia/commons/6/6b/Elden_Ring_logo.svg');

-- Achievements (3 per game)
-- League of Legends
INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'First Blood', 'Obtenir la première élimination de la partie.', 15
FROM games WHERE name='League of Legends';

INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Objectif prioritaire', 'Participer à la prise d’un dragon ou du Baron Nashor.', 20
FROM games WHERE name='League of Legends';

INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Carry', 'Finir la partie avec un KDA >= 5.0.', 25
FROM games WHERE name='League of Legends';

-- Overwatch 2
INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Team Wipe', 'Éliminer l’équipe ennemie en moins de 10 secondes.', 25
FROM games WHERE name='Overwatch 2';

INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Support MVP', 'Soigner 10 000 PV en une partie.', 20
FROM games WHERE name='Overwatch 2';

INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Ultimate Combo', 'Gagner un fight grâce à un combo d’ultimates.', 15
FROM games WHERE name='Overwatch 2';

-- Marvel Rivals
INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Synergie Marvel', 'Réaliser une action de combo/équipe (synergy) avec un allié.', 20
FROM games WHERE name='Marvel Rivals';

INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Héros invincible', 'Survivre 60 secondes en combat actif sans mourir.', 15
FROM games WHERE name='Marvel Rivals';

INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Contrôle de zone', 'Capturer/tenir un objectif pendant 30 secondes cumulées.', 20
FROM games WHERE name='Marvel Rivals';

-- Apex Legends
INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Champion Squad', 'Remporter une partie.', 30
FROM games WHERE name='Apex Legends';

INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Third Party', 'Gagner un combat en arrivant sur un fight déjà engagé.', 15
FROM games WHERE name='Apex Legends';

INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Ping Master', 'Faire 30 pings utiles dans une partie (info/loot/ennemis).', 10
FROM games WHERE name='Apex Legends';

-- Elden Ring
INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Boss Slayer', 'Vaincre un boss majeur.', 30
FROM games WHERE name='Elden Ring';

INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'Build Completed', 'Améliorer une arme au niveau élevé et obtenir une cendre/compétence clé.', 20
FROM games WHERE name='Elden Ring';

INSERT INTO achievements (game_id, title, description, points)
SELECT id, 'No Hit (presque)', 'Vaincre un boss en prenant très peu de dégâts (challenge perso).', 25
FROM games WHERE name='Elden Ring';

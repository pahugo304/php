# LoL Portal

LoL Portal est une plateforme web développée en PHP/MySQL dans le cadre d’un projet de fin de module.

## Fonctionnalités

- Authentification utilisateur
- Gestion des rôles `user` / `admin`
- Espace administrateur
- CRUD des jeux
- CRUD des succès
- Page profil utilisateur
- Affichage des jeux avec images, difficulté et succès associés

## Technologies utilisées

- PHP
- MariaDB / MySQL
- HTML / CSS
- Apache

## Structure du projet

- `index.php` : page d’accueil
- `games.php` : liste des jeux et détails
- `profile.php` : profil utilisateur
- `admin/` : espace administrateur
- `includes/` : configuration, base de données, auth, layout
- `sql/` : schéma et données de test
- `assets/` : CSS et images

## Installation

1. Cloner le projet dans `/var/www/html/lol-portal`
2. Créer la base de données
3. Importer `sql/schema.sql`
4. Importer `sql/seed.sql`
5. Importer `sql/seed_games.sql`
6. Configurer le fichier `.env`

## Comptes de test

### Admin
- username : `admin`
- email : `admin@lol.test`

### User
- username : `player1`
- email : `player1@lol.test`

## Notes

- Le thème visuel du site est inspiré de League of Legends
- Les jeux en base sont différents pour respecter les consignes du projet
- Les mots de passe utilisateurs sont stockés de manière sécurisée avec `password_hash()`
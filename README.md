# 🧠 LocalMind – Application Web Communautaire

## 📌 Description

**LocalMind** est une application web communautaire développée avec **Laravel** (backend) et **Vue.js** (frontend), permettant aux utilisateurs de poser des **questions localisées** et d’obtenir des **réponses d’utilisateurs proches**, afin de favoriser l’entraide locale.

L’application est **dockerisée** : backend, frontend, base de données et reverse proxy tournent dans des conteneurs Docker.

---

## 🛠️ Technologies

### Backend
* PHP 8.3 / Laravel
* PostgreSQL
* Blade Templates
* MVC & Eloquent ORM

### Frontend
* **Vue.js 3** (Composition API)
* **Vite 5** (build & dev server)
* **Tailwind CSS v4** (styles)
* **Axios** (requêtes HTTP vers l’API)
* **Lucide Vue** (icônes)
* **Font Awesome** (icônes complémentaires)

### Infrastructure
* **Docker** & **Docker Compose**
* **Nginx** (reverse proxy pour le backend Laravel)
* **PgAdmin** (administration PostgreSQL)

---

## 🐳 Dockerisation

Le projet est entièrement dockerisé. Les services définis dans `docker-compose.yml` :

| Service     | Description                    | Port (exemple) |
|------------|---------------------------------|----------------|
| **app**    | Laravel (PHP-FPM)               | via nginx      |
| **frontend** | Vue.js + Vite (dev server)    | 5173           |
| **nginx**  | Reverse proxy (PHP / API)       | 80             |
| **postgres** | Base PostgreSQL               | 5432           |
| **pgadmin** | Interface d’admin PostgreSQL  | 5050 (à définir dans `.env`) |

### Démarrer l’application avec Docker

À la racine du dossier `LocalMind` (où se trouve `docker-compose.yml`) :

```bash
# Copier l’exemple d’environnement et renseigner les variables
cp .env.example .env

# Construire et lancer tous les services
docker compose up -d

# Rebuild du frontend (si besoin, après changement de config)
docker compose build --no-cache frontend && docker compose up -d frontend
```

* **Frontend (Vue)** : http://localhost:5173  
* **Backend (API / app Laravel)** : selon la config Nginx (ex. http://localhost avec `PHP_PORT=80`)

---

## ⚙️ Fonctionnalités

* 🔐 Authentification (Utilisateur / Admin)
* 💬 Questions : création, modification, suppression, recherche par lieu ou mot-clé
* 💡 Réponses aux questions
* ⭐ Favoris
* 📊 Statistiques (optionnel)

---

## 🗄️ Base de données

Tables : `users`, `questions`, `responses`, `favorites`

Relations principales :

```php
User hasMany Questions
Question hasMany Responses
Response belongsTo Question
User hasMany Favorites
```

---

## 📁 Structure du projet

```
LocalMind/
├── backend/          # Laravel (API, auth, modèles)
│   └── Dockerfile
├── frontend/         # Vue 3 + Vite + Tailwind
│   └── Dockerfile
├── nginx/            # Config Nginx
├── docker-compose.yml
└── .env              # Variables d’environnement (à créer depuis .env.example)
```

---

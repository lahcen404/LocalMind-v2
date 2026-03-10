# 🧠 LocalMind – Community Web Application

## 📌 Description

**LocalMind** is a community web application built with **Laravel** (backend) and **Vue.js** (frontend). Users can ask **location-based questions** and get **answers from people nearby**, fostering local help and exchange.

The application is **dockerized**: backend, frontend, database, and reverse proxy all run in Docker containers.

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
* **Tailwind CSS v4** (styling)
* **Axios** (HTTP requests to the API)
* **Lucide Vue** (icons)
* **Font Awesome** (additional icons)

### Infrastructure
* **Docker** & **Docker Compose**
* **Nginx** (reverse proxy for Laravel backend)
* **PgAdmin** (PostgreSQL administration)

---

## 🐳 Docker

The project is fully dockerized. Services defined in `docker-compose.yml`:

| Service     | Description                     | Port (example) |
|------------|----------------------------------|----------------|
| **app**    | Laravel (PHP-FPM)                | via nginx      |
| **frontend** | Vue.js + Vite (dev server)     | 5173           |
| **nginx**  | Reverse proxy (PHP / API)       | 80             |
| **postgres** | PostgreSQL database            | 5432           |
| **pgadmin** | PostgreSQL admin UI             | 5050 (set in `.env`) |

### Run the application with Docker

From the `LocalMind` root (where `docker-compose.yml` lives):

```bash
# Copy the example env file and set your variables
cp .env.example .env

# Build and start all services
docker compose up -d

# Rebuild frontend (e.g. after config changes)
docker compose build --no-cache frontend && docker compose up -d frontend
```

* **Frontend (Vue)**: http://localhost:5173  
* **Backend (API / Laravel app)**: depends on Nginx config (e.g. http://localhost with `PHP_PORT=80`)

---

## ⚙️ Features

* 🔐 Authentication (User / Admin)
* 💬 Questions: create, edit, delete, search by location or keyword
* 💡 Answers to questions
* ⭐ Favorites
* 📊 Statistics (optional)

---

## 🗄️ Database

Tables: `users`, `questions`, `responses`, `favorites`

Main relationships:

```php
User hasMany Questions
Question hasMany Responses
Response belongsTo Question
User hasMany Favorites
```

---

## 📁 Project structure

```
LocalMind/
├── backend/          # Laravel (API, auth, models)
│   └── Dockerfile
├── frontend/         # Vue 3 + Vite + Tailwind
│   └── Dockerfile
├── nginx/            # Nginx config
├── docker-compose.yml
└── .env              # Environment variables (create from .env.example)
```

---

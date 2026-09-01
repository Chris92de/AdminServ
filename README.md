[![CI](https://github.com/Chris92de/AdminServ/actions/workflows/ci.yml/badge.svg)](https://github.com/Chris92de/AdminServ/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

# AdminServ 4.0

> Modern, lightweight web interface to manage TrackMania (TM2020), ManiaPlanet, and TrackMania Forever dedicated servers.

Re-engineered from scratch with **Modern PHP 8.3+**, **Preact**, **Tailwind CSS v4**, and **DaisyUI 5**.



**Full disclosure**: This project was collaboratively engineered with **Gemini 3.7 Flash**. It is loosely based on the original AdminServ project by `kev717` and me.

---

## Highlights & Key Features

* **Multi-Server Management**: Manage multiple Trackmania (TM2020, ManiaPlanet, and TMNF) servers from a single centralized web interface.
* **Live Dashboard & In-Game Chat**: Monitor live player counts, current map previews, and chat with players directly in-game with full Trackmania color code support.
* **Instant Admin Actions**: Restart challenges, skip maps, cancel votes, force scores, and broadcast server notifications in one click.
* **Player Moderation**: View connected players, kick/ban disruptive users, manage blacklists/guestlists, and switch players between spectators and teams.
* **Map & Playlist Management**: Browse server maps with real thumbnails and author times, drag-and-drop upload new `.Map.Gbx` files, and load or create MatchSettings playlists instantly.
* **Online Server Setup**: Add, configure, and test dedicated server profiles directly in the browser—no manual file editing required.
* **Fast & Mobile-Friendly**: Clean, modern interface with instant Light / Dark mode switching.
* **Zero-Hassle Deployment**: Runs on any standard PHP 8.2+ server or out-of-the-box with Docker.

---

## 🚀 Requirements

* Web Server (Apache, Nginx, Caddy, or built-in PHP server)
* **PHP 8.2+** with `curl`, `simplexml`, and `json` extensions
* Dedicated TrackMania Forever / ManiaPlanet / Trackmania 2020 server with XML-RPC enabled

---

## 📦 Quick Start / Installation

1. Clone or unpack the repository into your web document root.
2. Edit `config/adminserv.php` to set your dedicated server IP, XML-RPC port (default `5000`), and passwords:
   ```php
   'servers' => [
       'My TrackMania Server' => [
           'address' => '127.0.0.1',
           'port' => 5000,
           'admin_passwords' => [
               'SuperAdmin' => 'SuperAdmin',
               'Admin' => 'Admin',
               'User' => 'User',
           ],
       ],
   ],
   ```
3. Open AdminServ in your browser!

### Running with Built-in PHP Server (for local testing)
```bash
php -S localhost:8080 index.php
```

---

## 🛠️ Development (Building Frontend)

If you modify the Preact components in `frontend/`:
```bash
npm install
npm run build
```
Compiled assets will automatically be bundled into `public/assets/`.

---

## 📁 Project Structure

```
.
├── config/
│   └── adminserv.php          # Server definitions, admin roles, and app config
├── src/
│   ├── Controllers/           # REST API Controllers (Auth, Dashboard, Players, Maps, Config)
│   ├── Services/              # Core Services (GbxRemoteClient, GbxFetcher, AuthManager, MapBrowser)
│   └── App.php                # PSR-4 router and request dispatcher
├── frontend/
│   ├── src/                   # Preact components, DaisyUI styles, and API client
│   └── index.html
├── public/
│   └── assets/                # Pre-compiled static JS & CSS bundle (~20 KB gzip)
└── index.php                  # Root entrypoint
```

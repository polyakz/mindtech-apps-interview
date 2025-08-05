# Mindtech interview API

Project for Mindtech interview

---

## Requirements

- PHP 8.2+
- Composer
- Artisan

---

## Installation & Setup

1. **Clone the repository:**

```bash
git clone https://github.com/polyakz/mindtech-apps-interview
cd <project-folder>
```

2. Install and run:
```
docker-compose up -d --build
php artisan migrate
php artisan l5-swagger:generate
```

3. Env variables can be found in `.env`

4. Application served on `localhost` or `127.0.0.1`

Notes:
- Unfortunately the app is not running as expected, database gets connection refused, while existing and accessible

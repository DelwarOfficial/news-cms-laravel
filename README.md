# NewsCore — Professional Laravel News CMS

**Modern, Fast & News-Focused Content Management System**

Built with Laravel 12 | Tailwind CSS | Alpine.js

---

## 🚀 Overview

**NewsCore** is a specialized **News Content Management System** designed for news organizations, magazines, and digital publishers.

---

## ✨ Key Features

### News-Specific
- Breaking News Ticker
- Featured / Trending / Editor's Pick flags
- Reading Time auto-calculation
- Journalist + Copy Editor workflow support

### Core Modules
- Posts, Categories, Tags (Full CRUD)
- Media Library
- Users & Roles (Permission system)
- Settings Panel
- SEO + Sitemap Generation
- REST API (v1)

### Frontend (separate repository)
Frontend views, controllers, and routing live in the dedicated **[Dhaka Magazine UI](https://github.com/DelwarOfficial/news-cms-frontend)** repository, which reads from the same MySQL database via the CMS's public REST API (v1).

---

## 🛠 Installation

```bash
cd news-cms
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

**Admin Login:**
- `http://127.0.0.1:8000/admin`
- `admin@newscore.com` / `password123`

---

## 📁 Project Structure

Strict separation between CMS (this repo) and Frontend UI (separate repo). Frontend reads via the public REST API only.

```text
news-cms/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdvertisementController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── MediaController.php
│   │   │   │   ├── PostController.php
│   │   │   │   ├── SettingController.php
│   │   │   │   ├── SitemapController.php
│   │   │   │   ├── TagController.php
│   │   │   │   └── WidgetController.php
│   │   │   ├── Api/
│   │   │   │   └── PostApiController.php
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Jobs/
│   │   └── ProcessPostPublishing.php
│   ├── Models/
│   │   ├── Advertisement.php
│   │   ├── Category.php
│   │   ├── Post.php
│   │   ├── Revision.php
│   │   ├── Tag.php
│   │   ├── User.php
│   │   └── Widget.php
│   ├── Policies/
│   │   ├── CategoryPolicy.php
│   │   ├── PostPolicy.php
│   │   └── UserPolicy.php
│   └── Resources/
│       └── Api/
│           ├── CategoryResource.php
│           └── PostResource.php
│
├── database/
│   ├── migrations/        ← All table migrations here
│   └── seeders/
│       ├── AdminUserSeeder.php
│       ├── CategorySeeder.php
│       ├── DatabaseSeeder.php
│       ├── RolePermissionSeeder.php
│       └── SettingSeeder.php
│
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── categories/
│       │   │   └── index.blade.php
│       │   ├── media/
│       │   │   └── index.blade.php
│       │   ├── posts/
│       │   │   ├── create.blade.php
│       │   │   └── index.blade.php
│       │   ├── settings/
│       │   │   └── index.blade.php
│       │   ├── tags/
│       │   │   ├── create.blade.php
│       │   │   └── index.blade.php
│       │   └── dashboard.blade.php
│       └── components/
│
├── routes/
│   ├── web.php            ← Auth, redirects, static
│   ├── admin.php          ← Admin panel routes
│   └── api.php            ← REST API routes
│
├── config/
│   └── cms.php            ← Custom CMS config
│
└── storage/
    └── app/public/media/  ← Uploaded media files
```

---

**Version 1.0 — May 2026**

**Developed by:**  
**Delwar Hossain**  
[delwarhossain.net](https://delwarhossain.net)  
hello@delwarhossain.net

*Built as a complete, original News Content Management System.*
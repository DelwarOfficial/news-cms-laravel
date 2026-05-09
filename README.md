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
- Comments Moderation
- Users & Roles (Permission system)
- Settings Panel
- SEO + Sitemap Generation
- REST API (v1)

### Frontend
- Clean News Homepage
- Article Detail pages with SEO
- Category pages
- Facebook Comments

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

Follows professional Laravel conventions with clear separation between Admin and Frontend.

```text
news-cms/
│
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── GenerateSitemap.php
│   │       └── PublishScheduledPosts.php
│   │
│   ├── Exceptions/
│   │   └── Handler.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── PostController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── PageController.php
│   │   │   │   ├── MediaController.php
│   │   │   │   ├── MenuController.php
│   │   │   │   ├── TagController.php
│   │   │   │   ├── CommentController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── RoleController.php
│   │   │   │   ├── AdvertisementController.php
│   │   │   │   ├── WidgetController.php
│   │   │   │   ├── LanguageController.php
│   │   │   │   └── SettingController.php
│   │   │   │
│   │   │   ├── Api/
│   │   │   │   ├── PostApiController.php
│   │   │   │   ├── CategoryApiController.php
│   │   │   │   ├── SearchApiController.php
│   │   │   │   └── AuthApiController.php
│   │   │   │
│   │   │   └── Front/
│   │   │       ├── HomeController.php
│   │   │       ├── PostController.php
│   │   │       ├── CategoryController.php
│   │   │       ├── SearchController.php
│   │   │       └── PageController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   ├── LocaleMiddleware.php
│   │   │   └── ActivityLogMiddleware.php
│   │   │
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StorePostRequest.php
│   │       │   ├── StoreCategoryRequest.php
│   │       │   ├── StorePageRequest.php
│   │       │   └── StoreUserRequest.php
│   │       └── Api/
│   │           └── SearchRequest.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   ├── Category.php
│   │   ├── Tag.php
│   │   ├── Page.php
│   │   ├── Media.php
│   │   ├── MediaFolder.php
│   │   ├── Comment.php
│   │   ├── Menu.php
│   │   ├── MenuItem.php
│   │   ├── Advertisement.php
│   │   ├── Widget.php
│   │   ├── Language.php
│   │   ├── Translation.php
│   │   ├── Setting.php
│   │   └── Revision.php
│   │
│   ├── Services/
│   │   ├── PostService.php
│   │   ├── MediaService.php
│   │   ├── SeoService.php
│   │   ├── MenuService.php
│   │   ├── SettingService.php
│   │   ├── SitemapService.php
│   │   └── CacheService.php
│   │
│   ├── Policies/
│   │   ├── PostPolicy.php
│   │   ├── CategoryPolicy.php
│   │   └── UserPolicy.php
│   │
│   ├── Resources/
│   │   └── Api/
│   │       ├── PostResource.php
│   │       ├── PostCollection.php
│   │       ├── CategoryResource.php
│   │       └── UserResource.php
│   │
│   └── Observers/
│       └── PostObserver.php
│
├── database/
│   ├── migrations/        ← All table migrations here
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RolePermissionSeeder.php
│   │   ├── AdminUserSeeder.php
│   │   ├── CategorySeeder.php
│   │   └── SettingSeeder.php
│   └── factories/
│
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   ├── layouts/
│   │   │   │   ├── app.blade.php
│   │   │   │   ├── sidebar.blade.php
│   │   │   │   └── header.blade.php
│   │   │   ├── dashboard/
│   │   │   ├── posts/
│   │   │   ├── categories/
│   │   │   ├── pages/
│   │   │   ├── media/
│   │   │   ├── menus/
│   │   │   ├── users/
│   │   │   ├── comments/
│   │   │   ├── advertisements/
│   │   │   ├── settings/
│   │   │   └── roles/
│   │   │
│   │   ├── front/
│   │   │   ├── layouts/
│   │   │   │   ├── app.blade.php
│   │   │   │   ├── header.blade.php
│   │   │   │   └── footer.blade.php
│   │   │   ├── home/
│   │   │   ├── post/
│   │   │   ├── category/
│   │   │   ├── search/
│   │   │   └── page/
│   │   │
│   │   └── components/
│   │       ├── admin/
│   │       └── front/
│   │
│   ├── css/
│   └── js/
│
├── routes/
│   ├── web.php            ← Frontend routes
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
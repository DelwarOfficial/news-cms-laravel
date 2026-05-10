# News CMS Laravel - Admin Dashboard Features Overview

**Generated:** May 10, 2026  
**Project:** news-cms-laravel

---

## Table of Contents

1. [Admin Routes](#admin-routes)
2. [Admin Controllers](#admin-controllers)
3. [Admin API Endpoints](#admin-api-endpoints)
4. [Models & Relationships](#models--relationships)
5. [Security & Permissions](#security--permissions)
6. [Admin Database Schema](#admin-database-schema)
7. [Background Jobs](#background-jobs)
8. [Admin Views Structure](#admin-views-structure)
9. [Configuration Files](#configuration-files)
10. [Feature Gaps & Recommendations](#feature-gaps--recommendations)

---

## Admin Routes

**File:** [routes/web.php](routes/web.php)

All admin routes are protected by `auth` middleware and prefixed with `/admin`.

### Authentication Routes
- `GET /login` → [AuthController@login](app/Http/Controllers/Admin/AuthController.php#L1)
  - Displays login form
- `POST /login` → [AuthController@authenticate](app/Http/Controllers/Admin/AuthController.php#L7)
  - Authenticates user credentials with "Remember Me" option
- `POST /logout` → [AuthController@logout](app/Http/Controllers/Admin/AuthController.php#L20)
  - Logs out user and invalidates session

### Main Admin Dashboard
- `GET /admin` → [DashboardController@index](app/Http/Controllers/Admin/DashboardController.php#L10)
  - Displays dashboard with stats: total posts, published, pending, draft posts, users, categories, comments
  - Shows recent posts (8) with authors
  - Shows popular posts (5) by view count

### Post Management
- `GET /admin/posts` → [PostController@index](app/Http/Controllers/Admin/PostController.php#L10)
  - Lists posts with sorting options (title, status, created_at)
  - Authors see only their own posts
  - Paginated (20 per page)
- `GET /admin/posts/create` → [PostController@create](app/Http/Controllers/Admin/PostController.php#L33)
- `POST /admin/posts` → [PostController@store](app/Http/Controllers/Admin/PostController.php#L37)
  - Authors' posts default to "pending" status
  - Admins/Editors can publish directly
- `GET /admin/posts/{id}/edit` → [PostController@edit](app/Http/Controllers/Admin/PostController.php#L54)
- `PUT /admin/posts/{id}` → [PostController@update](app/Http/Controllers/Admin/PostController.php#L58)
- `DELETE /admin/posts/{id}` → [PostController@destroy](app/Http/Controllers/Admin/PostController.php#L69)

### Category Management
- `GET /admin/categories` → [CategoryController@index](app/Http/Controllers/Admin/CategoryController.php#L10)
  - Lists categories with post count
  - Ordered by order field
- `GET /admin/categories/create` → [CategoryController@create](app/Http/Controllers/Admin/CategoryController.php#L15)
- `POST /admin/categories` → [CategoryController@store](app/Http/Controllers/Admin/CategoryController.php#L21)
  - Supports hierarchical parent categories
- `GET /admin/categories/{id}/edit` → [CategoryController@edit](app/Http/Controllers/Admin/CategoryController.php#L33)
- `PUT /admin/categories/{id}` → [CategoryController@update](app/Http/Controllers/Admin/CategoryController.php#L43)
- `DELETE /admin/categories/{id}` → [CategoryController@destroy](app/Http/Controllers/Admin/CategoryController.php#L55)
- `POST /admin/categories/reorder` → [CategoryController@reorder](app/Http/Controllers/Admin/CategoryController.php#L59)
  - AJAX endpoint to reorder categories

### User Management
- `GET /admin/users` → [UserController@index](app/Http/Controllers/Admin/UserController.php#L10)
  - Lists users with roles (paginated, 20 per page)
- `GET /admin/users/create` → [UserController@create](app/Http/Controllers/Admin/UserController.php#L15)
- `POST /admin/users` → [UserController@store](app/Http/Controllers/Admin/UserController.php#L21)
  - Creates user with role assignment
- `GET /admin/users/{id}/edit` → [UserController@edit](app/Http/Controllers/Admin/UserController.php#L40)
- `PUT /admin/users/{id}` → [UserController@update](app/Http/Controllers/Admin/UserController.php#L45)
  - Allows role sync
- `DELETE /admin/users/{id}` → [UserController@destroy](app/Http/Controllers/Admin/UserController.php#L57)

### Media Management
- `GET /admin/media` → [MediaController@index](app/Http/Controllers/Admin/MediaController.php#L13)
  - Lists media with folder structure
  - Paginated (24 per page)
- `POST /admin/media` → [MediaController@store](app/Http/Controllers/Admin/MediaController.php#L19)
  - Uploads file (max 10MB)
  - Stores with UUID filename
  - Dispatches background job for processing
- `DELETE /admin/media/{id}` → [MediaController@destroy](app/Http/Controllers/Admin/MediaController.php#L38)
  - Deletes file from storage and database

### Tags Management
- `GET /admin/tags` → [TagController@index](app/Http/Controllers/Admin/TagController.php#L10)
  - Lists tags (paginated, 20 per page)
- `POST /admin/tags` → [TagController@store](app/Http/Controllers/Admin/TagController.php#L22)
  - Creates new tag with unique name
- `DELETE /admin/tags/{id}` → [TagController@destroy](app/Http/Controllers/Admin/TagController.php#L32)

### Comments Management
- `GET /admin/comments` → [CommentController@index](app/Http/Controllers/Admin/CommentController.php#L10)
  - Lists comments with post info (paginated, 20 per page)
- `POST /admin/comments/{id}/approve` → [CommentController@approve](app/Http/Controllers/Admin/CommentController.php#L15)
  - Approves pending comment
- `DELETE /admin/comments/{id}` → [CommentController@destroy](app/Http/Controllers/Admin/CommentController.php#L19)

### Settings Management
- `GET /admin/settings` → [SettingController@index](app/Http/Controllers/Admin/SettingController.php#L10)
- `POST /admin/settings` → [SettingController@update](app/Http/Controllers/Admin/SettingController.php#L14)
  - (Implementation stub - needs completion)

### Widgets Management
- `GET /admin/widgets` → [WidgetController@index](app/Http/Controllers/Admin/WidgetController.php#L10)
  - Lists widgets ordered by area and order
- `POST /admin/widgets` → [WidgetController@store](app/Http/Controllers/Admin/WidgetController.php#L16)
  - Creates widget with area, type, and title

### Advertisements Management
- `GET /admin/advertisements` → [AdvertisementController@index](app/Http/Controllers/Admin/AdvertisementController.php#L10)
  - Lists advertisements (paginated, 20 per page)
- `POST /admin/advertisements` → [AdvertisementController@store](app/Http/Controllers/Admin/AdvertisementController.php#L17)
  - Creates advertisement (image or code type)

### Sitemap Generation
- `POST /admin/sitemap/generate` → [SitemapController@generate](app/Http/Controllers/Admin/SitemapController.php#L11)
  - Generates sitemap.xml with homepage, all published posts, and categories
  - Uses Spatie Sitemap package

---

## Admin Controllers

**Location:** [app/Http/Controllers/Admin/](app/Http/Controllers/Admin/)

### 1. DashboardController
**File:** [DashboardController.php](app/Http/Controllers/Admin/DashboardController.php)

**Methods:**
- `index()` - Returns dashboard stats and popular content

**Responsibilities:**
- Aggregates admin statistics
- Counts posts by status (published, pending, draft)
- Retrieves recent posts (8)
- Retrieves popular posts (5)

---

### 2. AuthController
**File:** [AuthController.php](app/Http/Controllers/Admin/AuthController.php)

**Methods:**
- `login()` - Display login view
- `authenticate(Request $request)` - Process login with remember me
- `logout(Request $request)` - Logout and invalidate session

**Responsibilities:**
- Session management for admin users
- Credential validation

---

### 3. PostController
**File:** [PostController.php](app/Http/Controllers/Admin/PostController.php)

**Methods:**
- `index(Request $request)` - List posts (with role-based filtering)
- `create()` - Show create form
- `store(Request $request)` - Store new post
- `edit(Post $post)` - Show edit form
- `update(Request $request, Post $post)` - Update post
- `destroy(Post $post)` - Delete post

**Responsibilities:**
- Full CRUD for posts
- Role-based access control (Authors see only their posts)
- Status handling (draft/pending/published)
- Sorting and pagination

---

### 4. CategoryController
**File:** [CategoryController.php](app/Http/Controllers/Admin/CategoryController.php)

**Methods:**
- `index()` - List categories with post counts
- `create()` - Show create form
- `store(Request $request)` - Store category
- `edit(Category $category)` - Show edit form
- `update(Request $request, Category $category)` - Update category
- `destroy(Category $category)` - Delete category
- `reorder(Request $request)` - AJAX reorder endpoint

**Responsibilities:**
- Hierarchical category management
- Category ordering
- Parent-child relationships

---

### 5. UserController
**File:** [UserController.php](app/Http/Controllers/Admin/UserController.php)

**Methods:**
- `index()` - List users with roles
- `create()` - Show create form
- `store(Request $request)` - Create user with role
- `edit(User $user)` - Show edit form
- `update(Request $request, User $user)` - Update user and role
- `destroy(User $user)` - Delete user

**Responsibilities:**
- User account management
- Role assignment using Spatie Permissions
- User validation and creation

---

### 6. MediaController
**File:** [MediaController.php](app/Http/Controllers/Admin/MediaController.php)

**Methods:**
- `index()` - List media with folders
- `store(Request $request)` - Upload media file
- `destroy(Media $media)` - Delete media

**Responsibilities:**
- File upload management
- Storage handling with UUID filenames
- Background job dispatch for processing
- Media folder organization

---

### 7. TagController
**File:** [TagController.php](app/Http/Controllers/Admin/TagController.php)

**Methods:**
- `index()` - List tags
- `store(Request $request)` - Create tag
- `destroy(Tag $tag)` - Delete tag

**Responsibilities:**
- Tag management
- Unique tag validation

---

### 8. CommentController
**File:** [CommentController.php](app/Http/Controllers/Admin/CommentController.php)

**Methods:**
- `index()` - List comments
- `approve(Comment $comment)` - Approve comment
- `destroy(Comment $comment)` - Delete comment

**Responsibilities:**
- Comment moderation
- Status management (pending/approved)

---

### 9. SettingController
**File:** [SettingController.php](app/Http/Controllers/Admin/SettingController.php)

**Methods:**
- `index()` - Display settings form
- `update(Request $request)` - Update settings

**Responsibilities:**
- Application settings management
- (Implementation incomplete)

---

### 10. WidgetController
**File:** [WidgetController.php](app/Http/Controllers/Admin/WidgetController.php)

**Methods:**
- `index()` - List widgets
- `store(Request $request)` - Create widget

**Responsibilities:**
- Widget management for frontend areas
- Widget positioning and ordering

---

### 11. AdvertisementController
**File:** [AdvertisementController.php](app/Http/Controllers/Admin/AdvertisementController.php)

**Methods:**
- `index()` - List advertisements
- `store(Request $request)` - Create advertisement

**Responsibilities:**
- Advertisement management
- Support for image and code-based ads

---

### 12. SitemapController
**File:** [SitemapController.php](app/Http/Controllers/Admin/SitemapController.php)

**Methods:**
- `generate()` - Generate sitemap.xml

**Responsibilities:**
- SEO sitemap generation
- Automatic inclusion of published posts and categories

---

## Admin API Endpoints

**Location:** [app/Http/Controllers/Api/Admin/](app/Http/Controllers/Api/Admin/)  
**Base Path:** `/api/admin/*`  
**Authentication:** Sanctum token (Bearer token in Authorization header)  
**Middleware:** `auth:sanctum`

### 1. AdminPostApiController
**File:** [AdminPostApiController.php](app/Http/Controllers/Api/Admin/AdminPostApiController.php)

**Endpoints:**
- `POST /api/admin/posts` → `store(Request $request)`
  - Permission: `posts.create`
  - Creates post with categories
  - Sets slug from title
  - Request: `{title, content, status, categories[]}`
  - Response: `{status: 'success', data: Post}`

- `PUT /api/admin/posts/{id}` → `update(Request $request, $id)`
  - Permission: `posts.edit.any` OR `posts.edit.own` (with ownership check)
  - Updates post data
  - Request: `{title, content}`
  - Response: `{status: 'success', data: Post}`

- `DELETE /api/admin/posts/{id}` → `destroy(Request $request, $id)`
  - Permission: `posts.delete.any` OR `posts.delete.own` (with ownership check)
  - Deletes post
  - Response: `{status: 'success', message: 'Post deleted successfully'}`

- `PATCH /api/admin/posts/{id}/status` → `status(Request $request, $id)`
  - Permission: `posts.publish`
  - Updates post status
  - Request: `{status: 'draft|pending|published|scheduled|archived'}`
  - Response: `{status: 'success', data: Post}`

---

### 2. AdminMediaApiController
**File:** [AdminMediaApiController.php](app/Http/Controllers/Api/Admin/AdminMediaApiController.php)

**Endpoints:**
- `GET /api/admin/media` → `index(Request $request)`
  - Permission: `media.manage`
  - Lists media with pagination
  - Query params: `per_page` (default 20)
  - Response: `{status: 'success', data: Media[]}`

- `POST /api/admin/media/upload` → `store(Request $request)`
  - Permission: `media.manage`
  - Uploads file (max 10MB)
  - Dispatches `ProcessMediaUpload` background job
  - Request: multipart form-data with `file`
  - Response: `{status: 'success', message: '...', data: Media}`

- `DELETE /api/admin/media/{id}` → `destroy(Request $request, $id)`
  - Permission: `media.manage`
  - Deletes media
  - Response: `{status: 'success', message: 'Media deleted successfully'}`

---

### 3. AdminCommentApiController
**File:** [AdminCommentApiController.php](app/Http/Controllers/Api/Admin/AdminCommentApiController.php)

**Endpoints:**
- `GET /api/admin/comments` → `index(Request $request)`
  - Permission: `comments.manage`
  - Lists comments with post and user info
  - Query params: `per_page` (default 20)
  - Response: `{status: 'success', data: Comment[]}`

- `PATCH /api/admin/comments/{id}` → `status(Request $request, $id)`
  - Permission: `comments.manage`
  - Updates comment status
  - Request: `{status: 'pending|approved|spam|trash'}`
  - Response: `{status: 'success', data: Comment}`

---

## Models & Relationships

**Location:** [app/Models/](app/Models/)

### Core Admin Models

#### 1. User
**File:** [User.php](app/Models/User.php)

**Traits:**
- `HasApiTokens` (Laravel Sanctum)
- `HasFactory`
- `Notifiable`
- `HasRoles` (Spatie Permissions)

**Relationships:**
- `hasMany(Post)` - User's posts
- `hasMany(Media)` - User's uploaded media
- `hasMany(Comment)` - User's comments

**Key Fields:**
- name, username, email, password

---

#### 2. Post
**File:** [Post.php](app/Models/Post.php)

**Traits:**
- `HasFactory`
- `SoftDeletes`
- `HasSlug` (Spatie Sluggable)

**Relationships:**
- `belongsTo(User)` as author
- `belongsTo(Language)`
- `belongsToMany(Category, 'post_categories')`
- `belongsToMany(Tag, 'post_tags')`
- `hasMany(Comment)`
- `belongsToMany(Post, 'post_related')` - Related posts
- `hasMany(PostRevision)` - Version history
- `hasMany(PostTranslation)`

**Key Fields:**
- title, slug, excerpt, content
- status (draft/pending/published/scheduled/archived)
- published_at, scheduled_at
- is_breaking, is_featured, is_trending, is_editors_pick, is_sticky
- urgency_level (1=Normal, 2=Important, 3=Breaking)
- featured_image, featured_image_alt
- meta_title, meta_description, canonical_url, og_image
- view_count, comment_count, allow_comments

**Scopes:**
- `published()` - Posts with status = 'published'

**Accessors:**
- `reading_time` - Calculated from word count

---

#### 3. Category
**File:** [Category.php](app/Models/Category.php)

**Traits:**
- `HasFactory`
- `HasSlug` (Spatie Sluggable)

**Relationships:**
- `belongsTo(Category)` as parent (self-referential)
- `hasMany(Category)` as children
- `belongsToMany(Post, 'post_categories')`
- `hasMany(CategoryTranslation)`

**Key Fields:**
- name, slug, description
- parent_id (for hierarchical structure)
- image, icon
- order (for display ordering)
- status (active/inactive)
- meta_title, meta_description

---

#### 4. Comment
**File:** [Comment.php](app/Models/Comment.php)

**Traits:**
- `HasFactory`

**Relationships:**
- `belongsTo(Post)`
- `belongsTo(User)`
- `belongsTo(Comment)` as parent (for nested comments)
- `hasMany(Comment)` as replies

**Key Fields:**
- post_id, user_id, parent_id
- author_name, author_email
- content
- status (pending/approved/spam/trash)
- ip_address

---

#### 5. Media
**File:** [Media.php](app/Models/Media.php)

**Fillable Fields:**
- user_id, folder_id
- filename, original_name
- disk, path, url
- mime_type, size
- alt_text, caption

---

#### 6. MediaFolder
**File:** [MediaFolder.php](app/Models/MediaFolder.php)

**Fillable Fields:**
- name, slug, parent_id (hierarchical)

---

#### 7. Tag
**File:** [Tag.php](app/Models/Tag.php)

**Traits:**
- `HasFactory`
- `HasSlug` (Spatie Sluggable)

**Relationships:**
- `belongsToMany(Post, 'post_tags')`

**Key Fields:**
- name, slug

---

#### 8. Widget
**File:** [Widget.php](app/Models/Widget.php)

**Traits:**
- `HasFactory`

**Fillable Fields:**
- area, type, title
- config (JSON array)
- order
- is_active (boolean)

---

#### 9. Advertisement
**File:** [Advertisement.php](app/Models/Advertisement.php)

**Traits:**
- `HasFactory`

**Fillable Fields:**
- title, position, type (image/code)
- image, url, code
- start_date, end_date
- is_active (boolean)
- click_count

---

#### 10. Setting
**File:** [Setting.php](app/Models/Setting.php)

**Fillable Fields:**
- key (unique), value
- group (for organizing settings)
- type

---

#### 11. Language
**File:** [Language.php](app/Models/Language.php)

**Purpose:** Multi-language support

---

#### 12. Revision
**File:** [Revision.php](app/Models/Revision.php)

**Purpose:** Post revision history tracking

---

#### 13. Menu & MenuItem
**File:** [Menu.php](app/Models/Menu.php), [MenuItem.php](app/Models/MenuItem.php)

**Purpose:** Dynamic menu management

---

#### 14. Page
**File:** [Page.php](app/Models/Page.php)

**Purpose:** Static pages separate from posts

---

## Security & Permissions

### Role-Based Access Control (RBAC)

**Implementation:** Spatie Permission Package  
**Configuration File:** [config/permission.php](config/permission.php)

### Database Tables
- `roles` - Role definitions
- `permissions` - Permission definitions
- `model_has_permissions` - Direct permission assignments
- `model_has_roles` - Role assignments to users
- `role_has_permissions` - Permission assignments to roles

### Defined Roles

#### 1. Super Admin
**Permissions:** ALL (18 permissions)

**Can:**
- Create, edit (own/any), delete (own/any), publish posts
- Manage all categories, tags, media
- Manage comments
- Manage menus
- Manage users and roles
- Access admin settings
- Manage advertisements
- Manage themes
- Manage API keys

---

#### 2. Admin
**Permissions:** 16 of 18 (excludes: `roles.manage`, `api_keys.manage`)

**Can:**
- Same as Super Admin except cannot manage roles or API keys

---

#### 3. Editor
**Permissions:** 11
- posts.create
- posts.edit.own
- posts.edit.any
- posts.delete.own
- posts.delete.any
- posts.publish
- categories.manage
- tags.manage
- media.manage
- comments.manage
- dashboard.view

---

#### 4. Author
**Permissions:** 6
- posts.create
- posts.edit.own
- posts.delete.own
- tags.manage
- media.manage
- dashboard.view

---

#### 5. Journalist
**Permissions:** 6 (same as Author)
- posts.create
- posts.edit.own
- posts.delete.own
- tags.manage
- media.manage
- dashboard.view

---

#### 6. Contributor
**Permissions:** 5
- posts.create
- posts.edit.own
- tags.manage
- media.manage
- dashboard.view

---

### Authorization Policies

**Location:** [app/Policies/](app/Policies/)

#### PostPolicy
**File:** [PostPolicy.php](app/Policies/PostPolicy.php)

**Methods:**
- `viewAny(User $user)` - Always true (public posts)
- `view(User $user, Post $post)` - Super Admin/Admin/Editor OR author of post
- `create(User $user)` - Everyone authenticated
- `update(User $user, Post $post)` - Super Admin/Admin/Editor OR author of post
- `delete(User $user, Post $post)` - Super Admin/Admin OR author of post
- `publish(User $user)` - Super Admin/Admin/Editor only

---

#### UserPolicy
**File:** [UserPolicy.php](app/Policies/UserPolicy.php)

**Methods:**
- `viewAny(User $user)` - Super Admin/Admin only
- `create(User $user)` - Super Admin/Admin only
- `update(User $user, User $target)` - Super Admin OR Admin (not targeting Super Admin)
- `delete(User $user, User $target)` - Super Admin only (not self)

---

#### CategoryPolicy
**File:** [CategoryPolicy.php](app/Policies/CategoryPolicy.php)

**Methods:**
- `viewAny(User $user)` - Everyone
- `create(User $user)` - Super Admin/Admin/Editor
- `update(User $user, Category $category)` - Super Admin/Admin/Editor
- `delete(User $user, Category $category)` - Super Admin/Admin only

---

### Default Admin Account

**Email:** admin@newscore.com  
**Username:** admin  
**Password:** password123 (from [AdminUserSeeder.php](database/seeders/AdminUserSeeder.php))  
**Role:** Super Admin

⚠️ **Security Note:** Change password immediately in production!

---

## Admin Database Schema

**Location:** [database/migrations/](database/migrations/)

### Posts Table
```sql
CREATE TABLE posts (
  id BIGINT PRIMARY KEY,
  user_id BIGINT (foreign key to users),
  language_id BIGINT (foreign key to languages),
  title VARCHAR(500),
  slug VARCHAR(500) UNIQUE,
  excerpt TEXT,
  content LONGTEXT,
  featured_image VARCHAR,
  featured_image_alt VARCHAR,
  is_breaking BOOLEAN DEFAULT false,
  is_featured BOOLEAN DEFAULT false,
  is_trending BOOLEAN DEFAULT false,
  is_editors_pick BOOLEAN DEFAULT false,
  is_sticky BOOLEAN DEFAULT false,
  urgency_level TINYINT DEFAULT 1,
  status ENUM('draft','pending','published','scheduled','archived') DEFAULT 'draft',
  published_at TIMESTAMP NULL,
  scheduled_at TIMESTAMP NULL,
  meta_title VARCHAR,
  meta_description TEXT,
  canonical_url VARCHAR(500),
  og_image VARCHAR,
  view_count BIGINT DEFAULT 0,
  comment_count INT DEFAULT 0,
  allow_comments BOOLEAN DEFAULT true,
  deleted_at TIMESTAMP NULL (soft deletes),
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  INDEX(status),
  INDEX(published_at)
)
```

**Relevant Migrations:**
- [2026_05_10_000004_create_posts_table.php](database/migrations/2026_05_10_000004_create_posts_table.php)

---

### Categories Table
```sql
CREATE TABLE categories (
  id BIGINT PRIMARY KEY,
  parent_id BIGINT NULL (foreign key to categories),
  name VARCHAR,
  slug VARCHAR UNIQUE,
  description TEXT,
  image VARCHAR,
  icon VARCHAR,
  order INT DEFAULT 0,
  status ENUM('active','inactive') DEFAULT 'active',
  meta_title VARCHAR,
  meta_description TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  INDEX(parent_id),
  INDEX(slug)
)
```

**Relevant Migrations:**
- [2026_05_10_000003_create_categories_table.php](database/migrations/2026_05_10_000003_create_categories_table.php)

---

### Comments Table
```sql
CREATE TABLE comments (
  id BIGINT PRIMARY KEY,
  post_id BIGINT (foreign key to posts),
  parent_id BIGINT NULL (foreign key to comments - for replies),
  user_id BIGINT NULL (foreign key to users),
  author_name VARCHAR,
  author_email VARCHAR,
  content TEXT,
  status ENUM('pending','approved','spam','trash') DEFAULT 'pending',
  ip_address VARCHAR(45),
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  INDEX(post_id),
  INDEX(status)
)
```

**Relevant Migrations:**
- [2026_05_10_000010_create_comments_table.php](database/migrations/2026_05_10_000010_create_comments_table.php)

---

### Media Table
```sql
CREATE TABLE media (
  id BIGINT PRIMARY KEY,
  folder_id BIGINT NULL (foreign key to media_folders),
  user_id BIGINT (foreign key to users),
  name VARCHAR,
  file_name VARCHAR,
  file_path VARCHAR,
  file_url VARCHAR,
  file_type VARCHAR,
  file_size BIGINT,
  width INT NULL,
  height INT NULL,
  alt_text VARCHAR,
  caption TEXT,
  credit VARCHAR,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  INDEX(file_type)
)
```

**Relevant Migrations:**
- [2026_05_10_000009_create_media_table.php](database/migrations/2026_05_10_000009_create_media_table.php)
- [2026_05_10_000008_create_media_folders_table.php](database/migrations/2026_05_10_000008_create_media_folders_table.php)

---

### Tags Table
```sql
CREATE TABLE tags (
  id BIGINT PRIMARY KEY,
  name VARCHAR,
  slug VARCHAR,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
)
```

**Relevant Migrations:**
- [2026_05_10_000005_create_tags_table.php](database/migrations/2026_05_10_000005_create_tags_table.php)

---

### Advertisements Table
```sql
CREATE TABLE advertisements (
  id BIGINT PRIMARY KEY,
  title VARCHAR,
  position VARCHAR,
  type ENUM('image','code') DEFAULT 'code',
  image VARCHAR,
  url VARCHAR,
  code TEXT,
  start_date DATE NULL,
  end_date DATE NULL,
  is_active BOOLEAN DEFAULT true,
  click_count BIGINT DEFAULT 0,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  INDEX(position),
  INDEX(is_active)
)
```

**Relevant Migrations:**
- [2026_05_10_000014_create_advertisements_table.php](database/migrations/2026_05_10_000014_create_advertisements_table.php)

---

### Settings Table
```sql
CREATE TABLE settings (
  id BIGINT PRIMARY KEY,
  key VARCHAR UNIQUE,
  value LONGTEXT,
  group VARCHAR DEFAULT 'general',
  type VARCHAR DEFAULT 'text',
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  INDEX(key),
  INDEX(group)
)
```

**Relevant Migrations:**
- [2026_05_10_000013_create_settings_table.php](database/migrations/2026_05_10_000013_create_settings_table.php)

---

### Permissions & Roles Tables (Spatie)
**Relevant Migrations:**
- [2026_05_10_123125_create_permission_tables.php](database/migrations/2026_05_10_123125_create_permission_tables.php)

---

## Background Jobs

**Location:** [app/Jobs/](app/Jobs/)

### 1. ProcessMediaUpload
**File:** [ProcessMediaUpload.php](app/Jobs/ProcessMediaUpload.php)

**Trigger:** Dispatched when media is uploaded via API

**Queue Configuration:**
- Implements `ShouldQueue` interface
- Uses `Queueable` trait

**Responsibilities:**
- Generate WebP versions of images
- Create thumbnails
- Optimize image files
- Log processing completion

**Implementation Note:** Currently has simulation with sleep(2), needs actual implementation for production

---

### 2. ProcessPostPublishing
**File:** [ProcessPostPublishing.php](app/Jobs/ProcessPostPublishing.php)

**Trigger:** Can be dispatched when post status changes to 'published'

**Queue Configuration:**
- Implements `ShouldQueue` interface
- Uses Queueable, InteractsWithQueue traits
- Serializable with SerializesModels

**Responsibilities:**
- Send admin notifications
- Update sitemap
- Clear cache
- Push notifications to subscribers

**Implementation Note:** Stub implementation - needs completion

---

## Admin Views Structure

**Location:** [resources/views/admin/](resources/views/admin/)

### View Directory Structure
```
admin/
├── auth/
│   └── login.blade.php
├── categories/
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── index.blade.php
├── comments/
│   └── (index, show views)
├── dashboard.blade.php
├── layouts/
│   └── app.blade.php
├── media/
│   └── (index view)
├── posts/
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── index.blade.php
├── settings/
│   └── (settings view)
└── tags/
    └── (tags view)
```

### Main Layout
**File:** [resources/views/admin/layouts/app.blade.php](resources/views/admin/layouts/app.blade.php)

Provides:
- Main navigation/sidebar
- Admin header
- Flash messages
- CSS/JS includes

### Dashboard View
**File:** [resources/views/admin/dashboard.blade.php](resources/views/admin/dashboard.blade.php)

Displays:
- Statistics cards (posts, users, categories, comments)
- Recent posts list
- Popular posts list

### Post Views
**Location:** [resources/views/admin/posts/](resources/views/admin/posts/)

- **index.blade.php** - List with sorting/pagination
- **create.blade.php** - Create form
- **edit.blade.php** - Edit form

### Category Views
**Location:** [resources/views/admin/categories/](resources/views/admin/categories/)

- **index.blade.php** - List with hierarchical display
- **create.blade.php** - Create form (parent selection)
- **edit.blade.php** - Edit form with parent selection

### Media Views
**Location:** [resources/views/admin/media/](resources/views/admin/media/)

- **index.blade.php** - Media library with folder structure

### Other Views
- [resources/views/admin/comments/](resources/views/admin/comments/)
- [resources/views/admin/tags/](resources/views/admin/tags/)
- [resources/views/admin/settings/](resources/views/admin/settings/)

---

## Configuration Files

### 1. CMS Configuration
**File:** [config/cms.php](config/cms.php)

```php
return [
    'name' => env('APP_NAME', 'NewsCore'),
    'version' => '1.0.0',
    'description' => 'Professional News Content Management System',
    'posts_per_page' => 12,
    'enable_comments' => true,
    'enable_registration' => false,
    'seo' => [
        'default_meta_title' => 'NewsCore - Latest News & Breaking Stories',
        'default_meta_description' => '...'
    ]
];
```

---

### 2. Permission Configuration
**File:** [config/permission.php](config/permission.php)

Spatie Permission package configuration:
- Role model: `Spatie\Permission\Models\Role`
- Permission model: `Spatie\Permission\Models\Permission`
- Table names configured (roles, permissions, model_has_permissions, etc.)

---

### 3. Sanctum Configuration
**File:** [config/sanctum.php](config/sanctum.php)

API token authentication for admin API endpoints

---

### 4. Application Configuration
**File:** [config/app.php](config/app.php)

Standard Laravel app config

---

## Feature Gaps & Recommendations

### High Priority (Critical)

1. **Settings Management Incomplete**
   - Location: [SettingController.php](app/Http/Controllers/Admin/SettingController.php)
   - `update()` method has no implementation
   - Recommendation: Implement database persistence for application settings
   - Suggested Settings: Site title, description, contact info, social links, etc.

2. **Missing Post Revision History**
   - Posts table has soft deletes but no full revision tracking
   - Model exists: [Revision.php](app/Models/Revision.php) but not implemented
   - Recommendation: Implement post version history with Revision model

3. **Incomplete Background Jobs**
   - ProcessPostPublishing job is stub only
   - ProcessMediaUpload has mock implementation
   - Recommendation: Implement proper image processing, notifications, cache clearing

4. **Category Reorder API**
   - Exists but may need frontend integration testing
   - Recommendation: Verify drag-and-drop functionality in views

### Medium Priority (Important)

5. **Widget Management Limited**
   - Only index and store methods implemented
   - Missing edit/update/delete
   - Recommendation: Complete CRUD for widgets

6. **Advertisement Management Limited**
   - Only index and store methods
   - Missing edit/update/delete
   - Recommendation: Complete CRUD + add scheduling validation

7. **Comment Status Moderation**
   - Has pending/approved/spam/trash statuses
   - Missing bulk actions
   - Recommendation: Add bulk approve/reject/spam actions

8. **Media Management**
   - No folder management endpoints in API
   - Missing thumbnail generation display
   - Recommendation: Complete folder CRUD

9. **User Role Limits**
   - No validation on role assignment
   - Editors can manage other editors
   - Recommendation: Add hierarchy validation (lower roles can't edit higher)

### Low Priority (Enhancement)

10. **Multi-language Support**
    - Language model exists but not fully integrated
    - Posts have language_id but UI doesn't support switching
    - Recommendation: Implement multi-language post editor

11. **SEO Features**
    - Models have meta fields but no preview/validation in UI
    - Recommendation: Add SEO preview and validation

12. **Analytics Integration**
    - view_count field exists but no analytics dashboard
    - Recommendation: Add view trends, engagement metrics

13. **API Documentation**
    - No OpenAPI/Swagger documentation
    - Recommendation: Add API documentation

14. **Export Features**
    - No export to CSV/PDF for posts, users, comments
    - Recommendation: Add export functionality

15. **Search & Filtering**
    - Posts can sort but search functionality limited
    - Recommendation: Implement full-text search, advanced filtering

### Security Recommendations

1. **Password Policy**
   - Default password "password123" should be enforced to change
   - Recommendation: Add password expiration, complexity validation

2. **API Rate Limiting**
   - No apparent rate limiting on API endpoints
   - Recommendation: Add throttle middleware

3. **Audit Logging**
   - No audit trail for admin actions
   - Recommendation: Log all admin actions with timestamps and user info

4. **Two-Factor Authentication**
   - Not implemented
   - Recommendation: Add 2FA for admin accounts

5. **CORS Configuration**
   - Check CORS settings for API endpoints
   - Recommendation: Restrict CORS to specific domains

---

## Summary Statistics

**Total Admin Controllers:** 12 web + 3 API = 15 controllers

**Total Routes:** ~25 web routes + 7 API routes = 32 routes

**Total Models:** 14 models (User, Post, Category, Comment, Media, etc.)

**Total Policies:** 3 policies (Post, User, Category)

**Total Jobs:** 2 background jobs

**Total Permissions:** 18 defined permissions

**Total Roles:** 6 roles (Super Admin, Admin, Editor, Author, Journalist, Contributor)

**Database Tables:** 16+ tables (posts, categories, comments, media, users, permissions, roles, etc.)

---

## Quick Setup

1. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

2. **Seed Database with Default Roles & Admin User:**
   ```bash
   php artisan db:seed
   ```

3. **Default Admin Login:**
   - Email: `admin@newscore.com`
   - Password: `password123`

4. **Generate API Token (for testing API endpoints):**
   ```bash
   php artisan tinker
   > $user = App\Models\User::find(1)
   > $token = $user->createToken('admin-token')->plainTextToken
   ```

5. **Access Admin Dashboard:**
   - URL: `http://localhost/admin`
   - Login with admin credentials

---

**Document Generated:** May 10, 2026  
**Framework:** Laravel 11  
**PHP Version:** 8.2+

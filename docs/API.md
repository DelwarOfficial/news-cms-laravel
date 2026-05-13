# NewsCore CMS — API Documentation

## Base URL

```
http://localhost/dhaka-magazine/api
```

## Authentication

### API Key (V1)
All V1 authenticated endpoints require an API key.

```
Header: X-API-Key: nh_<key>
Header: Authorization: Bearer nh_<key>
Query:  ?api_key=nh_<key>
```

**Scopes:** `read`, `write`, `media`, `comments`, `cms`, `admin`, `*`

### Session (Admin)
Admin panel endpoints use Laravel Sanctum session auth via `/login`.

---

## Response Format

### Success (list)
```json
{
  "data": [...],
  "meta": {
    "page": 1,
    "limit": 15,
    "total": 145,
    "totalPages": 10
  }
}
```

### Success (single)
```json
{
  "data": { ... }
}
```

### Error
```json
{
  "error": "Validation Error",
  "message": "The given data was invalid.",
  "details": { "title": ["The title field is required."] }
}
```

### Status codes
- `200` OK
- `201` Created
- `204` No Content (deletion)
- `401` Unauthorized (missing/invalid API key)
- `403` Forbidden (insufficient scope)
- `404` Not Found
- `409` Conflict
- `422` Validation Error
- `500` Server Error

---

## Rate Limiting

| Layer | Limit |
|-------|-------|
| Public (no auth) | 60 requests/minute |
| API Key authenticated | 120 requests/minute |
| CMS API (cms scope) | 60 requests/minute |
| View increment | 1 request/60 seconds per IP |

Headers returned: `X-RateLimit-Limit`, `X-RateLimit-Remaining`

---

## V1 — Public (read-only, no auth)

### `GET /v1/posts`
List published posts with filters.

**Query params:**
| Param | Type | Description |
|-------|------|-------------|
| `limit` | int | 1-50, default 15 |
| `sort` | string | `latest` (default), `oldest`, `popular`, `title` |
| `category_slug` | string | Filter by category slug |
| `tag_slug` | string | Filter by tag slug |
| `author_id` | int | Filter by author |
| `date_from` | date | `Y-m-d` |
| `date_to` | date | `Y-m-d` |
| `search` | string | Full-text search in title/excerpt |
| `is_breaking` | bool | |
| `is_featured` | bool | |
| `is_trending` | bool | |
| `is_editors_pick` | bool | |
| `is_sticky` | bool | |
| `post_format` | string | `standard`, `video`, `gallery`, `opinion`, `live` |

### `GET /v1/posts/{slug}`
Single post by slug. Increments view count.

### `GET /v1/posts/breaking`
Latest 10 breaking posts.

### `GET /v1/posts/trending`
Latest 10 trending posts.

### `GET /v1/posts/popular`
Top 10 by view count.

### `GET /v1/posts/featured`
Latest 5 featured posts.

### `GET /v1/posts/editors-pick`
Latest 5 editor's pick posts.

### `POST /v1/posts/{id}/view`
Increment post view count (rate limited: 1/60s).

### `GET /v1/categories`
All active categories in tree structure (parents with nested `children[]`).

### `GET /v1/categories/{slug}/posts`
Paginated posts for a category. Supports `date_from`, `date_to`, `is_breaking`, `is_featured`, `is_trending`, `is_sticky`, `is_editors_pick` filters.

### `GET /v1/search`
Full-text search. Supports `q` (query), `category_slug`, `is_breaking`, `is_featured`, `is_trending`, `date_from`, `date_to`.

---

## Post Resource Fields

```json
{
  "id": 1,
  "title": "Article Title",
  "slug": "article-title",
  "excerpt": "Summary text...",
  "image_url": "http://...",
  "published_at": "2026-05-14T12:00:00.000000Z",
  "reading_time": 3,
  "view_count": 42,
  "is_breaking": false,
  "is_featured": true,
  "is_trending": false,
  "is_editors_pick": false,
  "is_sticky": false,
  "post_format": "standard",
  "category": "Sports",
  "category_slug": "sports",
  "author": "Author Name",
  "author_username": "author",
  "author_avatar": null,
  "tags": ["tag1", "tag2"],
  "meta_title": "...",
  "meta_description": "...",
  "canonical_url": "http://...",
  "og_image": "http://...",
  "locale": "en"
}
```

---

## V1 — Authenticated (API Key required, any scope)

### Posts
| Method | Path | Description |
|--------|------|-------------|
| GET | `/v1/posts/manage` | List all posts (includes drafts). Filters: `status`, `category_id`, `author_id`, `date_from`, `date_to`, `search` |
| POST | `/v1/posts/manage` | Create post |
| GET | `/v1/posts/manage/{id}` | Show post by ID |
| PUT | `/v1/posts/manage/{id}` | Update post |
| DELETE | `/v1/posts/manage/{id}` | Hard delete |

### Categories
| Method | Path | Description |
|--------|------|-------------|
| GET | `/v1/categories/manage` | List all categories |
| POST | `/v1/categories/manage` | Create category |
| GET | `/v1/categories/manage/{id}` | Show category |
| PUT | `/v1/categories/manage/{id}` | Update category |
| DELETE | `/v1/categories/manage/{id}` | Delete category (409 if has posts) |

### Tags
| Method | Path | Description |
|--------|------|-------------|
| GET | `/v1/tags/manage` | List all tags |
| POST | `/v1/tags/manage` | Create tag (name + slug) |
| GET | `/v1/tags/manage/{id}` | Show tag |
| PUT | `/v1/tags/manage/{id}` | Update tag |
| DELETE | `/v1/tags/manage/{id}` | Delete tag (409 if attached to posts) |

### Media
| Method | Path | Description |
|--------|------|-------------|
| GET | `/v1/media` | List media. Filters: `folder_id`, `type`, `search` |
| POST | `/v1/media` | Upload file (multipart, max 10MB) |
| DELETE | `/v1/media/{id}` | Delete media file + record |

### Menus
| Method | Path | Description |
|--------|------|-------------|
| GET/POST | `/v1/menus` | List / Create menus |
| GET/PUT/DELETE | `/v1/menus/{id}` | Show / Update / Delete menu |
| POST | `/v1/menus/{menu}/items` | Add menu item (nested under menu) |
| PUT | `/v1/menus/{menu}/items/{item}` | Update menu item |
| DELETE | `/v1/menus/{menu}/items/{item}` | Delete menu item |

### Widgets
| Method | Path | Description |
|--------|------|-------------|
| GET/POST | `/v1/widgets` | List / Create widget |
| GET/PUT/DELETE | `/v1/widgets/{id}` | Show / Update / Delete widget |
| POST | `/v1/widgets/{id}/toggle` | Toggle is_active |

### Advertisements
| Method | Path | Description |
|--------|------|-------------|
| GET/POST | `/v1/advertisements` | List / Create ad |
| GET/PUT/DELETE | `/v1/advertisements/{id}` | Show / Update / Delete ad |

### Settings
| Method | Path | Description |
|--------|------|-------------|
| GET | `/v1/settings` | List all settings (cached) |
| POST | `/v1/settings` | Batch update `{"settings": {"key": "value"}}` |

### Sitemap
| Method | Path | Description |
|--------|------|-------------|
| GET | `/v1/sitemap` | JSON array of published post URLs |

### Revisions
| Method | Path | Description |
|--------|------|-------------|
| GET | `/v1/revisions` | Paginated revisions. Filter: `post_id` |

### Dashboard
| Method | Path | Description |
|--------|------|-------------|
| GET | `/v1/dashboard` | Stats: total/published posts, categories, tags, media |

---

## V1 — CMS API (requires `cms` scope, for external push/sync)

### `GET /v1/cms/status`
Health check.

### `POST /v1/cms/posts`
Create post from external CMS. Key features:
- `category_slug` — auto-resolves or creates category
- `tag_names[]` — auto-creates tags via `firstOrCreate`
- `featured_image_url` — downloads image via HTTP, stores as Media, attaches as featured
- `raw_import_payload` — stores original JSON for audit
- `source_url` / `source_name` — attribution fields

**Request body:**
```json
{
  "title": "Breaking News",
  "body": "<p>Full article here</p>",
  "status": "published",
  "category_slug": "technology",
  "tag_names": ["Tech", "AI", "Innovation"],
  "featured_image_url": "https://example.com/photo.jpg",
  "source_url": "https://original-source.com/article",
  "source_name": "Partner News Agency",
  "is_breaking": true,
  "meta_title": "SEO Title",
  "meta_description": "SEO description"
}
```

### `PUT /v1/cms/posts/{id}`
Update post. Supports partial updates. Replaces tags if `tag_names` provided. Downloads new image if `featured_image_url` changed.

### `DELETE /v1/cms/posts/{id}`
Soft-delete: sets status to `archived` + `deleted_at`.

### Categories & Tags
| Method | Path | Description |
|--------|------|-------------|
| GET | `/v1/cms/categories` | List all categories |
| POST | `/v1/cms/categories` | Create category (supports `parent_slug`) |
| GET | `/v1/cms/tags` | List all tags |
| POST | `/v1/cms/tags` | Create tag (name-based, idempotent) |
| POST | `/v1/cms/media` | Multipart file upload |

---

## Legacy API (backward compatible)

The following endpoints from the original API remain active:
- `GET /api/posts`, `/api/posts/{slug}`
- `GET /api/categories`, `/api/categories/{slug}/posts`
- `GET /api/tags/{slug}/posts`
- `GET /api/search`, `/api/trending`, `/api/breaking`, `/api/featured`
- `POST /api/auth/login`, `/api/auth/logout`, `/api/auth/me`
- `POST /api/admin/posts`, `/api/admin/media`, `/api/admin/comments`

---

## Authentication — Admin Panel

1. Log in at `http://localhost/dhaka-magazine/login`
2. Navigate to **API Keys** in sidebar
3. Click **Create API Key**, name it, select scopes
4. **Copy the key immediately** — it is shown only once
5. Use the key in `X-API-Key` header for V1 API requests

# Heritage Basket — Admin

Filament admin panel for managing Heritage Basket's catalog, blog, and orders.

This is one of **two separate Laravel apps** sharing one MySQL database:
- `thehertiagebasket/admin` (this project) — this admin panel.
- `thehertiagebasket/frontend` — the public storefront.

## Stack

Laravel 13 + Filament 3. Filament's own assets are pre-published static files (`public/css/filament`, `public/js/filament`) — **this app does not need npm/Vite at all**, it's pure PHP.

## Local URL

- **http://theheritagebasket.admin.local/admin** (nginx vhost via Laragon — see `H:\laragon\etc\nginx\sites-enabled\theheritagebasket.admin.local.conf`)
- Or `php artisan serve --port=8001` → http://127.0.0.1:8001/admin

## Login

```
admin@heritagebasket.test / password
```

Create more admin users with `php artisan make:filament-user`.

## Database — this app owns the schema

Shared MySQL database `heritagebasket` (root / no password / 127.0.0.1:3306). **All migrations live here and are only ever run from this app.** The `frontend` app has its own copies of the Eloquent models pointing at the same tables but never runs `php artisan migrate`.

If you change the schema:
1. Add/edit migration files here, run `php artisan migrate`.
2. Mirror the relevant Eloquent model changes into `../frontend/app/Models/`.
3. Update Filament resources here if the change is admin-facing.

Auth uses its own `admin_users` table/model (not the default Laravel `users` table) — this was deliberate, to avoid any collision with a future customer-facing `users` table on the frontend, since both apps hit the same database.

## Filament resources

- **Products** — includes a repeater for gallery images (`product_images` table) and care instructions.
- **Categories** — the only taxonomy right now (regions were dropped, see below).
- **Blog Posts**
- **Orders** — read-mostly; only `status` and `notes` are editable (orders are created by the frontend checkout, not manually here). Includes a line-items relation manager. No "create" page — orders only come from real checkouts.
- **Newsletter Subscribers** — read-only list.

## Known limitations / things to revisit

- **Product images are hotlinked** from the Stitch mockup tool's CDN (`lh3.googleusercontent.com/aida-public/...`) — seeded that way on purpose to match the mockups immediately, but these are third-party URLs outside our control. Use the Product form's "Cover image URL" / gallery fields to swap in real photography (plain URL strings, not file uploads) when available.
- **Regions were dropped** per an explicit decision partway through the build — there was a `regions` table and `Region` model/resource, removed via migration `2026_07_24_162321_drop_regions_and_region_id_from_products.php`. If a real regional structure is wanted later (the original mockups had Punjab/Sindh/Balochistan/KPK/Multan), re-add as a new migration + model + Filament resource + `region_id` on `products`.
- No customer accounts yet on the frontend — if added later, they'll need their own `users` table (not `admin_users`, which is this app's own auth table).

## Commands

```bash
php artisan serve --port=8001
php artisan migrate            # only ever run here, never in frontend
php artisan db:seed            # re-seed categories/products/blog posts
php artisan make:filament-user # create another admin login
```
# theheritagebucket.backend

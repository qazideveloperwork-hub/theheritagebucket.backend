
# Product Management Rework — Change Log

Session date: 2026-07-25/26. Scope: redesigned the Product Create screen to support
Simple vs. Variable products, and added supporting master-data screens (Suppliers,
Colors, Sizes, Info Library) under a new **Stock** sidebar submenu.

**2026-07-26 (continued)**: a second pass added Product edit, a real Inventory
screen, auto-generated SKUs, new pricing/merchandising fields (`product_code`,
`discount_percentage`, `sale_price`, `is_coming_soon`), full Category CRUD with
status + `cat_code`, and converted products from a single category to
many-to-many. See "2026-07-26 additions" near the bottom for details — several
claims earlier in this doc (no edit screen, `purchase.stock` is a stub, SKU is
manually typed) are from the *first* pass and are now out of date; corrected
inline below where practical.

## Why

The original `products` table was flat (one SKU, one price, one stock count per
product) with no way to model size variations, no supplier/color relationships, and
no reusable "info popup" content (e.g. a size guide shown when a customer picks a
size). This rework adds all of that without disturbing the rest of the app (orders,
cart, blog, etc. were untouched).

## Database changes

New tables:
- `sizes` — `name`, `sort_order`
- `colors` — `name`, `hex_code` (nullable, used for a swatch)
- `suppliers` — `name`, `phone`, `city`, `address`, `description`
- `product_colors` — pivot, `product_id` + `color_id` (many-to-many; a product can be
  tagged with several colors)
- `product_variations` — `product_id`, `size_id`, `sku` (unique), `regular_price`,
  `discount_price`, `purchase_price`, `stock_quantity`, `stock_status`,
  `stock_status_manual`
- `infos` — reusable info-popup library: `icon`, `title`, `content`. Created once,
  attachable to any number of variations (avoids re-typing the same "Size Guide"
  content per product).
- `product_info` — pivot, `product_variation_id` + `info_id` + `sort_order`

Changed on `products`:
- `price` renamed → `regular_price`; `sale_price` renamed → `discount_price`
- Added `type` (`simple` | `variable`), `supplier_id` (FK), `purchase_price`,
  `stock_status`, `stock_status_manual`, `floating_image_url`
- `sku`, `regular_price`, `stock_quantity` made nullable — for `type = variable`
  products these live on `product_variations` instead; the parent row has none of
  its own.
- Dropped the old single `color_id`-style column in favor of the `product_colors`
  pivot (a product can have multiple colors).

Migration files (in order): see `database/migrations/2026_07_25_*`.

## Business rules implemented

- **Simple vs. Variable**: chosen via a radio toggle on the create form. Simple
  products fill in SKU/price/stock directly on the product. Variable products get a
  repeatable list of variation rows (one per size), each with its own SKU, pricing,
  and stock.
- **Stock status**: auto-derived from quantity (`qty > 0` → In Stock) by default, on
  both the product (simple) and each variation (variable). A "manually set stock
  status" checkbox lets the admin override it — useful for e.g. marking something
  out-of-stock during a pre-order.
- ~~**Variation SKU auto-suggest**: picking a size auto-fills the SKU field as
  `{Product Name}-{SIZE}`...~~ **Superseded 2026-07-26**: SKU is no longer a form
  field at all (product or variation) — it's fully server-generated. See
  "2026-07-26 additions" below.
- **Colors**: multi-select checkboxes tagging a product with any number of colors
  from the `colors` master list (not a per-variation attribute).
- **Info popups**: per-variation, picked from the reusable `infos` library (not typed
  fresh per product) — e.g. a "Size Guide" or "Shipping note" popup with an icon,
  title, and rich content, shown to the customer when they view that variation.
  Icons are chosen from a small fixed set defined in `config/info_icons.php`
  (ruler, truck, shield-check, info, clock, sparkles, tag, gift) rendered via
  `resources/views/partials/icon.blade.php`.
- **Supplier city**: dropdown of Pakistani cities grouped by province, defined in
  `config/pakistan_cities.php` (not free text).

## New admin screens (all under Stock in the sidebar, except Products itself)

Each has list + create + edit + delete:
- Suppliers (`/suppliers`) — `SupplierController`
- Colors (`/colors`) — `ColorController`
- Sizes (`/sizes`) — `SizeController`
- Info Library (`/info`) — `InfoController`

Product screens (`/products`) — `ProductController`. Note: at the time this was
written there was **no edit/update yet** — only create, list, and delete. **This
changed 2026-07-26**: edit/update now exists, sharing the same
`resources/views/product/create.blade.php` template (`$isEdit = (bool) $product`).

## UI conventions established this session

- **Breadcrumbs**: every screen now shows `Home » Section » Page` at the top instead
  of a "Back to list" button. Shared partial: `resources/views/partials/breadcrumbs.blade.php`,
  included like:
  ```
  @include('partials.breadcrumbs', ['crumbs' => [
      ['label' => 'Home', 'url' => route('dashboard')],
      ['label' => 'Suppliers', 'url' => route('suppliers.list')],
      ['label' => 'Edit Supplier'],
  ]])
  ```
  (last item has no `url` — renders as plain text, current page.)
- Removed the subtitle line under page `<h1>` headers (e.g. "Add a new product to the
  catalog.") across all screens — headers are just the `<h1>` now.
- No JS framework in this app — the variation repeater and type-toggle on the product
  create form are plain vanilla JS (see `<script>` block at the bottom of
  `resources/views/product/create.blade.php`).
- **Tailwind is a build step, not JIT-in-browser** (`vite build` → `public/build/`).
  Any new Tailwind utility class used in a Blade file requires `npm run build` (or a
  running `npm run dev`) before it will actually appear styled — editing the Blade
  file alone is not enough.

## Rich text descriptions + `product_descriptions` table

- **Short description** and **Story** on the product form are CKEditor 4 (Classic,
  full toolbar) instances instead of plain textareas, loaded via CDN
  (`cdn.ckeditor.com`) — no npm package added. If the CDN fails to load, the fields
  gracefully degrade to plain `<textarea>`s (the JS checks `window.CKEDITOR` before
  calling `.replace()`). `CKEDITOR.config.versionCheck = false;` suppresses the
  built-in "this version is not secure" nag banner (free CKEditor 4 is unmaintained
  since 2023; acceptable here since this is an internal admin tool, not public-facing).
- **Considered and reverted once**: a separate *repeatable* `product_descriptions`
  table (multiple title+content sections per product, like tabs) was built, then
  removed — Story can already hold that content via headings inside one CKEditor
  field, and it added complexity with no concrete need yet.
- **Final shape**: `short_description` and `story` were moved off the `products`
  table entirely and into a `product_descriptions` table — but as a **one-to-one**
  relation (`product_id` is `unique()`), not repeatable. One product = at most one
  `product_descriptions` row, holding both `short_description` and `content` (the
  rich-text story). Parent-level only — variations don't have their own description.
  `Product::description()` (`hasOne`) / `ProductDescription::product()` (`belongsTo`).
  `ProductController@store` writes both fields into a single
  `$product->description()->create([...])` call. Migrations:
  `2026_07_25_200000_create_product_descriptions_table`,
  `2026_07_25_200001_drop_story_from_products_table`,
  `2026_07_25_200758_move_short_description_to_product_descriptions` (each backfills
  existing data before dropping the old column).
- Cleaned up every other place that referenced the old `story`/`short_description`
  columns so nothing was left silently broken: `database/seeders/ProductSeeder.php`
  (now writes to the `description` relation) and the dormant
  `app/Filament/Resources/ProductResource.php` (removed the now-nonexistent
  `story`/`short_description` fields).

### Frontend impact (`frontend/` app) — found and fixed

The admin and frontend apps **share the same MySQL database** (`heritagebasket`) —
frontend has no migrations of its own for `products`, it just reads/writes via its
own copy of the Eloquent models. This means the *very first* rename in this session
(`price`→`regular_price`, `sale_price`→`discount_price`) had already broken the live
storefront and gone unnoticed until this description work prompted a check. Fixed
in: `frontend/app/Models/Product.php` (fillable/casts renamed, `story` removed,
`description()` relation + new `frontend/app/Models/ProductDescription.php` added),
`frontend/app/Http/Controllers/ShopController.php` (price filter/sort columns),
`frontend/app/Services/CartService.php` (`addItem` unit price), and the two views
that render price/story: `resources/views/components/product-card.blade.php` and
`resources/views/products/show.blade.php` (`story` → `$product->description?->content`,
rendered with `{!! !!}` now since it's HTML; short description meta tag →
`$product->description?->short_description`).

**Lesson for future sessions in this repo**: any column rename/drop on a table
`admin` and `frontend` both touch must be grepped for in *both* codebases, not just
the one being actively edited — the shared DB means a schema change in one app is
an instant runtime risk in the other, with no migration-tracking overlap to catch it.

## Sidebar icons

Added a small heroicons-outline set (`home`, `grid`, `cart`, `adjustments`, `stack`,
`archive`, `users`, `photo`) inline in `resources/views/admin/_sidebar.blade.php`
(next to each top-level nav item, matching the reference screenshot). Not extracted
into `config/` since these are purely decorative/layout, unlike the info-popup icon
set which is admin-selectable data.

## 2026-07-26 additions

### Inventory screen (`/purchase/stock`) — no longer a stub

`InventoryController` (new) + `resources/views/purchase/stock.blade.php`. One row
per **product**, not per inventory record: simple products show a single SKU/
status/qty/Update form; variable products stack one line per variation (size,
SKU, status badge, qty input + its own Update button) inside the same row, using
`grid-cols-[24px_96px_auto]` + `divide-y` so the size/qty/Update columns line up
and stay visually separated across variations. Search by product name or SKU.
Each variation's qty updates independently via `PUT /purchase/stock/{inventory}`
(unchanged route/controller from the original inventory-table work); stock_status
is derived from quantity unless `stock_status_manual` is set, same rule as the
product form.

### SKU is now auto-generated, not typed

Removed the SKU input entirely from both the simple-product section and the
variation template on `product/create.blade.php` — no field, no placeholder, no
client-side suggestion JS. Format: `HB-<number>`, one shared sequence across
`products.sku` and `product_variations.sku` (`ProductController::nextSku()`,
`app/Http/Controllers/ProductController.php`). The very first SKU ever generated
picks a random 6-digit starting point (`random_int(100000, 999999)`); every SKU
after that is `MAX(existing HB-* number) + 1`, computed fresh each time by
scanning both tables (no separate counter table). Uniqueness relies on this
monotonic increment, not a DB-level retry/lock — acceptable for this admin
tool's single-operator scale.

- On **edit**, a simple product keeps its existing SKU (`$product->sku ?:
  $this->nextSku()`) — never regenerated.
- On **edit of a variable product**, variations are still deleted-and-recreated
  wholesale each save (pre-existing pattern), but SKUs now survive that by
  matching the old variation to the new one **by `size_id`** instead of by SKU
  string (the old by-SKU matching broke once SKU became non-user-input). Only a
  genuinely new size gets a fresh `nextSku()` call.
- All 24 pre-existing SKUs (18 simple products + 6 variations across 2 variable
  products, seeded before this change) were backfilled once via `php artisan
  tinker` into the same `HB-<sequential>` format — not a tracked migration, a
  one-off manual run.

### New pricing/merchandising fields

Migration `2026_07_26_100000_add_product_code_and_discount_percentage` +
`2026_07_26_110000_add_is_coming_soon_to_products_table` added, to both
`products` and `product_variations` unless noted:

- **`product_code`** (`products` only, nullable) — a second, *manually typed*
  code distinct from the auto-generated `sku`. Product-level even for variable
  products; all variations share the parent's code (no per-variation input).
  Editable on the create/edit form (Basic information section).
- **`discount_percentage`** (nullable decimal 5,2) — replaces the old "Discount
  price" input. The form only asks for a percentage; `discount_price` is
  computed server-side (`regular_price * (1 - discount_percentage / 100)`,
  rounded 2dp) in `applyProductAttributes()` / the variation loop in
  `syncRelations()`. A small inline JS preview (`Discount price: Rs. X.XX`)
  updates live under the percentage field as you type, purely cosmetic — the
  server always recalculates on save regardless of what the preview showed.
- **`sale_price`** (nullable decimal 12,2) — **independent** of the discount
  mechanism above; a separate manually-entered flat price with its own meaning
  on the storefront (see badge priority below). Not derived from anything.
- **`is_coming_soon`** (`products` only, boolean, default `false`) — new
  checkbox next to Featured/New arrival.
- Product list (`product/list.blade.php`) gained "Discount %" and "Sale price"
  columns (per-variation where applicable), alongside the existing Price column
  (which still shows `discount_price` struck through `regular_price`).

### Categories: real CRUD + status + `cat_code`

Previously `/categories` and `/categories/create` were `Route::view()` calls to
7-line placeholder Blade stubs with no controller, no form fields, no
validation — categories could not actually be managed anywhere in this admin
panel before now.

- New `CategoryController` (index/create/store/edit/update/destroy),
  `resources/views/category/{list,create,edit}.blade.php` (replacing the old
  `resources/views/product/{categories_list,create_category}.blade.php` stubs,
  deleted). Routes moved from `Route::view()` to real controller routes in
  `routes/admin_pages.php`.
- Sidebar: removed the separate "Create Category" submenu link
  (`resources/views/admin/_sidebar.blade.php`) — category creation now happens
  via a "+ Create category" button on the list page, same pattern as Products/
  Colors, not a second sidebar entry.
- **`cat_code`** (migration `2026_07_26_120000`, required, unique) — a
  manually-assigned stable identifier, separate from the DB `id`. Rationale: MySQL
  auto-increment never reuses a deleted row's `id`, so if a category were ever
  hard-deleted and recreated, any code/report keyed on the old `id` would break;
  `cat_code` is meant to be the stable reference across that scenario. In
  practice this is now moot for *deletion* (see next point) but `cat_code` is
  still the intended stable label for exports/reporting.
- **`status`** (migration `2026_07_26_140000`: `publish` / `draft` / `deleted`,
  default `publish`) — categories are **soft-deleted** now, exactly like
  products (`CategoryController::destroy()` just flips status, never calls
  `->delete()`). List page has the same status-tabs UI as the product list
  (`All / Published / Draft / Deleted` with counts). This was chosen deliberately
  over hard-delete + a "block delete if products are attached" guard (which was
  the first implementation, since replaced) — because with soft-delete the row
  and its `id`/`cat_code` never disappear, so existing `product_category`
  pivot rows are never at risk regardless of status.
- Product-facing category dropdowns (admin product create/edit, product list
  filter) and the storefront's category filter now exclude `deleted` (storefront
  additionally requires `publish`, excluding `draft` too).

### Products: single category → many-to-many

Product used to have one `category_id` FK. Now: `product_category` pivot table
(`product_id`, `category_id`, unique pair, `cascadeOnDelete` on both sides) —
migration `2026_07_26_130000_create_product_category_table`, which also
backfilled every existing product's single `category_id` into the pivot as its
first row before **dropping** the `category_id` column from `products` in the
same migration.

- `Product::category()` (`BelongsTo`) → `Product::categories()`
  (`BelongsToMany`, pivot `product_category`) in **both** the admin app
  (`app/Models/Product.php`) and the frontend app
  (`frontend/app/Models/Product.php`) — same change needed in both since they
  share the DB but keep separate Eloquent model copies (see the "Lesson for
  future sessions" note above; applied here proactively this time).
- `Category::products()` flipped `HasMany` → `BelongsToMany` to match, in both
  apps.
- Admin product create/edit form: the single Category `<select>` replaced with
  a checkbox multi-select (`categories[]`), same visual pattern as the existing
  Colors checkboxes. `ProductController::syncRelations()` now does
  `$product->categories()->sync($data['categories'] ?? [])` alongside the
  pre-existing `colors()->sync(...)` call.
- Admin product list Category column now shows a comma-joined list
  (`$product->categories->pluck('name')->join(', ')`); the category filter
  dropdown still filters (via `whereHas('categories', ...)` instead of a direct
  `where('category_id', ...)`).
- **Frontend storefront updated in the same pass** (not deferred, unlike the
  first session's price-rename miss): `ShopController` (category filter now
  `whereHas`, shop already supported selecting *multiple* filter categories via
  `categories[]` query params — that part didn't need to change, only how a
  product's *own* categories are queried), `HomeController` (new-arrivals/
  featured queries, category tiles), `ProductController::show()` (related
  products now match on *any* shared category via `whereHas`, PDP breadcrumb
  and "More from {category}" section use `$product->categories->first()`),
  `components/product-card.blade.php` (category name under product title now
  `$product->categories->first()?->name`).

### Frontend badge priority

New `frontend/resources/views/components/product-badge.blade.php`, a small
Blade component encapsulating a fixed priority chain (first match wins, one
badge shown at a time):

1. `is_coming_soon` → "Coming Soon" (secondary color)
2. `stock_status === 'out_of_stock'` → "Out of Stock" (muted)
3. `sale_price` set → "Sale" (red / `bg-error`)
4. `discount_price` + `discount_percentage` both set → "`{percentage}`% OFF"
   (primary color)
5. `is_new_arrival` → "New"

Wired into `product-card.blade.php` (top-left of the image — the existing
free-text `badge` column/pill stays top-right, untouched, a separate concept)
and `products/show.blade.php` (PDP, next to the existing badge pill). Frontend
`Product` model's `$fillable`/`casts()` were missing `discount_percentage`,
`sale_price`, `stock_status`, `is_coming_soon` entirely before this — added, or
`is_coming_soon` in particular would have read back as raw `0`/`1` instead of a
real boolean and silently never matched the `is_coming_soon` badge check.

**Scope note**: this badge logic only reads the *parent product's* fields. The
frontend still has no `ProductVariation` model or per-variation stock/discount
handling at all (unchanged from before this session) — for a `variable`
product, `stock_status` is hardcoded to `in_stock` by the admin regardless of
actual per-size stock (`ProductController::applyProductAttributes()`), so the
"Out of Stock" badge is not reliable for variable products today. Flagged, not
fixed, this session.

## Known gaps / not yet built

- Frontend/storefront has no `ProductVariation` model, no per-variation
  stock/discount/badge awareness, and no `Inventory` model — variable products
  are only ever read via their parent `Product` row on the storefront. A
  variable product's `stock_status` is always `in_stock` at the parent level
  regardless of real per-size stock (see "Frontend badge priority" above).
- `app/Filament/Resources/ProductResource.php` (admin app) is a dormant,
  unwired-in resource still referencing the pre-rework schema shape (flat
  `category_id`, manual `sku` input, no `discount_percentage`/`sale_price`/
  `product_code`/`is_coming_soon`). Not touched this session since nothing
  routes to it; flagged in case it's ever wired up by mistake.
- No bulk "reassign these products to a different category" tool — if a
  category needs replacing, products must be re-edited one at a time (checkbox
  multi-select on the product form).
- `nextSku()` has no explicit row-lock/retry around the SKU uniqueness
  guarantee — relies on it being effectively single-operator traffic. Fine at
  current scale; would need hardening (e.g. a dedicated counter row with
  `lockForUpdate()`, or a unique-violation retry loop) if concurrent product
  creation ever becomes realistic.

## 2026-07-26 additions (continued) — Media Library, Product image picker, Sliders

Same session, later pass. Three connected pieces of work: (1) simplified the
Info Library, (2) built a real Media Library with on-upload image resizing,
(3) used it to replace raw file-upload fields (Product images, and five new
homepage "slider" content screens) with a "choose from library" picker, wired
all the way through to the live storefront.

### Info Library: icon removed, content removed, list+create merged

The Info Library (`infos` table) originally had `icon` (picked from a fixed
`config/info_icons.php` set, rendered via `partials/icon.blade.php`) and
`content` (a textarea, "shown in the popup when the icon is clicked").

- **`icon` dropped entirely**: column dropped (`2026_07_26_000000_drop_icon_from_infos_table`),
  `config/info_icons.php` and `resources/views/partials/icon.blade.php` deleted,
  removed from create/edit forms, list column, and controller validation.
  Confirmed unused anywhere else (frontend never read `Info::icon` at all — the
  feature was admin-only and not yet wired to the storefront).
- **`content` dropped entirely too**, same treatment
  (`2026_07_26_000200_drop_content_from_infos_table`) — once real example data
  was seen, it turned out `title` already holds the full customer-facing popup
  text (e.g. *"Let op! Wees voorzichtig met scherpe voorwerpen..."*), making
  `content` a dead duplicate field. `Info` model is now just `title`.
- **List + Create merged into one page**: `resources/views/info/list.blade.php`
  redesigned as a two-column layout (`lg:grid-cols-[420px_1fr]`) — a small
  inline "Add Info" form on the left, the list (title + delete) on the right —
  replacing the old separate list/create pages pattern. The standalone
  `info/create.blade.php` still exists (route still resolves) but is no longer
  linked to from the list page.
- **Moved sidebar location**: Info Library, Colors, and Sizes all moved out of
  the **Stock** submenu into **Settings** (Stock now only has Inventory +
  Suppliers). Breadcrumbs on all three resources' list/create/edit views
  updated from `Home › Stock › ...` to `Home › Settings › ...` to match.

### Media Library (new): `media` table + `MediaController`

New feature from scratch, modelled loosely on a legacy reference project the
user pointed at (`FHR-AI/fashionhomerun-v2`) but **not** copying its design —
that project used raw `public_path()` writes (no `Storage` disk), no upload
validation, pre-formatted display strings instead of raw bytes/dimensions, and
thumbnail lookup by filename convention instead of DB tracking. Built clean
instead:

- **`media` table** (`2026_07_26_010000_create_media_table`): `disk`, `path`
  (original file), `variants` (JSON — see below), `file_name`,
  `original_name`, `mime_type`, `extension`, `size` (bytes, integer), `width`,
  `height`, `title`, `alt_text`, `uploaded_by` (FK → `admin_users`, **not**
  `users` — this app's real auth table, caught after the first migration
  attempt failed on the FK).
- **Resize-on-upload**: `intervention/image` v3 installed (GD driver, no
  Imagick on this server). Every upload generates **thumbnail (400px),
  medium (800px), large (1600px)** — `scaleDown()`, so it only shrinks, never
  upscales a small source image. This was flagged as a possible bug (all four
  files looked byte-identical for two test uploads) but confirmed correct: the
  test images were 253×512 and 200×200 — smaller than every target width, so
  "shrink to fit" was correctly a no-op. Verified working end-to-end with a
  real 2000×1500 test image (produced genuinely distinct 400×300/800×600/
  1600×1200 files).
- `Media` model: `url()`/`thumbnailUrl()`/`mediumUrl()`/`largeUrl()` helpers,
  `formatted_size`/`dimensions` accessors (computed for display, not stored as
  strings), `allPaths()` (original + every variant) used by `destroy()` to
  delete every physical file, not just the original.
- `MediaController`: `index` (search title/alt_text/original_name, paginate
  40), `store` (validates `image|mimes:jpg,jpeg,png,webp,gif|max:8192`;
  returns JSON when `Accept: application/json` — used by the in-picker upload
  flow, see below — redirect otherwise), `update` (title/alt_text only),
  `destroy` (deletes all physical files + the row).
- `resources/views/media/index.blade.php`: thumbnail grid, "+ New file" panel,
  live client-side search (debounced auto-submit on input, no button — the
  user explicitly asked for this, not the initial "type + press Search"
  version), and a details modal (click a thumbnail → File Name/Type/Uploaded
  On/Size/Dimensions/"permanently delete"/URL/Uploaded by/Title/Alt Text +
  Save changes) populated from `data-*` attributes already in the page (no
  AJAX needed for the modal itself, only for delete/save form posts).

### Product image fields → Media Library picker

All raw `<input type="file">` image fields on the Product create/edit form
(shared `product/create.blade.php`, `$isEdit` flag) were replaced with a
**"Choose from Library"** button. Direct upload was removed from the product
form entirely (per explicit instruction) — if the exact image isn't in the
library yet, the picker itself has an "Upload Files" tab so nothing is lost.

- **Feature image, Floating image, Artisan photo** (single-select each): new
  FK columns on `products` — `cover_image_id`, `floating_image_id`
  (`2026_07_26_020100_add_media_ids_to_products_table`), `artisan_image_id`
  (`2026_07_26_030000_add_artisan_image_id_to_products_table`), all nullable
  → `media`, `nullOnDelete()`. Each still also sets the pre-existing
  `*_image_url` string column (from `Media::url()`) — the raw string column
  remains the one thing every other part of the app/storefront actually reads;
  the new `*_image_id` FK is bookkeeping for "which library asset is this,"
  not a replacement. `Product::coverImage()`/`floatingImage()`/
  `artisanImage()` (`BelongsTo` → `Media`) added.
- **Gallery images** (multi-select): new `product_media` pivot table
  (`2026_07_26_020000_create_product_media_table` — `product_id`, `media_id`,
  `sort_order`) — **separate from** the pre-existing `product_images` table
  (which still holds any earlier gallery uploads; both are additive, not
  migrated into each other). `Product::galleryMedia()` (`BelongsToMany`,
  `withPivot('sort_order')`). `ProductController::syncRelations()` does a
  full wholesale `->sync()` each save (same "no diffing" convention as
  variations elsewhere in this file), so the edit form pre-renders the
  product's existing `galleryMedia` as removable chips with hidden
  `gallery_media_ids[]` inputs — without that pre-render, saving without
  touching the gallery picker would silently wipe it via `sync([])`.
- **Reusable picker**: `resources/views/partials/media-picker.blade.php`, one
  instance per page (`@include`d once, opened via `openMediaPicker('single'|
  'multiple', onConfirmCallback)`). Full-screen popup (not a small modal) with
  Library/Upload Files tabs, a checkbox per thumbnail, and a persistent
  right-side detail sidebar (Dimensions/Size/"Permanently delete"/URL/
  Uploaded by/File Name/Title/Alt Text) that updates live as tiles are
  checked — matching a reference screenshot the user provided. Single mode
  auto-unchecks any other tile (radio behaviour); the "Upload" button (bottom
  of the sidebar, disabled until ≥1 checked) applies the selection and closes
  the popup. The in-modal "Upload Files" tab posts to `media.store` via
  `fetch` with `Accept: application/json` (no page navigation — critical,
  since the product form could have unsaved edits) and injects the result as
  a new, pre-checked tile.
- Product form layout: the three (four, including Artisan) "Choose from
  Library" buttons sit in `flex flex-col` columns with `mt-auto` so they
  align at the same vertical position regardless of how many preview
  thumbnails/chips are stacked above them in each column.

### Sliders: shared `sliders` table, 5 homepage content types

New **Sliders** sidebar submenu (was previously 3 dead `Route::view()`
placeholders with no controller/model at all). One shared table rather than
one table per type, since the user asked for a `type` column partway through
once it became clear there'd be several:

- **`sliders` table** (`2026_07_26_040000_create_sliders_table` +
  `..._040100_add_category_and_product_to_sliders_table` +
  `..._040200_add_quote_to_sliders_table`): `type`, `image_id` (→ `media`),
  `category_id` (→ `categories`), `product_id` (→ `products`), `heading_1`,
  `heading_2`, `description`, `quote`, `button_text`, `button_url`,
  `sort_order`. Not every type uses every column — see below.
- **`Slider` model**: `TYPE_HOME_BANNER`, `TYPE_HOME_CATEGORY`,
  `TYPE_HOME_NEW_ARRIVAL`, `TYPE_ARTISAN_STORY`, `TYPE_FAVOURITE_PRODUCT`
  constants; `scopeOfType()`; `image()`/`category()`/`product()` relations.

| Type | Shape | Fields used | Admin screen |
|---|---|---|---|
| `home_banner` | single row | image, heading_1, heading_2, description, button_text, button_url | `HomeSliderController` (edit/update) |
| `artisan_story` | single row | image, heading_1, heading_2, description, quote | `ArtisanStoryController` (edit/update) |
| `home_category` | list | category_id (dropdown), image (own picker per row) | `CategorySliderController` (index/store/destroy) |
| `home_new_arrival` | list | product_id (dropdown) | `NewArrivalController` (index/store/destroy) |
| `favourite_product` | list | product_id (dropdown) | `FavouriteProductController` (index/store/destroy) |

`NewArrivalController`/`FavouriteProductController` extend an abstract
`ProductSliderController` (index/store/destroy identical between the two —
add a product from a dropdown of not-yet-added products, remove with a
confirm). `destroy()` on every list-type controller does
`abort_unless($slider->type === self::TYPE, 404)` so a guessed/wrong-type
slider id can't be deleted cross-type.

No drag-reorder — list order is insertion order via `sort_order = max+1` on
add; removing and re-adding is the only way to reorder today. No enforced cap
of exactly 4 items on `home_category`/`home_new_arrival`/`favourite_product`
(the screenshots showed 4, but nothing stops adding more or fewer).

The pre-existing `sliders.category`/`sliders.banner` placeholder routes: the
`Category Slider` link now points at the real `CategorySliderController`; the
"Banner Images" nav link was removed at the user's request (route/view still
exist, just unlinked — not deleted, since nothing was said about the feature
itself, only the nav entry).

### Frontend: homepage now reads from `sliders`, with automatic fallback

`frontend/app/Http/Controllers/HomeController.php` — every section now
queries the curated `Slider` data first, and **falls back to the previous
automatic logic** (category product-count, `Product::is_new_arrival`,
`Product::is_featured` random pick) whenever that type has zero rows yet —
so the homepage never goes blank mid-rollout while the admin is still filling
sliders in one at a time.

- New `frontend/app/Models/Slider.php` (same shape as the admin one) and
  `frontend/app/Models/Media.php`.
- **Cross-app URL issue found and fixed**: the `media` table only stores a
  relative `path`; the admin's `Media::url()` resolves it via
  `Storage::disk('public')->url()`, which is fine in the admin app (whose
  `APP_URL` is `theheritagebasket.admin.local`) but would resolve to the
  **wrong domain** if the same code ran inside the frontend app (`APP_URL`
  `theheritagebasket.local` — a domain that doesn't serve `/storage/media/...`
  at all, since the physical files only exist in the admin app's
  `storage/app/public`). Fixed by having the frontend's `Media` model build
  URLs against the admin app's own domain instead:
  `config('services.admin.url')` (new `frontend/config/services.php` entry,
  `ADMIN_APP_URL` env var, defaults to `http://theheritagebasket.admin.local`).
  This mirrors how `Product::cover_image_url` etc. already work (stored as a
  full absolute URL pointing at the admin domain at write time) — Media just
  needed the equivalent built at read time instead, since only the relative
  path is stored.
- `resources/views/home.blade.php`: hero, category tiles, artisan story
  section all use `$slider?->field ?? 'original hardcoded text/image'` —
  every hardcoded string/image from the original static markup is preserved
  as the fallback default, not deleted.
- Verified end-to-end: rendered the real `/` route after the fact and
  confirmed two products the user had already added live via the New Arrival
  admin screen (while this was being built) were genuinely appearing on the
  homepage, sourced from `sliders`, not the old `is_new_arrival`-flag query.

## Known gaps / not yet built (this pass)

- `home_category` rows aren't filtered by "does this category still have
  published products" the way the old auto-derived fallback was — a curated
  slide just needs `category.status = 'publish'`, not product count > 0.
- No UI anywhere to reorder existing slider list rows (new-arrival/favourite/
  category) short of remove-and-re-add.
- `sliders.banner` ("Banner Images") has no real screen — still the original
  placeholder view, just no longer linked from the sidebar.
- Category Slider's "Add" form requires picking an image before the category
  can be added (`image_media_id` is a required field) — there's no "add
  category now, image later" path.


# Orders, Settings, Cart & Checkout — Change Log

Session date: 2026-07-27. Scope: wired up Orders (list/delayed/completed), a real
Settings screen (contact, payment, cart policy, shipping cost), variable-product
sizes end-to-end on the storefront, and a full stock/quantity-aware cart with a
proper modal UI. Touches both `admin/` and `frontend/` — they share one MySQL
database (`heritagebasket`) but keep separate Eloquent model copies, so most
schema-driven features below needed a matching model/relation added in *both*
apps (see the "Lesson for future sessions" note in
`docs/product-management-changes.md` — same lesson applied here throughout).

## Admin: Orders sidebar — List / Delayed / Completed

The old sidebar had a single "Orders" link straight to a placeholder view. Now a
`<details>` submenu (matching the Products/Settings pattern) with three real
screens, backed by a new `OrderController`:

- **Order List** (`orders.list`) — searchable (order #, customer, email),
  filterable by status, paginated.
- **Delayed Orders** (`orders.delayed`) — pending/processing/shipped orders past
  their computed expected delivery date. Computation lives on `Order` model:
  `expectedDeliveryDate()` = order `created_at` + **max** `expected_delivery_days`
  across the order's line items' products + a fixed **2-day buffer**
  (`Order::DELAY_BUFFER_DAYS`). `isDelayed()` combines that with an active-status
  check (`Order::ACTIVE_STATUSES`). Delivered/cancelled orders never count as
  delayed. Filtering is done in two passes (fetch active orders with
  `items.product`, filter in PHP, re-query by ID for pagination) since "max lead
  time across items" isn't expressible as a plain SQL `WHERE` — acceptable at this
  admin tool's scale.
- **Completed Orders** (`orders.completed`) — `status = delivered`, filterable by
  a from/to date range on `created_at` (not a delivery date — no `delivered_at`
  column was added).

New migration columns on `order_items`: `product_variation_id` (nullable FK,
`nullOnDelete`) and `variation_label` (nullable string snapshot, e.g. "L") — see
the Sizes section below for why. Order edit screen
(`resources/views/orders/edit.blade.php`) shows `variation_label` next to the
product name in the items table, and the Subtotal/Shipping/Total block is now
labelled "Shipping cost" (was "Shipping") to match the storefront.

**Known gap**: `app/Filament/Resources/OrderResource.php` (Filament, dormant,
unwired — see `product-management-changes.md`) was **not** updated to match any
of this. Nothing routes to it; flagged in case it's ever wired up by mistake.

## Admin: Settings > Options — real screen, 4 independent sections

Was a stub ("Add subscription toggles, payment keys, etc."). Now backed by a new
`settings` table (one row, singleton via `Setting::current()`) and
`SettingController`, with **four separate forms** on one page (WhatsApp, Email,
JazzCash, Bank Transfer, plus a later 5th — Cart), each with its own hidden
`section` input and its own Save button, so saving one doesn't touch the others.
`SettingController::update()` switches validation rules on the `section` param.

- **WhatsApp number** + **Contact email** — shown on the storefront (WhatsApp
  icon on product pages & checkout, using `wa.me/<number>`).
- **JazzCash** (account number + title) and **Bank Transfer** (bank name +
  account number + title) — shown at checkout when that payment method is
  selected.
- Phone-like fields use a digits-only regex (`^[0-9]{10,15}$`); bank account
  number accepts either a plain numeric account number (5-20 digits) or a full
  IBAN pattern.
- All fields required, all forms use `novalidate` + `@error()` inline messages
  instead of the browser's native validation popups (was explicitly requested —
  don't reintroduce native `required` popups here).

Frontend reads this via a matching `frontend/app/Models/Setting.php`, shared to
`products.show`, `checkout.create`, `checkout.confirmation` (and now
`cart.index`, see below) through a `View::composer` in
`frontend/app/Providers/AppServiceProvider.php`.

### Payment methods (storefront checkout)

`payment_method` went from `cod,bank_transfer` → **`cod,jazzcash,bank_transfer`**
(JazzCash replaced the old generic bank-transfer-only setup). Checkout page shows
account details for the selected method (Alpine `x-show`, driven by the Settings
values above), with a graceful "we'll share details after you place the order"
fallback if the admin hasn't filled them in yet.

### Settings > Cart — `max_quantity_per_product`

New column on `settings` (`unsignedInteger`, default 5). This is the **admin
policy cap** — see the Cart section far below for how it interacts with real
stock.

## Admin: Settings > Shipping — real per-product shipping cost

Was a stub ("Manage shipping rules and codes here."). Now a real screen backed
by a new `product_shipping_cost` table (`product_id` unique FK, `shipping_cost`
decimal, both cascade-deleted with the product) — one row per product, editable
inline in a paginated/searchable product list, each row its own form + Update
button (same convention as the Inventory screen).

**Default fallback**: any product with no shipping cost row yet defaults to
**Rs. 250** — both in the actual checkout math (`Cart::shippingCost()` on the
frontend) and in what the admin sees pre-filled on this screen, so the admin
isn't misled into thinking an unset product ships free.

Checkout's shipping fee is no longer a flat constant
(`CheckoutController::SHIPPING_FEE` was removed). It's now
`Cart::shippingCost()`: **sum of each distinct cart line item's product
shipping cost, once per line item regardless of quantity** (ordering 3 of the
same item doesn't triple its shipping cost) — this was an explicit product
decision, not the only reasonable interpretation, if it ever needs revisiting.

Subtotal / Shipping cost / Total labels are now consistent across checkout,
order confirmation, the cart page, and the admin order edit screen.

## Frontend: Product detail page — gallery, colors, badges

- **Gallery = feature image + Media Library gallery images**, deduped
  (`$product->galleryMedia`, the `product_media` pivot — **not** the legacy
  `product_images` table, which is a separate/older upload path). This was the
  root cause of "no slider showing" for products that only used the Media
  Library picker to add gallery images.
- Main image is a drag/swipe carousel (Pointer Events, no prev/next buttons per
  explicit request) instead of static; thumbnail strip below is a horizontal
  scroll row with its own left/right arrows once there are more than 4 images,
  and auto-scrolls the active thumbnail into view.
- `<x-product-badge>` (Sale/Discount/Coming Soon/Out of Stock/New) now overlays
  the image top-left, and the custom free-text `badge` field overlays top-right
  — matching `product-card.blade.php`'s treatment exactly, instead of sitting as
  plain text near the title.
- **Colors**: new `product_color_media` table (product_id + color_id + media_id
  + sort_order) lets each color have its own set of images. Small square
  swatches under price; clicking one swaps the whole gallery/slider to that
  color's photos. Admin side: product edit form gets a per-color image picker
  (hidden until that color's checkbox is checked), reusing the existing Media
  Library picker in multiple-select mode.
- **Sizes** (see next section) render *above* colors, per explicit ordering
  request.
- **Weight**, **short description** (was only in the SEO meta tag before, now
  visible on the page), and **info popups** (from the existing Info Library,
  product-level `product_infos` for simple products, `product_info` for
  variations) all show under the price.
- **"The Story"** section clamps to 5 lines by default with a Read more/Read
  less toggle (Tailwind's built-in `line-clamp`, core since v3.3 — no plugin
  needed).

## Frontend: Variable products (sizes) — full pipeline, not just UI

This was the big one: variable products previously couldn't actually be
purchased on the storefront at all (per the known-gaps note in
`product-management-changes.md` — frontend had no `ProductVariation` model).
Built the whole pipeline, not just a size-picker:

- New frontend models: `Size`, `ProductVariation`, `Info` (mirroring the admin
  ones), plus `Product::variations()`/`infos()` relations.
- **Size selector** pills on the product page. Default displayed price is the
  **lowest**-priced variation (changed from an initial "highest" choice after
  discussion) until a size is picked, then reacts to the selected one.
  Price display everywhere (simple products too) follows the same
  sale-price-over-discount-price priority as the existing badge logic:
  `Product::currentPrice()`/`effectivePrice()`, `ProductVariation::currentPrice()`
  /`effectivePrice()`.
- Out-of-stock sizes are **selectable, not disabled** (shown struck-through) —
  selecting one shows "Out of stock." and disables Add to Basket, rather than
  making the button unclickable outright.
- **Cart schema**: `cart_items.product_variation_id` (nullable FK). Two cart
  rows for the same product with different sizes are separate line items.
- **Order schema**: `order_items.product_variation_id` +
  `variation_label` (snapshot, since a variation could be edited/deleted later
  — same "snapshot the name at order time" convention as `product_name`).
- Checkout summary, confirmation page, cart page, and the admin order edit
  screen all show the size next to the product name where relevant.

## Frontend: Search

Header search icon toggles a dropdown search bar (Alpine `searchOpen`,
`@click.outside`), submitting to the existing shop route with a `q` param.
`ShopController::index()` now filters by product name
(`where('name', 'like', "%{$q}%")`) — this didn't exist before at all. Shop page
shows "Showing results for '...' — Clear" and the filter sidebar carries `q`
through as a hidden field so switching category/price filters doesn't drop the
active search term.

## Frontend: Cart — stock-aware quantities + a real modal

This is the most involved piece and the one still worth re-reading carefully
before continuing tomorrow.

### Data model

- New frontend `Inventory` model (mirrors admin's), `Product::inventory()` /
  `ProductVariation::inventory()` relations, `availableQuantity()` helpers.
- **`stockLimit()`** on `Product`/`ProductVariation` (nullable int): `0` if
  `stock_status = out_of_stock`; **`null`** ("no quantity-based cap") if the
  admin has `stock_status_manual` checked — i.e. the admin explicitly vouches
  for availability regardless of the tracked count; otherwise the real tracked
  quantity. `CartItem::stockLimit()` delegates to the variation's if the line
  item has one, else the product's — **never** falls back product-level when a
  variation exists and its own limit happens to be `null` (that was a real bug
  caught and fixed mid-session: `?? ` short-circuits on `null`, not `0`, so a
  naive `$variation?->stockLimit() ?? $product->stockLimit()` would wrongly
  consult the parent product when the variation was manually-unlimited).

### The two caps, and why the wording differs

`CartService::maxAllowedQuantity(Product, ?ProductVariation)` = `min(admin's
max_quantity_per_product, stockLimit() ?? adminMax)`. Two *different* things can
bind:

1. **Real stock is genuinely low** (e.g. 2 left) — worth an alarming "Only 2
   left" message.
2. **The admin's per-order policy cap is lower than real stock** (e.g. 25 in
   stock, admin cap is 5) — this is *not* a scarcity warning, just an order
   limit, and showing "Only 5 left" here was flagged by the user as actively
   misleading (there are 25!).

Fixed by exposing `realStockLimit` separately from the effective `maxAllowed`,
computed as: `stockLimit() < adminMax ? stockLimit() : null`. The persistent
"Only X left" cart label only renders when `realStockLimit !== null && < 4`
(explicit threshold). Messaging distinguishes "Only N **in stock**" vs "Maximum
N **allowed per order**" everywhere a limit is surfaced (the on-load stale-item
popup, the increment-blocked notice, the checkout-block modal).

### Enforcement — server is authoritative, client is optimistic

`CartService::addItem()`/`updateItemQuantity()` both clamp to
`maxAllowedQuantity()` server-side and report back whether clamping happened
(`{clamped, maxAllowed}`) so the controller can surface a notice.
`CartController::store()` also hard-rejects (422) adding an item with
`maxAllowed < 1` (fully out of stock) before ever calling `addItem()`.

**+/- felt slow** because every click awaited a full round-trip before updating
anything. Fixed with optimistic UI: `cartRow.bump(delta)` updates the visible
quantity/line-total *instantly* and dispatches a lightweight
`cart:quantity-preview` event so the page-level subtotal bumps instantly too,
then **debounces** the actual PATCH call by 400ms so rapid clicking collapses
into one network request instead of one per click. The debounced call still
reconciles with whatever the server actually clamped it to.

**Minus never deletes** — `decrement()` no-ops at quantity 1 instead of removing
the item (explicit request; removal is only via the trash icon).

**Stale over-limit rows** (a cart item whose stored quantity already exceeds
the current limit — e.g. it was added before a stock/setting change) are
**not** auto-corrected anymore — an earlier version auto-clamped them
silently, which was explicitly reversed: the popup just informs the customer,
they adjust the quantity themselves via +/-.

### The modal

Replaced two `alert()` calls (over-limit-on-load, checkout-blocked) with a
proper centered modal, `Alpine.store('errorModal')` (open/image/title/message/
`items[]`), rendered once in `cart/index.blade.php`, closable via the × / click
outside / Escape. Two modes:

- **Single item** — image + name + one message (not currently used after the
  auto-clamp-on-load behavior was removed, but the store still supports it).
- **List** — used for (a) checkout blocked by one or more stock issues, and
  (b) multiple stale over-limit items found on page load. Each row: thumbnail +
  name + its own specific message.

**Bug caught and fixed**: initially each `cartRow`'s own `init()` independently
called `errorModal.show()` when *that* row was over its limit — with two
problem rows on the page, the second one's `init()` just overwrote the first's
modal content before the customer ever saw it (Alpine store is a single shared
object). Fixed by moving the trigger to **one place**:
`cartSummary.init()`, using the already-computed `stockIssues` array (which
`CartController::cartPayload()` builds server-side from *all* items, covering
both out-of-stock and over-quantity cases) — so it's a single combined list
covering every problem item, not a race between rows.

Checkout is blocked in **both** layers: client (`cartSummary.proceedToCheckout()`
checks `stockIssues.length`) and server (`CheckoutController::create()`/
`store()` independently re-check `$cart->items->contains(fn ($item) => !
$item->isInStock())` and redirect back with a message) — the client check alone
isn't trustworthy.

### Delivery estimate (cart page)

Single combined line — "You will receive this order by `<date>`" — replacing
what used to be a per-item "Standard Shipping (X-Y days)" repeated on every row
(redundant, and didn't match how order delay is computed elsewhere). Computed
as `now()->addDays($items->max(fn ($item) => $item->product->expected_delivery_days
?? 3) + 2)` — same "max across items, +2 day buffer" formula as
`Order::expectedDeliveryDate()`, just anchored to "today" since the cart hasn't
become an order yet.

## Bug fix: `short_description` crash

`product_descriptions.short_description` is a `VARCHAR(255)`
(`Schema::table(...)->string(...)`), but had **no length validation** — pasting
a long paragraph (apparently content meant for "Story") threw a raw
`Illuminate\Database\QueryException` ("String data, right truncated"). Fixed:
`max:255` added to `ProductController::validationRules()`, plus a `maxlength`
attribute and a live "`X characters remaining`" counter under the textarea in
`resources/views/product/create.blade.php`.

## Misc

- `theheritagebasket.admin.local/login` 404'd — only `/` was ever routed to the
  login screen. Added `Route::redirect('/login', '/')` inside the `guest`
  middleware group.
- Reminder for next session: **`npm run build` is required** after any new
  Tailwind utility class shows up in a Blade file, in *both* `admin/` and
  `frontend/` — this bit twice this session (WhatsApp icon size, checkout
  layout, cart modal). Blade template changes alone (`view:clear` +
  `view:cache`) are not enough if the class combination is new.

## Known gaps / continue tomorrow

- `docs/product-management-changes.md`'s own known-gaps list is still current
  (dormant Filament `ProductResource`, no slider reorder UI, etc.) — nothing in
  today's session touched those.
- The size-guide / "MAATTABEL"-style popup referenced early in this session as
  inspiration was **not** built — only the size pills + existing Info Library
  popups (which are a different, already-existing mechanism) were wired up.
- `cartRow`'s per-item "Only X left" / notice text was moved to sit under the
  +/- stepper (was under the product name/price) — verify this still reads
  cleanly once there's a real multi-line product name in production data.
- Nothing yet uses `Cart::shippingCost()`'s "sum per distinct line item"
  behavior in a scenario with genuinely many distinct products in one order —
  worth a manual check once there's realistic test data.
- The admin Options page's "Cart" section (`max_quantity_per_product`) and
  "Shipping" page are both brand new this session — worth the admin actually
  setting real values (WhatsApp number, JazzCash/bank details, per-product
  shipping costs) instead of the defaults, since none of that content existed
  before.

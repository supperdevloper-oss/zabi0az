# ZabiSoft — WordPress software catalogue

A complete, installable WordPress site for publishing software entries: a real
custom theme written in PHP/HTML5/CSS3/vanilla JS plus an independent plugin
that manages the catalogue. No page builder, no framework, no build step.

Everything is manageable from the WordPress admin: posts, pages, categories,
menus, images, widgets, theme options and the software data itself.

```
src/themes/zabisoft/                  the theme (install / activate)
src/plugins/zabisoft-software-manager/ the plugin (install / activate)
dist/                                 the two ZIP files you upload to WordPress
docs/                                 installation and setup guides (Arabic + English)
tools/                                build helpers: readme/POT generator, images, packaging
```

## Quick start

1. Upload `dist/zabisoft-1.0.0.zip` in **Appearance → Themes → Add New → Upload Theme**,
   activate it.
2. Upload `dist/zabisoft-software-manager-1.0.0.zip` in
   **Plugins → Add New → Upload Plugin**, activate it.
3. **Settings → Permalinks → Post name → Save Changes**.
4. Appearance → Menus: assign menus to *Primary*, *Top bar* and *Footer*.
5. Optional: **Software → Settings → Import demo content** to see the whole system
   (six software entries, the standard pages, menus, blog posts), then remove it
   again when your own content is ready.

Requirements: WordPress 6.4+ (tested on 7.1), PHP 7.4+, MySQL/MariaDB, GD or
Imagick for thumbnails. No other plugin is required.

The full guides are in [`docs/دليل-التركيب-والإعداد.md`](docs/دليل-التركيب-والإعداد.md) (Arabic)
and [`docs/INSTALL-EN.md`](docs/INSTALL-EN.md) (English).

## What the site does

* **Front page** — hero with search, category strip, the latest entries, sidebar.
* **Listing pages** — the same card everywhere: thumbnail, title, date/author/
  category, short description, “View details & download” button, numbered
  pagination (`1 2 3 … Next »`).
* **Software page** — table of contents, overview, screenshot gallery with
  lightbox, “technical setup details” table, system requirements, key features,
  numbered installation steps, download box with mirrors, related entries.
* **Download transparency** — every button prints the destination host, uses
  `rel="nofollow noopener"`, can be gated by a countdown, and each URL is
  verified by a link checker (hover-free status, one hour cache, bulk re-check,
  optional WP-Cron job). Files you have no right to distribute are never
  bundled — the demo data links to the publishers' own pages.
* **Pages** — About, Contact (working form with honeypot, rate limiting and
  nonce), Privacy policy, Disclaimer, DMCA, Sitemap, 404, search, archives.
* **Behind the scenes** — a `software` post type with `software_category`,
  `software_platform` and `software_tag`, nineteen structured fields, admin
  columns with link status, shortcodes (`[zsm_software_grid]`,
  `[zsm_downloads]`, `[zsm_specs]`, `[zsm_requirements]`, `[zsm_link_status]`)
  and one-click demo content.

## Bundled graphics

Every image in the package was produced for this project: the theme screenshot
and the demo covers/screenshots are generated with ImageMagick
(`tools/make-images.sh`), the interface icons are inline SVG written for the
theme. No third-party logos, photographs or icon fonts are included, so no extra
licence is needed. See `src/themes/zabisoft/assets/images/README.txt`.

## How it was tested

* PHP syntax: 59 files checked with two independent parsers, zero problems.
* Runtime: the theme and the plugin were activated on a clean WordPress 7.1
  (SQLite) installation, the demo content was imported and every screen was
  fetched over HTTP: front page, software archive, category archives, single
  software entries, blog posts, search, pagination, feeds, `robots.txt`,
  `wp-sitemap.xml`, all six static pages, the 404 page and the contact form
  submission. No PHP notices, warnings or errors.
* Packaging: the final ZIP files were unpacked into a fresh installation and the
  site was served from those copies (both theme and plugin) to confirm the
  archives contain everything needed.

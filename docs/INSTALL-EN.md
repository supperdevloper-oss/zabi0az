# ZabiSoft — installation and setup guide (English)

A ready WordPress software catalogue: a real PHP theme (no page builder, no
framework) plus an independent plugin that manages the software entries and
their download links. Everything is controlled from the WordPress admin.

The Arabic guide is in `docs/دليل-التركيب-والإعداد.md`.

---

## 1. What is in the package

| File | What it is | Where it goes |
| --- | --- | --- |
| `zabisoft-1.0.0.zip` | The theme (design, templates, styling) | Appearance → Themes → Add New → Upload Theme |
| `zabisoft-software-manager-1.0.0.zip` | The plugin (software entries, categories, fields, download mirrors, link checker) | Plugins → Add New → Upload Plugin |

Install **both**. The theme renders the pages; the plugin stores and organises
the information. With only the theme you still get a working site, but no
software catalogue in the admin.

---

## 2. Requirements

* WordPress **6.4+** (tested on 7.1).
* **PHP 7.4+** (8.1+ recommended).
* MySQL / MariaDB — any normal WordPress host.
* PHP **GD or Imagick** for image thumbnails (enabled on virtually every host).
* An **Administrator** account. No external library, no build step, no
  additional plugin is required.

---

## 3. Installation — method A (WordPress admin)

1. Log in to `/wp-admin`.
2. Appearance → Themes → Add New → **Upload Theme** → choose
   `zabisoft-1.0.0.zip` → Install → **Activate**.
3. Plugins → Add New → **Upload Plugin** → choose
   `zabisoft-software-manager-1.0.0.zip` → Install → **Activate**.
4. **Settings → Permalinks** → choose “Post name”
   (`https://yoursite.com/%postname%/`) → **Save Changes**.
   This is required for the software and category URLs.
5. Appearance → Customize to set the logo, colours and front page texts.

## 4. Installation — method B (cPanel / FTP)

1. Open cPanel → **File Manager** (or connect over FTP).
2. Go to `wp-content/themes`, upload `zabisoft-1.0.0.zip`, then right-click it
   and choose **Extract**, so that `wp-content/themes/zabisoft/style.css`
   exists (the ZIP already contains the `zabisoft` folder — do not nest it).
3. Go to `wp-content/plugins` and do the same with
   `zabisoft-software-manager-1.0.0.zip` →
   `wp-content/plugins/zabisoft-software-manager/`.
4. Delete the two ZIP files afterwards (optional but tidy).
5. Back in the admin: activate the theme **ZabiSoft** and the plugin
   **ZabiSoft Software Manager**.
6. Settings → Permalinks → “Post name” → Save Changes.

---

## 5. First configuration

### 5.1 Menus
Appearance → Menus: create “Main menu” (Home plus the important categories plus
Contact us) and display it in the **Primary** location. Create “Footer menu”
(About, Contact us, Privacy policy, DMCA, Disclaimer, Sitemap) and display it in
the **Footer** location. A third location, **Top bar**, is available for short
links.

### 5.2 Page templates
While editing any page, pick a template in the “Page Attributes” box:

| Page | Template |
| --- | --- |
| About | About page |
| Contact | Contact page (working form with honeypot and rate limiting) |
| Sitemap | Sitemap page |
| Anything without a sidebar | Full width (no sidebar) |

### 5.3 Plugin settings — **Software → Settings**

* **Archive URL slug** — default `softwares`, so entries live at
  `/softwares/entry-name/`.
* **Category URL base** — leave **empty** for clean category URLs such as
  `/office-tools/`. If a page already uses that address, type a word such as
  `category` to get `/category/office-tools/`.
* **Entries per archive page**, **default countdown** (seconds),
  **check links when saving**, **re-check every 12 hours (WP-Cron)** and the
  **disclosure text** printed under the download box.

After changing a slug, save the settings and then save the permalinks once.

---

## 6. Adding a software entry

1. **Software → Add New**.
2. Title + the full description in the editor (this becomes the “Overview”
   section of the page).
3. **Featured image** = the cover (800×500 works well).
4. Choose a **category** and a **platform** in the side boxes.
5. Fill in **Program details**: version, file size, setup file name, setup type,
   compatibility, licence, last update, developer, official website, languages.
   Empty fields are simply not printed.
6. Fill in **System requirements**, **Key features** (one per line) and
   **Installation instructions** (one per line).
7. **Screenshots**: click “Add screenshots” and pick images from the media
   library — they appear in a gallery with a lightbox.
8. **Download mirrors**: one row per link (label, URL, size, type, platform,
   note). The “Check link” button verifies a URL immediately. Multiple mirrors
   are supported.
9. **Countdown**: leave empty to use the default, set a per-entry value, or `0`
   to disable it.
10. Publish. The table of contents, specification table, requirements, gallery,
    download box and related entries are generated automatically.

### Legal note
Do not upload installer files and do not link to files you are not allowed to
distribute — link to the publisher's own page instead. The theme prints the
destination host next to every button and adds `rel="nofollow noopener"`.
Commercial logos and box shots are protected: only use images you own or that
are licensed to you.

---

## 7. Demo content

**Software → Settings → Demo content → Import demo content** creates ten complete
software entries covering every category used in the menu (images included, links
pointing at the publishers' own websites), eight categories, the six standard
pages, three blog posts and the two menus — the main menu carries the same nine
items in the same order as the reference site, and both menus are assigned to the
theme locations for you. “Remove demo content” deletes exactly what it created.
Nothing is imported automatically.

---

## 8. Appearance of the listings

**Appearance → Customize → Front page** collects the options that decide how the
front page and the archives look:

* **Box every entry in the listings** — draws a 2 px frame around each entry, like
  the reference site. On by default; turn it off for a plain list with dotted
  separators.
* **Show the version, size and system chips inside listings** — off by default so
  the listings match the reference. The full specification table stays on every
  software page either way.
* **Show the hero banner** / **Show the category strip** — optional extras for the
  top of the front page. Off by default, because the reference starts straight
  with the list.
* **Number of entries on the front page** — ten by default, which produces the
  `1 2 3 … Next »` pagination under the list.
* **Show the section heading above the list** — off by default.

The black **top bar** is off by default as well. To use it, create a second menu
(About, Contact, Privacy…) and assign it to the *Top bar* location under
Appearance → Menus, then tick *Show the black top bar* in the Customizer.

---

## 9. Troubleshooting

**Category or software pages return 404.** Settings → Permalinks → Save
Changes. If it persists, deactivate and reactivate the plugin, then save the
permalinks again.

**A category URL collides with an existing page.** Set a *Category URL base*
(e.g. `category`) in Software → Settings, save, then save the permalinks.

**Link checks always fail.** Some sites block automated requests (403) or your
host blocks outgoing requests. The stored result refreshes hourly and the
automatic check can be switched off.

**The contact form does not deliver mail.** It uses `wp_mail()`; install an
SMTP plugin such as WP Mail SMTP or configure mail delivery on your server.

**The theme does not appear after a cPanel upload.** Make sure
`wp-content/themes/zabisoft/style.css` exists and reload the Themes screen.

**SEO plugins.** The theme does not print duplicate meta tags and works with
Yoast, Rank Math and SEOPress. `/wp-sitemap.xml` (WordPress core) is always
available.

**Security.** No secrets are stored in the theme files, all input is sanitised
(`sanitize_*`), all output is escaped (`esc_*`), forms are nonce protected and
capability checks are in place. Deactivating the plugin keeps your data;
deleting it asks first (Software → Settings → “Delete all data on uninstall”).

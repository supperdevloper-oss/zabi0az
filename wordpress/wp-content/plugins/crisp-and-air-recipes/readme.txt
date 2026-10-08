=== Crisp & Air Recipes ===
Contributors: crisp-and-air
Requires at least: 6.4
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPL-2.0-or-later

Native recipe-card fields, safe batch importing, ingredient/category search, author profile photos, and careful Recipe JSON-LD for the Crisp & Air WordPress theme.

== Features ==

* Uses WordPress Posts, Categories, Tags, media, authors, scheduling, and revisions; the Posts menu is relabeled Recipes.
* Adds a recipe details panel, practical publishing checklist, featured/popular controls, and editable SEO title/description fields.
* Prints recipe cards and emits Recipe JSON-LD only for published posts with a featured image and complete core recipe details.
* Supports UTF-8 CSV imports in batches of 25, up to 5,000 rows per file. Imports create drafts; duplicate titles are skipped.
* Can associate a unique image filename already in the Media Library. It never downloads images from CSV URLs.
* Searches recipe titles, text, ingredient metadata, category names, and tags. Tag archive pages are noindex and omitted from the core sitemap.
* Provides an accessible contact-form shortcode that emails the configured WordPress site address; contact submissions are not stored as recipe posts.

== Important ==

This plugin does not generate recipe facts, cooking instructions, nutrition, photos, author credentials, or ratings from titles. Review every draft and complete the details before publishing. `aggregateRating` is never generated.

== Setup ==

1. Activate the plugin and the Crisp & Air theme.
2. Save Settings → Permalinks using Post name.
3. Use Recipes → Add Recipe for a single recipe or Tools → Recipe Import for a CSV batch.
4. Keep recipes as drafts until you have reviewed their article, recipe card, image, category, and SEO fields.

# Crisp & Air — Air Fryer Recipes for WordPress

A polished, mobile-first recipe publication starter built for ordinary, self-hosted WordPress—not a proprietary CMS. The package includes a live-ready design preview, a lightweight WordPress theme, a small companion plugin, and a safe batch importer for the recipe titles and reviewed data you will provide.

**No 4,000 recipes have been invented or published.** On activation, the exact untouched English WordPress “Hello world!” post and “Sample Page” are retained as drafts so starter copy cannot appear as a recipe or public page. Edited or translated content is left alone. Title-only CSV rows also become drafts; temperature, nutrition, recipe images, ratings, author credentials, and article copy are never fabricated by the importer.

## First: architecture

Read [ARCHITECTURE.md](ARCHITECTURE.md) before installing. In brief: recipes are native WordPress Posts (shown in the dashboard as **Recipes**) to keep the editor familiar and root-level permalinks short. A companion plugin adds recipe-card fields, SEO/social fields, category-aware ingredient search, eligible Recipe JSON-LD, author photos, and a reviewed-draft CSV workflow. A dependency-free theme supplies the homepage, recipe, category, search, author, and legal-page layouts.

## Preview

Open `preview/index.html` to view the responsive editorial homepage. The standalone preview has working search/filter controls, mobile navigation, recipe-page and printable-card layouts. Its recipe records are obvious design placeholders: no real titles, instructions, times, ingredients, nutrition, authorship, ratings, or Recipe JSON-LD are supplied. Generated food images are layout artwork, not actual recipe photographs. Do not cook from or publish preview placeholders. A fresh WordPress install starts with no published recipe posts.

## Install in WordPress

Requirements: a self-hosted WordPress installation (6.4 or newer recommended), PHP 8.1+, and a supported MySQL/MariaDB host.

1. Upload and activate `packages/crisp-and-air-theme.zip` under **Appearance → Themes → Add New → Upload Theme**.
2. Upload and activate `packages/crisp-and-air-recipes.zip` under **Plugins → Add New → Upload Plugin**.
3. Open **Settings → Permalinks**, select **Post name**, and save once.
4. Visit **Appearance → Customize** to edit the hero copy, image, and newsletter callout. Update the site title/tagline and Site Icon under **Appearance → Customize → Site Identity**.
5. Add a real author name, short bio, and optional portrait under **Users → Profile**. Configure your monitored contact email under **Settings → General**.
6. Build your menu under **Appearance → Menus** (Recipes, Categories, Popular Recipes, About, and Contact are available as starter pages). Add and curate recipe categories under **Recipes → Categories**.
7. Review the editable About, Contact, Privacy, Terms, Cookie, and Disclaimer pages. The theme marks its legal starters for site-owner review. A new WordPress install may already contain its own **draft Privacy Policy**; the theme preserves that page rather than overwriting it. Update and publish the privacy page only after adapting it to your real hosting, contact inbox, ad/analytics tools, newsletter provider, and applicable requirements.
8. Add an email-provider form shortcode in **Appearance → Widgets** or the Homepage Customizer setting before collecting subscribers. The theme itself does not collect email addresses.

The theme creates the homepage, recipe/category directories, About and Contact pages, and editable legal-page starters only when a page with that slug does not already exist. It will not overwrite existing pages or article content. On a fresh WordPress install, the core-generated Privacy Policy can remain a draft; the fallback footer omits its link until that page is published. Starter legal pages are not a substitute for reviewing the site’s real practices and requirements.

## Add a recipe by hand

1. Choose **Recipes → Add Recipe**.
2. Add the title and write the article in the normal block editor. Start with a specific introduction that answers the title’s actual cooking question; add useful context on ingredients/airflow, practical steps, and only the tips, variations, substitutions, storage, reheating, and FAQs that truly help. The recipe-card fields render the exact ingredient and instruction lists in the article and the printable card, so keep one verified source of truth. Avoid generic lead-ins and repeated filler; the editor checklist shows what to verify.
3. Add an excerpt, assign an existing category and a few relevant tags, and upload/select a recipe photo under **Featured image**. Write descriptive alt text in the media details.
4. Fill the **Recipe details** panel. Enter one ingredient per line and one instruction step per paragraph. Add temperature/time, servings, and calories only when you can stand behind them.
5. Add an SEO title and meta description, preview the post, verify the recipe card, and then publish or schedule.

The published recipe card prints cleanly and provides Recipe JSON-LD only when the essential data and featured image are present. Ratings are not emitted unless a legitimate rating system is added and real ratings exist.

## Import titles/data in batches

1. Download the included starter CSV: `wordpress/wp-content/plugins/crisp-and-air-recipes/assets/recipe-import-template.csv`.
2. Keep the header row and include a `title` column. Add only reviewed data to optional columns; use UTF-8 CSV and avoid spreadsheet formula fields.
3. In wp-admin, use **Tools → Recipe Import**. Upload the file, then click **Import next batch** until complete. The importer handles 25 rows per request, skips existing matching titles, and creates drafts only.
4. Finish editorial review in **Recipes → All Recipes**. A title-only draft is not publication-ready; add unique article copy, a real image, recipe details, category, SEO metadata, and cross-links before scheduling.

Optional columns are `category`, `tags`, `excerpt`, `article_content`, `ingredients`, `instructions`, `prep_minutes`, `cook_minutes`, `air_fryer_temp`, `servings`, `course`, `cuisine`, `difficulty`, `calories`, `notes`, `tips`, `variations`, `substitutions`, `storage`, `reheating`, `faqs`, `seo_title`, `meta_description`, `featured_image_file`, `featured`, and `popular`. For list fields, separate items by line breaks. FAQ lines use `Question | Answer`. The importer does not fetch external images; upload rights-cleared photos to the Media Library first. `featured_image_file` can then match an exact, unique media-library filename (for example `air-fryer-wings.webp`) to a draft. If a filename is absent or duplicated, the draft is left without a featured image and the importer reports it for review.

## Everyday management

- **Edit recipes/categories:** Recipes → All Recipes / Categories; standard WordPress editor, revisions, tags, author, and scheduling.
- **Upload images:** Media → Add New; select responsive image sizes and enter meaningful alt text. Use a modern image/CDN optimizer only if you need additional compression.
- **Menus:** Appearance → Menus; assign the Primary and Footer locations.
- **Homepage:** Appearance → Customize → Crisp & Air Homepage. Featured and popular cards come from checkboxes in the Recipe details panel; latest recipes update automatically.
- **Pages:** Pages → Add New. Use the standard block editor; starter pages are editable.
- **Ads:** Add a Custom HTML block in the named Appearance → Widgets areas (recipe before/after card, archive in-feed, sidebar, or footer). Load the AdSense script once using Google's approved integration (for example, Site Kit) rather than repeating it in every slot. Keep ad placements clearly separated from recipe controls.
- **Backups:** Configure your host or a reputable WordPress backup service to back up both the database and `wp-content/uploads`; schedule and test a restore. Keep a copy off-site.

## Customization and package layout

- Theme source: `wordpress/wp-content/themes/crisp-and-air/`
- Companion plugin: `wordpress/wp-content/plugins/crisp-and-air-recipes/`
- Installable archives: `packages/`
- Responsive design preview and placeholder recipe-template walkthrough: `preview/`
- Original generated sample food-photo assets and 512px site-icon starter: `wordpress/wp-content/themes/crisp-and-air/assets/images/` (add recipe photos to the Media Library and set descriptive alt text before publishing)
- Technical decisions and scaling notes: [ARCHITECTURE.md](ARCHITECTURE.md)

The implementation uses native WordPress features and no runtime build step. If you already use Yoast SEO, Rank Math, AIOSEO, SEOPress, or The SEO Framework, the companion plugin detects them and defers its own SEO/social tags; use one SEO metadata provider. Do not activate a second recipe-card plugin that emits duplicate Recipe schema.

# Crisp & Air — recommended WordPress architecture

## Decisions

| Requirement | Implementation | Why it fits a beginner and 4,000+ recipes |
|---|---|---|
| CMS | Self-hosted WordPress; no proprietary service or build step | All publishing, media, menus, pages, scheduling, and users stay in wp-admin. |
| Recipe records | Native WordPress **Posts**, relabeled “Recipes”; native hierarchical Categories and Tags | The familiar editor and author/scheduling workflows remain intact. Root-level post-name permalinks preserve short recipe URLs such as `/air-fryer-chicken-wings/`. |
| Recipe data | A small companion plugin adds one clearly labeled “Recipe details” panel, SEO fields, author-photo support, and a safe CSV importer | Recipe fields travel with the content and do not disappear when the design changes. No custom database schema or page builder is needed. |
| Article editing | Gutenberg content for the story and cooking guidance; dedicated fields for the printable card and recipe facts | An imported title alone stays a draft. Editors can add complete, original, reviewed copy and recipe data before scheduling. |
| First activation | Create only missing site/trust pages and the category hierarchy; do not seed recipe posts | Preserve the default WordPress Hello world post and Sample Page as drafts only when their untouched English core content matches exactly. Never rewrite an owner-edited or translated starter. |
| Theme | Lightweight, dependency-free hybrid/classic theme with native menus, widgets, Customizer controls, and block-editor support | The homepage and editorial templates are polished but remain editable through standard WordPress screens. No proprietary CMS or front-end framework is required. |
| URLs and archives | Settings → Permalinks → **Post name**; recipe categories use a short, stable WordPress category path; archive/search pages paginate | No per-recipe rewrite rules or 4,000-item in-memory index. WordPress handles canonical post URLs and paginated archives. |
| Search | WordPress search plus a focused extension for recipe ingredients and matching category names | Searches title, story, excerpt, ingredients, and category terms without a third-party search service. |
| Structured data | Recipe JSON-LD is emitted only when the published recipe has the necessary real recipe-card data and a featured image | Avoids fake ratings, invented nutrition, and incomplete Recipe markup. `aggregateRating` is deliberately omitted. |
| SEO | Native editable SEO title/description, canonical and social metadata, breadcrumbs, WordPress XML sitemap, noindex search/tag archives; compatible with a single SEO plugin | Good defaults without forcing an SEO subscription. If Yoast, Rank Math, AIOSEO, SEOPress, or The SEO Framework is activated, the companion plugin defers metadata output to avoid duplicates. |
| Advertising | Clearly separated theme widget areas in the sidebar and between long-form sections | Add AdSense through a Custom HTML block/widget or an approved integration such as Site Kit; no ad code is baked into article content. |
| Newsletter | Dedicated homepage placement with a shortcode/provider integration point | Connect a mailing-list provider before collecting email addresses; no personal data is collected by the theme itself. |
| Scale and performance | Server-rendered PHP templates, native paginated WP_Query, responsive `srcset` images, lazy loading, minimal vanilla JS | The theme does not request all recipes at once, rely on a remote API, or add a JavaScript framework. |

## Content model

- **Posts / Recipes:** one post per recipe. Keep the title’s meaning and main phrase; use a concise, human-readable post slug. Set a real author and a featured image with accurate alt text.
- **Categories:** a controlled hierarchy under “Air Fryer Recipes,” with useful food groups such as Chicken, Beef, Pork, Seafood, Vegetables, Potatoes, Breakfast, Lunch, Dinner, Appetizers, Snacks, Desserts, Frozen Foods, Healthy, Quick & Easy, and Vegetarian. Create a new category only when it will contain a meaningful collection and help visitors browse.
- **Tags:** a small number of genuinely useful cross-category descriptors. Avoid making tags that duplicate category archives.
- **Recipe card fields:** prep/cook minutes, servings, temperature (°F), course, cuisine, difficulty, calories (optional), ingredients, numbered instructions, notes, and optional practical tips/variations/storage/reheating/FAQ content.
- **Article body:** original introduction and useful sections in Gutenberg. Do not publish a title-only record or an unreviewed generated draft.
- **Author:** standard WordPress user account, profile description, and optional media-library photo. No credentials or review scores are invented.

## 4,000-title workflow

1. Save titles and any researched, reviewed recipe data as UTF-8 CSV using the included template. A title-only row is valid but will become an **unpublished draft**.
2. In WordPress, open **Tools → Recipe Import**, upload the CSV, check the detected row count, then import in batches of 25. Existing matching titles are skipped rather than overwritten. No recipe is auto-published and no remote image is downloaded.
3. Open **Recipes → All Recipes** and filter drafts. Add category/tags, article copy, complete recipe-card fields, a suitable image/alt text, and SEO title/description. Verify temperatures, doneness, and nutrition; leave nutrition blank if it is not reasonably known.
4. Preview on mobile and desktop, confirm the recipe card and image, review links and schema readiness, then publish or schedule a manageable batch.
5. Use the standard WordPress editor, bulk actions, categories, media library, revisions, and backups as the library grows. Archives use pagination; there is no 4,000-card front-end dump.

The importer can also accept reviewed columns for article body, excerpt, ingredients, instructions, times, category/tags, optional nutrition, practical sections, SEO metadata, and a unique `featured_image_file` already uploaded to the Media Library. It creates drafts either way. It does **not** fetch remote images or fabricate recipe measurements, articles, photos, or ratings from a title.

## Launch checklist

- Install a supported WordPress release (6.4+ recommended), activate the Crisp & Air theme and companion plugin, and choose **Settings → Permalinks → Post name**. Save permalinks once to refresh WordPress rewrite rules.
- Replace the sample brand name, site icon, homepage copy, menu, author profile, contact details, and social links with your own information.
- Review and complete the editable legal-page starters for your actual site, analytics, ads, mailing-list provider, and jurisdiction before publishing them.
- Add a newsletter provider shortcode only after its privacy/consent settings are configured. Configure AdSense through its approved integration and place ads sparingly in the supplied widget areas.
- Use an SEO plugin only if you want one; use **one** metadata/schema source for any added plugin. Do not enable a second recipe-card/schema plugin that duplicates the native recipe card.
- Set up scheduled database + uploads backups with your host or a trusted backup plugin; test a restore before publishing at scale.

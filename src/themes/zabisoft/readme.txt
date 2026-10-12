=== ZabiSoft ===

Contributors: zabisoft
Requires at least: WordPress 6.4
Tested up to: WordPress 7.1
Requires PHP: 7.4
Version: 1.0.0
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: two-columns, right-sidebar, custom-menu, custom-logo, featured-images, translation-ready, rtl-language-support, blog, e-commerce, footer-widgets

A fast, clean software catalogue theme: front page with hero and category strip,
list style archives, a full software detail template with specifications, system
requirements, screenshots, a table of contents and a countdown gated download
box with checked mirrors.

== Description ==

ZabiSoft was built for sites that publish software entries. It is a classic
PHP/HTML5/CSS3 theme, no build step and no JavaScript framework:

* Front page with hero search, category strip and the latest entries.
* The same list card on the front page, on category pages, on the software
  archive, on search results and on author pages.
* Software pages with a table of contents, screenshot gallery with lightbox,
  “technical setup details” table, system requirements, feature list,
  installation steps and the download box.
* Download buttons that print the destination host, use rel="nofollow noopener"
  and can be hidden behind a countdown before they appear.
* Three menus (main, top bar, footer), six widget areas, five colour schemes,
  sidebar variants, breadcrumbs, pagination, comment styles and a full RTL
  stylesheet.
* Customizer options for the front page, the colours, the typography, the
  sidebar and the footer.

== The content model ==

Software entries, their categories, platforms and download mirrors are provided
by the companion plugin **ZabiSoft Software Manager**. Without it the theme
still works: entries can be written as normal posts, and the `_zs_*` custom
fields can be filled in with the “Custom fields” panel.

== Installation ==

1. In WordPress go to Appearance → Themes → Add New → Upload Theme and send
   `zabisoft.zip`, then activate it.
2. Install the plugin `zabisoft-software-manager.zip` through
   Plugins → Add New → Upload Plugin.
3. Open Appearance → Menus and assign a menu to “Primary”, “Top bar” and
   “Footer”.
4. Open Appearance → Customize to set the logo, the colours and the front page
   texts.
5. Optional: Software → Settings → “Import demo content” to see everything
   working, then delete it again.

The complete, illustrated guide (in Arabic and in English) is in the
`docs/` folder of the delivery package.

== Bundled graphics ==

Everything in `assets/images` is original work created for this theme (simple
SVG shapes and the ImageMagick generated `screenshot.png`). No third-party
icons, photographs or logos are bundled, so no extra licence is required. See
`assets/images/README.txt`.

== Changelog ==

= 1.0.0 =
* First release.

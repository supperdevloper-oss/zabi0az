=== ZabiSoft Software Manager ===
Contributors: zabisoft
Tags: software, download, custom post type, mirrors, link checker
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds the complete software catalogue to WordPress: entries, categories,
platforms, download mirrors, link verification and demo content.

== Description ==

This plugin is the engine behind the ZabiSoft theme. It registers everything the
theme renders, so the content stays safe if you ever switch to another theme.

**What it adds**

* Custom post type **Software** with its own archive, URL slug and editor.
* Three taxonomies: **Software Categories** (hierarchical, like normal
  categories), **Platforms** (Windows, macOS, Linux, Android, iOS…) and
  **Software Tags**.
* Structured meta boxes: version, file size, setup file name, setup type,
  compatibility, licence, last update, developer, official website, interface
  languages.
* A system requirements box, a features box and installation instructions.
* A **mirror repeater** for download links: label, URL, size, type, platform and
  a note. Every button on the website prints the destination host.
* A **link checker** that verifies each URL (HEAD request with a ranged GET
  fallback), stores the result for one hour, shows a status column in the
  admin list, can re-check everything with one click and can run twice a day
  through WP-Cron.
* A screenshot picker that uses the normal WordPress media library.
* Admin list columns: cover, version, size, system, mirrors, link status.
* Shortcodes: `[zsm_software_grid]`, `[zsm_downloads]`, `[zsm_specs]`,
  `[zsm_requirements]`, `[zsm_link_status]`.
* Optional **demo content**: six software entries, six pages, blog posts and two
  menus, all created from images shipped with the plugin (so nothing is copied
  from the internet). One click imports it, one click removes it again.

**Download buttons and legal notice**

The plugin never hosts installers. It prints the links you enter and shows the
host name next to every button, together with a disclosure line you can edit in
Software → Settings. Always link to the software publisher's own page unless you
have written permission to distribute the file.

== Installation ==

1. Upload the ZIP file through Plugins → Add New → Upload Plugin, or copy the
   `zabisoft-software-manager` folder into `wp-content/plugins/` with cPanel or
   FTP.
2. Activate the plugin.
3. Open **Software → Settings**, set the archive slug (default `softwares`) and
   the number of entries per page.
4. Visit **Settings → Permalinks** and click *Save changes* once, so the new
   permalinks are written.
5. Optional: click **Import demo content** in Software → Settings to see the
   complete system working, then delete it again when your own entries are
   ready.

== Frequently Asked Questions ==

= Does it work without the ZabiSoft theme? =

Yes. The content model, the meta boxes, the link checker and the shortcodes are
independent. If the ZabiSoft theme is missing, the plugin renders its own basic
templates for the software archive and the single entry.

= Is anything sent to a third party site? =

No. The only outgoing request is the link check, which asks the URL you entered
whether it is reachable. That result is cached locally for one hour.

= Can I distribute installers with it? =

Only files you are allowed to distribute. The plugin does not change the law:
it simply shows the links you provide.

== Changelog ==

= 1.0.0 =
* First release: software post type, three taxonomies, structured fields,
  download mirrors, link checker, shortcodes, settings and demo content.

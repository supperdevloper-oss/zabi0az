#!/usr/bin/env bash
#
# Builds the two ZIP files that are installed through the WordPress admin.
#
#   dist/zabisoft-<version>.zip                     → theme
#   dist/zabisoft-software-manager-<version>.zip    → plugin
#
# The archives contain one top level folder, exactly what
# "Appearance → Themes → Add New → Upload Theme" and
# "Plugins → Add New → Upload Plugin" expect.
#
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SRC="$ROOT/src"
DIST="$ROOT/dist"

THEME_SLUG="zabisoft"
PLUGIN_SLUG="zabisoft-software-manager"

THEME_VERSION="$(grep -m1 '^Version:' "$SRC/themes/$THEME_SLUG/style.css" | sed 's/.*: *//' | tr -d '\r')"
PLUGIN_VERSION="$(grep -m1 "ZSM_VERSION', '" "$SRC/plugins/$PLUGIN_SLUG/$PLUGIN_SLUG.php" | sed "s/.*'\([0-9.]*\)'.*/\1/" | tr -d '\r')"

mkdir -p "$DIST"

# Never ship development leftovers ------------------------------------------
find "$SRC" \( -name '.DS_Store' -o -name 'Thumbs.db' -o -name '*.log' -o -name '*.map' \) -delete

theme_zip="$DIST/${THEME_SLUG}-${THEME_VERSION}.zip"
plugin_zip="$DIST/${PLUGIN_SLUG}-${PLUGIN_VERSION}.zip"

rm -f "$theme_zip" "$plugin_zip"

( cd "$SRC/themes" && zip -rq "$theme_zip" "$THEME_SLUG" -x '*.DS_Store' )
( cd "$SRC/plugins" && zip -rq "$plugin_zip" "$PLUGIN_SLUG" -x '*.DS_Store' )

echo "Theme  : $theme_zip"
echo "Plugin : $plugin_zip"

for archive in "$theme_zip" "$plugin_zip"; do
	printf '  %-46s %8s  %3s files\n' "$(basename "$archive")" "$(du -h "$archive" | cut -f1)" "$(unzip -l "$archive" | tail -1 | awk '{print $2}')"
done

# A quick sanity check: the required WordPress files must be inside.
required=(
	"$THEME_SLUG/style.css"
	"$THEME_SLUG/functions.php"
	"$THEME_SLUG/index.php"
	"$THEME_SLUG/header.php"
	"$THEME_SLUG/footer.php"
	"$THEME_SLUG/single.php"
	"$THEME_SLUG/archive.php"
	"$THEME_SLUG/search.php"
	"$THEME_SLUG/404.php"
	"$THEME_SLUG/screenshot.png"
	"$THEME_SLUG/assets/css/editor.css"
	"$THEME_SLUG/assets/js/zabisoft.js"
	"$THEME_SLUG/assets/images/placeholder.svg"
	"$THEME_SLUG/rtl.css"
	"$THEME_SLUG/readme.txt"
)

missing=0

for file in "${required[@]}"; do
	if ! unzip -l "$theme_zip" "$file" >/dev/null 2>&1; then
		echo "MISSING in theme zip: $file"
		missing=1
	fi
done

plugin_required=(
	"$PLUGIN_SLUG/$PLUGIN_SLUG.php"
	"$PLUGIN_SLUG/includes/class-zsm-post-types.php"
	"$PLUGIN_SLUG/includes/class-zsm-fields.php"
	"$PLUGIN_SLUG/includes/class-zsm-meta-boxes.php"
	"$PLUGIN_SLUG/includes/class-zsm-link-checker.php"
	"$PLUGIN_SLUG/includes/class-zsm-admin-columns.php"
	"$PLUGIN_SLUG/includes/class-zsm-settings.php"
	"$PLUGIN_SLUG/includes/class-zsm-demo-content.php"
	"$PLUGIN_SLUG/includes/class-zsm-shortcodes.php"
	"$PLUGIN_SLUG/includes/helpers.php"
	"$PLUGIN_SLUG/assets/css/admin.css"
	"$PLUGIN_SLUG/assets/js/admin.js"
	"$PLUGIN_SLUG/uninstall.php"
	"$PLUGIN_SLUG/readme.txt"
)

for file in "${plugin_required[@]}"; do
	if ! unzip -l "$plugin_zip" "$file" >/dev/null 2>&1; then
		echo "MISSING in plugin zip: $file"
		missing=1
	fi
done

if [ "$missing" -eq 0 ]; then
	echo "All required files are inside both archives."
else
	echo "Some files are missing – fix the build before shipping."
	exit 1
fi

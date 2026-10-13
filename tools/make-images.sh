#!/usr/bin/env bash
#
# Generates the theme screenshot and the license free demo images.
# Requires ImageMagick 6 (convert) with the DejaVu fonts.
#
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
THEME="$ROOT/src/themes/zabisoft"
DEMO="$ROOT/src/plugins/zabisoft-software-manager/assets/demo"
FONT="DejaVu-Sans"
FONT_BOLD="DejaVu-Sans-Bold"

mkdir -p "$DEMO"

# ---------------------------------------------------------------------------
# 1. Theme screenshot (1200 x 900)
#
# The screenshot shipped in src/themes/zabisoft is a real capture of the
# running theme (see README), so this mock is only drawn when asked for:
#   bash tools/make-images.sh --with-screenshot
# ---------------------------------------------------------------------------
if [ "${1:-}" = '--with-screenshot' ]; then
convert -size 1200x900 xc:'#ececec' \
	-fill '#101820' -draw 'rectangle 0,0 1200,118' \
	-fill '#1d2c3d' -draw 'rectangle 0,0 1200,58' \
	-font "$FONT_BOLD" -pointsize 46 -fill '#ffffff' -annotate +60+72 'ZABISOFT' \
	-font "$FONT" -pointsize 17 -fill '#b9c3cd' -annotate +62+100 'Download Free Your Desired Software' \
	-fill '#ffffff' -draw 'roundrectangle 780,38 1140,80 6,6' \
	-font "$FONT" -pointsize 16 -fill '#9aa0a6' -annotate +798+65 'Search software…' \
	-fill '#1b1b1b' -draw 'rectangle 1036,45 1132,73' \
	-font "$FONT_BOLD" -pointsize 13 -fill '#ffffff' -annotate +1051+64 'SEARCH' \
	-fill '#f6f6f6' -draw 'rectangle 0,118 1200,168' \
	-fill '#cfcfcf' -draw 'rectangle 0,164 1200,168' \
	-font "$FONT_BOLD" -pointsize 16 -fill '#4e4e4e' \
	-annotate +140+148 'Operating Systems' -annotate +310+148 'Office Tools' \
	-annotate +435+148 'Antivirus' -annotate +540+148 'Utilities' -annotate +640+148 'Data Recovery' \
	-fill '#1b1b1b' -draw 'rectangle 46,120 108,166' \
	-font "$FONT_BOLD" -pointsize 16 -fill '#ffffff' -annotate +60+148 'Home' \
	-fill '#ffffff' -draw 'rectangle 0,168 1200,832' \
	-fill '#f2f2f2' -draw 'roundrectangle 40,200 230,340 6,6' \
	-fill '#c9d3dd' -draw 'roundrectangle 40,200 230,340 6,6' \
	-fill '#8fa3b5' -draw 'circle 135,270 135,225' \
	-font "$FONT_BOLD" -pointsize 24 -fill '#1c1c1c' -annotate +255+230 'LibreOffice Free Download' \
	-font "$FONT" -pointsize 14 -fill '#9a9a9a' -annotate +255+255 '25 OCTOBER 2026   ·   BY ADMIN   ·   OFFICE TOOLS' \
	-font "$FONT" -pointsize 16 -fill '#555555' \
	-annotate +255+290 'A complete offline office suite with word processor, spreadsheet and' \
	-annotate +255+314 'presentation modules. Full installer, version, size and requirements.' \
	-fill '#1b1b1b' -draw 'roundrectangle 255,328 400,362 5,5' \
	-font "$FONT_BOLD" -pointsize 13 -fill '#ffffff' -annotate +280+350 'READ MORE' \
	-fill '#c9d3dd' -draw 'roundrectangle 40,390 230,530 6,6' \
	-fill '#8fa3b5' -draw 'circle 135,460 135,415' \
	-font "$FONT_BOLD" -pointsize 24 -fill '#1c1c1c' -annotate +255+420 'VLC Media Player 3.x' \
	-font "$FONT" -pointsize 14 -fill '#9a9a9a' -annotate +255+445 '24 OCTOBER 2026   ·   BY ADMIN   ·   MULTIMEDIA' \
	-font "$FONT" -pointsize 16 -fill '#555555' \
	-annotate +255+480 'Plays almost every media format without extra codec packs. Portable' \
	-annotate +255+504 'and installer builds, screenshots and installation steps included.' \
	-fill '#1b1b1b' -draw 'roundrectangle 255,518 400,552 5,5' \
	-font "$FONT_BOLD" -pointsize 13 -fill '#ffffff' -annotate +280+540 'READ MORE' \
	-fill '#c9d3dd' -draw 'roundrectangle 40,580 230,720 6,6' \
	-fill '#8fa3b5' -draw 'circle 135,650 135,605' \
	-font "$FONT_BOLD" -pointsize 24 -fill '#1c1c1c' -annotate +255+610 'GIMP Image Editor' \
	-font "$FONT" -pointsize 14 -fill '#9a9a9a' -annotate +255+635 '22 OCTOBER 2026   ·   BY ADMIN   ·   GRAPHIC DESIGN' \
	-font "$FONT" -pointsize 16 -fill '#555555' \
	-annotate +255+670 'Professional photo retouching, layers, masks and plug-ins, free and' \
	-annotate +255+694 'open source. Every mirror is checked before it is printed.' \
	-fill '#1b1b1b' -draw 'roundrectangle 255,708 400,742 5,5' \
	-font "$FONT_BOLD" -pointsize 13 -fill '#ffffff' -annotate +280+730 'READ MORE' \
	-fill '#a9bf04' -draw 'roundrectangle 880,200 1160,244 8,8' \
	-fill '#6e8c03' -draw 'rectangle 880,238 1160,246' \
	-font "$FONT_BOLD" -pointsize 17 -fill '#ffffff' -annotate +900+228 'RECENT POSTS' \
	-fill '#fafafa' -draw 'rectangle 880,246 1160,470' \
	-font "$FONT" -pointsize 14 -fill '#333333' \
	-annotate +900+280 '• 7-Zip Free Download' -annotate +900+312 '•  Blender 3D Suite' \
	-annotate +900+344 '•  Audacity Audio Editor' -annotate +900+376 '•  VeraCrypt Encryption' \
	-annotate +900+408 '•  Krita Digital Painting' -annotate +900+440 '•  Inkscape Vector Studio' \
	-fill '#a9bf04' -draw 'roundrectangle 880,500 1160,544 8,8' \
	-fill '#6e8c03' -draw 'rectangle 880,538 1160,546' \
	-font "$FONT_BOLD" -pointsize 17 -fill '#ffffff' -annotate +900+528 'CATEGORIES' \
	-fill '#fafafa' -draw 'rectangle 880,546 1160,770' \
	-font "$FONT" -pointsize 14 -fill '#333333' \
	-annotate +900+580 '•  Operating Systems (24)' -annotate +900+612 '•  Office Tools (31)' \
	-annotate +900+644 '•  Multimedia & Video (18)' -annotate +900+676 '•  Security & Antivirus (27)' \
	-annotate +900+708 '•  Graphic & Design (22)' -annotate +900+740 '•  Utilities (35)' \
	-fill '#dddddd' -draw 'rectangle 0,832 1200,860' \
	-fill '#cdcdcd' -draw 'rectangle 0,860 1200,900' \
	-font "$FONT" -pointsize 15 -fill '#333333' -annotate +470+886 '© 2026 ZabiSoft — all rights reserved' \
	"$THEME/screenshot.png"
else
	echo 'Skipping the theme screenshot; pass --with-screenshot to redraw the mock.'
fi

# ---------------------------------------------------------------------------
# 2. Demo cover images (800 x 500)
# ---------------------------------------------------------------------------
make_cover() {
	local out="$1" title="$2" subtitle="$3" tone="$4"
	convert -size 800x500 "xc:$tone" \
		-fill '#ffffff22' -draw 'circle 720,60 720,-40' \
		-fill '#00000022' -draw 'circle 60,460 60,560' \
		-fill '#ffffff' -draw 'roundrectangle 90,120 710,400 14,14' \
		-fill '#e9eef2' -draw 'rectangle 90,120 710,170' \
		-fill '#c4ced6' -draw 'circle 118,145 118,134' -draw 'circle 142,145 142,134' -draw 'circle 166,145 166,134' \
		-fill '#dfe6ea' -draw 'roundrectangle 120,200 420,240 6,6' \
		-fill '#eef2f5' -draw 'roundrectangle 120,262 680,290 6,6' \
		-fill '#eef2f5' -draw 'roundrectangle 120,306 620,334 6,6' \
		-fill '#eef2f5' -draw 'roundrectangle 120,350 500,378 6,6' \
		-font "$FONT_BOLD" -pointsize 40 -fill '#ffffff' -gravity south -annotate +0+34 "$title" \
		-font "$FONT" -pointsize 22 -fill '#ffffffcc' -gravity south -annotate +0+12 "$subtitle" \
		"$out"
}

make_cover "$DEMO/cover-office.jpg"    'Office suite'      'documents · sheets · slides' '#1c6ea4'
make_cover "$DEMO/cover-media.jpg"     'Media player'      'video · audio · subtitles'   '#2f6f4f'
make_cover "$DEMO/cover-graphics.jpg"  'Image editor'      'layers · masks · plug-ins'   '#8a3f3f'
make_cover "$DEMO/cover-utility.jpg"   'File utility'      'archive · compress · share'  '#5a5a6e'
make_cover "$DEMO/cover-3d.jpg"        '3D creation'       'modelling · render · video'  '#8a6a1f'
make_cover "$DEMO/cover-audio.jpg"     'Audio tool'        'record · edit · export'      '#6a4ea3'
make_cover "$DEMO/cover-os.jpg"        'Operating system'  'install · update · drivers'  '#b5561f'
make_cover "$DEMO/cover-security.jpg"  'Security scanner'  'scan · protect · report'     '#2f4f6f'
make_cover "$DEMO/cover-recovery.jpg"  'Data recovery'     'partition · files · rescue'  '#4a4a5a'
make_cover "$DEMO/cover-download.jpg"  'Download manager'  'queue · resume · capture'    '#1f6f6a'

# ---------------------------------------------------------------------------
# 3. Demo screenshots (1000 x 625)
# ---------------------------------------------------------------------------
make_shot() {
	local out="$1" title="$2" tone="$3" accent="$4"
	convert -size 1000x625 "xc:$tone" \
		-fill '#ffffff' -draw 'roundrectangle 40,40 960,585 10,10' \
		-fill "$accent" -draw 'rectangle 40,40 960,96' \
		-fill '#ffffff' -draw 'circle 78,68 78,58' -draw 'circle 104,68 104,58' -draw 'circle 130,68 130,58' \
		-font "$FONT_BOLD" -pointsize 22 -fill '#ffffff' -annotate +160+76 "$title" \
		-fill '#f3f5f7' -draw 'rectangle 40,96 250,585' \
		-fill '#dfe4e8' -draw 'roundrectangle 62,130 228,160 5,5' \
		-fill '#e9edf0' -draw 'roundrectangle 62,176 228,206 5,5' \
		-fill '#e9edf0' -draw 'roundrectangle 62,222 228,252 5,5' \
		-fill '#e9edf0' -draw 'roundrectangle 62,268 228,298 5,5' \
		-fill '#fbfcfd' -draw 'rectangle 250,96 960,585' \
		-fill '#ffffff' -draw 'roundrectangle 290,140 920,320 8,8' \
		-fill '#e6ebef' -draw 'roundrectangle 320,180 700,208 5,5' \
		-fill '#eef2f5' -draw 'roundrectangle 320,228 880,252 5,5' \
		-fill '#eef2f5' -draw 'roundrectangle 320,266 830,290 5,5' \
		-fill "$accent" -draw 'roundrectangle 320,348 470,392 6,6' \
		-font "$FONT" -pointsize 16 -fill '#ffffff' -annotate +352+376 'Continue' \
		-fill '#e6ebef' -draw 'roundrectangle 500,348 620,392 6,6' \
		-fill '#fbfcfd' -draw 'rectangle 250,430 960,585' \
		-fill '#eef2f5' -draw 'roundrectangle 290,470 900,500 5,5' \
		-fill '#eef2f5' -draw 'roundrectangle 290,520 830,550 5,5' \
		"$out"
}

make_shot "$DEMO/shot-1.jpg" 'Main window'       '#16283a' '#2196f3'
make_shot "$DEMO/shot-2.jpg" 'Preferences'       '#2f3b2a' '#4c9563'
make_shot "$DEMO/shot-3.jpg" 'Export dialog'     '#3a2b3f' '#8a68cf'

echo "Generated theme screenshot and demo images."

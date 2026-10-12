#!/usr/bin/env python3
"""Sanity checks for the theme stylesheets.

Catches the two mistakes that are easy to make when a stylesheet is edited by
hand and that are hard to spot in the browser:

1. unbalanced braces — one missing `}` silently swallows the rules that follow;
2. a missing `@media` opener, which pushes "print only" rules such as
   `display: none` for the header, the sidebar or the footer to the top level,
   so they apply on screen and the site looks broken.

Usage: python3 tools/check-css.py [file ...]
       (defaults to every .css file of the theme)
"""

from __future__ import annotations

import re
import sys
from pathlib import Path

REPO = Path(__file__).resolve().parent.parent
THEME = REPO / 'src' / 'themes' / 'zabisoft'

# Selectors that must never be hidden at the top level of a stylesheet.
# `.zs-nav__toggle` and `.zs-countdown` are deliberately absent: hiding those
# until JavaScript or the countdown reveals them is the intended behaviour.
GUARDED = {
    '.zs-header',
    '.zs-header__inner',
    '.zs-masthead',
    '.zs-footer',
    '.zs-sidebar',
    '.zs-nav',
    '.zs-nav__list',
    '.zs-topbar',
    '.zs-branding',
    '.zs-content',
    '.zs-layout',
    '.zs-downloads',
    '#page',
    '#primary',
    '#secondary',
    '#colophon',
    'body',
    'header',
    'footer',
    'aside',
    'nav',
    'main',
}


def strip_comments(css: str) -> str:
    """Replace comments with the same number of newlines, keeping line numbers."""

    return re.sub(r'/\*.*?\*/', lambda m: '\n' * m.group(0).count('\n'), css, flags=re.S)


def rules(css: str):
    """Yield (line, depth, selector, body) for every rule of a stylesheet."""

    depth = 0
    line = 1
    selector_start = 0
    selector_line = 1
    body_start = 0

    for index, char in enumerate(css):
        if char == '\n':
            line += 1
        elif char == '{':
            if depth == 0:
                selector_start = index
                selector_line = line
            depth += 1

            if depth == 1:
                body_start = index + 1
        elif char == '}':
            depth -= 1

            if depth < 0:
                yield ( line, -1, '', 'extra closing brace' )
                depth = 0
            elif depth == 0:
                yield ( selector_line, 0, css[selector_start + 1:body_start - 1].strip(), css[body_start:index] )

    if depth:
        yield ( line, depth, '', f'{depth} unclosed block(s) at the end of the file' )


def check_file(path: Path) -> list[str]:
    css = strip_comments(path.read_text(encoding='utf-8'))
    problems: list[str] = []

    for line, depth, selector, body in rules(css):
        if depth < 0:
            problems.append(f'{path.name}:{line}: {body}')
            continue

        if depth > 0:
            problems.append(f'{path.name}:{line}: {body}')
            continue

        if not re.search(r'display\s*:\s*none', body):
            continue

        parts = [part.strip().lower() for part in selector.split(',')]

        for part in parts:
            if part in GUARDED:
                problems.append(
                    f'{path.name}:{line}: "{part}" is hidden at the top level of the file — '
                    'it probably belongs inside an @media block (a missing @media opener?)'
                )
                break

    return problems


def main() -> int:
    targets = [Path(arg) for arg in sys.argv[1:]] or sorted(THEME.rglob('*.css'))
    problems: list[str] = []

    for target in targets:
        if target.is_file():
            problems += check_file(target)

    for problem in problems:
        print('FAIL ' + problem)

    print(f'check-css: {len(targets)} file(s), {len(problems)} problem(s)')

    return 1 if problems else 0


if __name__ == '__main__':
    sys.exit(main())

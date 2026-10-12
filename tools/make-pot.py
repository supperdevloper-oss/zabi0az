#!/usr/bin/env python3
"""Build a Gettext .pot template from a PHP tree.

Only strings that belong to the requested text domain are exported. The parser
understands nested calls such as esc_html__( 'x', 'domain' ) and _n( 'a', 'b',
1, 'domain' ), it ignores anything inside comments and it records line numbers.

Usage:
    python3 tools/make-pot.py <source-dir> <text-domain> <output.pot> \
        [--package "Name 1.0"] [--bug-report "https://example.com"]
"""

from __future__ import annotations

import argparse
import datetime
import os
import re
import sys

FUNCTIONS = {
    "__": 1,
    "_e": 1,
    "esc_html__": 1,
    "esc_html_e": 1,
    "esc_attr__": 1,
    "esc_attr_e": 1,
    "_x": 2,
    "_ex": 2,
    "esc_html_x": 2,
    "esc_attr_x": 2,
    "_n": 2,
    "_nx": 3,
}

SKIP_DIRS = {".git", "node_modules", "vendor", "dist", "build", "languages"}


def strip_comments(source: str) -> str:
    """Blank out PHP comments, keeping strings and offsets untouched."""
    out = list(source)
    i = 0
    length = len(source)

    while i < length:
        ch = source[i]

        if ch in "'\"":
            quote = ch
            i += 1
            while i < length:
                if source[i] == "\\":
                    i += 2
                    continue
                if source[i] == quote:
                    i += 1
                    break
                i += 1
        elif ch == "/" and i + 1 < length and source[i + 1] == "/":
            while i < length and source[i] != "\n":
                out[i] = " "
                i += 1
        elif ch == "#":
            while i < length and source[i] != "\n":
                out[i] = " "
                i += 1
        elif ch == "/" and i + 1 < length and source[i + 1] == "*":
            out[i] = out[i + 1] = " "
            i += 2
            while i + 1 < length and not (source[i] == "*" and source[i + 1] == "/"):
                if source[i] != "\n":
                    out[i] = " "
                i += 1
            if i + 1 < length:
                out[i] = out[i + 1] = " "
                i += 2
        else:
            i += 1

    return "".join(out)


def decode_php_string(raw: str) -> str:
    """Turn a PHP single or double quoted literal into plain text."""
    if len(raw) < 2:
        return ""

    body = raw[1:-1]
    body = body.replace("\\'", "'").replace('\\"', '"').replace("\\\\", "\\")

    if raw[0] == '"':
        body = body.replace("\\n", "\n").replace("\\t", "\t").replace("\\r", "\r")

    return body


def split_arguments(inner: str) -> list[str]:
    """Split a call body into top-level arguments."""
    args: list[str] = []
    depth = 0
    current = ""
    i = 0
    length = len(inner)

    while i < length:
        ch = inner[i]

        if ch in "'\"":
            quote = ch
            current += ch
            i += 1
            while i < length:
                current += inner[i]
                if inner[i] == "\\":
                    if i + 1 < length:
                        current += inner[i + 1]
                        i += 2
                        continue
                if inner[i] == quote:
                    break
                i += 1
            i += 1
            continue

        if ch in "([{":
            depth += 1
        elif ch in ")]}":
            depth -= 1
        elif ch == "," and depth == 0:
            args.append(current.strip())
            current = ""
            i += 1
            continue

        current += ch
        i += 1

    if current.strip():
        args.append(current.strip())

    return args


def line_of(source: str, offset: int) -> int:
    return source.count("\n", 0, offset) + 1


def flatten_concatenation(argument: str) -> str | None:
    """Join 'a' . 'b' style arguments, return None when not constant."""
    parts = re.split(r"\s*\.\s*", argument.strip(), flags=0)
    text = ""
    first = True

    for part in parts:
        part = part.strip()

        # Strip a trailing comment marker produced by the comment remover.
        match = re.match(r"^(?:'((?:[^'\\]|\\.)*)')", part) or re.match(
            r'^(?:"((?:[^"\\]|\\.)*)")', part
        )

        if not match:
            return None

        raw = match.group(0)
        text += decode_php_string(raw) if first else decode_php_string(raw)
        first = False

    return text


def extract(path: str, domain: str) -> dict[str, dict]:
    with open(path, "r", encoding="utf-8", errors="replace") as handle:
        source = handle.read()

    clean = strip_comments(source)
    # Remove single line comments conservatively: strip_comments keeps newlines.
    found: dict[str, dict] = {}

    pattern = re.compile(
        r"\b(" + "|".join(sorted(FUNCTIONS, key=len, reverse=True)) + r")\s*\("
    )

    for match in pattern.finditer(clean):
        name = match.group(1)
        start = match.end()
        depth = 1
        i = start
        length = len(clean)

        while i < length and depth > 0:
            ch = clean[i]

            if ch in "'\"":
                quote = ch
                i += 1
                while i < length:
                    if clean[i] == "\\":
                        i += 2
                        continue
                    if clean[i] == quote:
                        break
                    i += 1
            elif ch == "(":
                depth += 1
            elif ch == ")":
                depth -= 1
                if depth == 0:
                    break

            i += 1

        inner = clean[start:i]
        raw_inner = source[start:i]
        line = line_of(source, match.start())
        args = split_arguments(inner)
        raw_args = split_arguments(raw_inner)

        expected = FUNCTIONS[name]

        if len(args) < expected + 1:
            # Some calls omit the domain; only accept them for the simple cases.
            if len(args) < expected:
                continue
            has_domain = False
        else:
            has_domain = True

        if has_domain:
            domain_arg = args[-1].strip()

            if domain_arg.strip("'\"") != domain:
                continue

        singular = flatten_concatenation(raw_args[0]) if raw_args else None

        if singular is None:
            continue

        plural = None
        context = None

        if name in ("_n", "_nx"):
            plural = flatten_concatenation(raw_args[1])

            if name == "_nx":
                context = flatten_concatenation(raw_args[2])
        elif name in ("_x", "_ex", "esc_html_x", "esc_attr_x"):
            context = flatten_concatenation(raw_args[1])

        if not singular:
            continue

        key = (context or "") + "\x04" + singular + ("\x05" + plural if plural else "")

        entry = found.setdefault(
            key,
            {
                "singular": singular,
                "plural": plural,
                "context": context,
                "references": [],
            },
        )

        if entry["plural"] is None and plural:
            entry["plural"] = plural

        if entry["context"] is None and context:
            entry["context"] = context

        entry["references"].append((path, line))

    return found


def escape_po(text: str) -> str:
    return (
        text.replace("\\", "\\\\")
        .replace('"', '\\"')
        .replace("\n", "\\n")
        .replace("\t", "\\t")
    )


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("source")
    parser.add_argument("domain")
    parser.add_argument("output")
    parser.add_argument("--package", default="")
    parser.add_argument("--bug-report", default="")
    parser.add_argument("--base", default=None, help="Directory used for #: references")
    args = parser.parse_args()

    entries: dict[str, dict] = {}
    files = 0

    for root, dirs, names in os.walk(args.source):
        dirs[:] = [d for d in dirs if d not in SKIP_DIRS]

        for name in sorted(names):
            if not name.endswith(".php"):
                continue

            path = os.path.join(root, name)
            files += 1

            for key, entry in extract(path, args.domain).items():
                bucket = entries.setdefault(key, entry)

                if bucket is not entry:
                    bucket["references"].extend(entry["references"])

    lines = [
        'msgid ""',
        'msgstr ""',
        '"Project-Id-Version: %s\\n"' % escape_po(args.package),
        '"Report-Msgid-Bugs-To: %s\\n"' % escape_po(args.bug_report),
        '"POT-Creation-Date: %s\\n"' % datetime.datetime.utcnow().strftime("%Y-%m-%d %H:%M+0000"),
        '"MIME-Version: 1.0\\n"',
        '"Content-Type: text/plain; charset=UTF-8\\n"',
        '"Content-Transfer-Encoding: 8bit\\n"',
        '"X-Generator: tools/make-pot.py\\n"',
        '"X-Domain: %s\\n"' % escape_po(args.domain),
        "",
    ]

    for key in sorted(
        entries,
        key=lambda k: (entries[k]["context"] or "").lower() + entries[k]["singular"].lower(),
    ):
        entry = entries[key]
        references = sorted(set(entry["references"]))

        for path, line in references[:6]:
            if args.base:
                path = os.path.relpath(path, args.base)
            lines.append("#: %s:%d" % (path, line))

        if entry["context"]:
            lines.append('msgctxt "%s"' % escape_po(entry["context"]))

        lines.append('msgid "%s"' % escape_po(entry["singular"]))

        if entry["plural"]:
            lines.append('msgid_plural "%s"' % escape_po(entry["plural"]))
            lines.append('msgstr[0] ""')
            lines.append('msgstr[1] ""')
        else:
            lines.append('msgstr ""')

        lines.append("")

    with open(args.output, "w", encoding="utf-8") as handle:
        handle.write("\n".join(lines))

    print(
        "%s: %d strings from %d PHP files (%s)"
        % (args.output, len(entries), files, args.domain)
    )

    return 0


if __name__ == "__main__":
    sys.exit(main())

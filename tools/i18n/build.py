"""Builds the WordPress translation files from translations.py.

For the theme and the plugin it writes, in their languages/ folder:
  *.pot           template with all English texts
  *.po            editable translations (Poedit, Loco Translate)
  *.mo            compiled translations
  *.l10n.php      faster PHP translation files (WordPress 6.5+)

It fails when a text used in the code has no translation.
Run:  python3 tools/i18n/build.py
"""
import struct
import sys
from datetime import datetime, timezone
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from extract import ROOT, extract  # noqa: E402
from translations import HEADER_STRINGS, PLUGIN, THEME  # noqa: E402

LOCALES = {
    'de_DE': {'index': 0, 'plural_forms': 'nplurals=2; plural=(n != 1);'},
    'sk_SK': {'index': 1, 'plural_forms': 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2;'},
}
TARGETS = {
    'medcentrum': {
        'table': THEME,
        'dir': ROOT / 'wp-content/themes/medcentrum/languages',
        'prefix': '',  # themes: de_DE.mo
        'project': 'MedCentrum theme',
    },
    'medcentrum-booking': {
        'table': PLUGIN,
        'dir': ROOT / 'wp-content/plugins/medcentrum-booking/languages',
        'prefix': 'medcentrum-booking-',  # plugins: medcentrum-booking-de_DE.mo
        'project': 'MedCentrum – Online Booking',
    },
}


def po_quote(text):
    return '"' + text.replace('\\', '\\\\').replace('"', '\\"').replace('\n', '\\n') + '"'


def php_quote(text):
    return "'" + text.replace('\\', '\\\\').replace("'", "\\'") + "'"


def headers(project, locale=None):
    now = datetime.now(timezone.utc).strftime('%Y-%m-%d %H:%M+0000')
    rows = [
        ('Project-Id-Version', project),
        ('POT-Creation-Date', now),
        ('MIME-Version', '1.0'),
        ('Content-Type', 'text/plain; charset=UTF-8'),
        ('Content-Transfer-Encoding', '8bit'),
    ]
    if locale:
        rows += [
            ('PO-Revision-Date', now),
            ('Language', locale),
            ('Plural-Forms', LOCALES[locale]['plural_forms']),
        ]
    return rows


def entries(table, locale):
    """[(msgid, msgid_plural or None, [msgstr forms])] sorted by msgid."""
    out = []
    for key, value in table.items():
        tr = value[LOCALES[locale]['index']] if locale else None
        if isinstance(key, tuple):
            forms = tr if tr else ['', '']
            out.append((key[0], key[1], list(forms)))
        else:
            out.append((key, None, [tr or '']))
    return sorted(out, key=lambda e: e[0])


def write_po(path, project, table, locale, references):
    lines = ['msgid ""', 'msgstr ""']
    lines += [po_quote(f'{k}: {v}\n') for k, v in headers(project, locale)]
    for msgid, plural, forms in entries(table, locale):
        lines.append('')
        key = (msgid, plural) if plural else msgid
        for ref in references.get(key, []):
            lines.append(f'#: {ref}')
        lines.append(f'msgid {po_quote(msgid)}')
        if plural:
            lines.append(f'msgid_plural {po_quote(plural)}')
            for i, form in enumerate(forms):
                lines.append(f'msgstr[{i}] {po_quote(form)}')
        else:
            lines.append(f'msgstr {po_quote(forms[0])}')
    path.write_text('\n'.join(lines) + '\n', encoding='utf-8')


def write_mo(path, project, table, locale):
    header = ''.join(f'{k}: {v}\n' for k, v in headers(project, locale))
    pairs = [(b'', header.encode())]
    for msgid, plural, forms in entries(table, locale):
        key = msgid if not plural else msgid + '\0' + plural
        pairs.append((key.encode(), '\0'.join(forms).encode()))
    pairs.sort(key=lambda p: p[0])

    count = len(pairs)
    originals_at = 28
    translations_at = originals_at + count * 8
    data_at = translations_at + count * 8
    ids = b''.join(k + b'\0' for k, _ in pairs)
    strs = b''.join(v + b'\0' for _, v in pairs)

    table_ids, table_strs, offset = [], [], data_at
    for k, _ in pairs:
        table_ids.append((len(k), offset))
        offset += len(k) + 1
    for _, v in pairs:
        table_strs.append((len(v), offset))
        offset += len(v) + 1

    out = struct.pack('<7I', 0x950412DE, 0, count, originals_at, translations_at, 0, 0)
    out += b''.join(struct.pack('<2I', *t) for t in table_ids)
    out += b''.join(struct.pack('<2I', *t) for t in table_strs)
    out += ids + strs
    path.write_bytes(out)


def write_l10n_php(path, domain, table, locale):
    lines = ['<?php', 'return [']
    lines.append(f"\t'domain' => {php_quote(domain)},")
    lines.append(f"\t'language' => {php_quote(locale)},")
    lines.append(f"\t'plural-forms' => {php_quote(LOCALES[locale]['plural_forms'])},")
    lines.append("\t'messages' => [")
    for msgid, plural, forms in entries(table, locale):
        if plural:
            key = f'{php_quote(msgid)} . "\\0" . {php_quote(plural)}'
            value = ' . "\\0" . '.join(php_quote(f) for f in forms)
        else:
            key, value = php_quote(msgid), php_quote(forms[0])
        lines.append(f'\t\t{key} => {value},')
    lines.append('\t],')
    lines.append('];')
    path.write_text('\n'.join(lines) + '\n', encoding='utf-8')


def main():
    found = extract()
    problems = 0
    for domain, target in TARGETS.items():
        table = target['table']
        used = set(found[domain]) | HEADER_STRINGS.get(domain, set())
        missing = sorted(map(str, used - set(table)))
        unused = sorted(map(str, set(table) - used))
        for m in missing:
            print(f'[{domain}] missing translation: {m!r}')
        for u in unused:
            print(f'[{domain}] unused translation: {u!r}')
        problems += len(missing)
        for key, value in table.items():
            for locale, cfg in LOCALES.items():
                tr = value[cfg['index']]
                if isinstance(key, tuple):
                    expected = 2 if locale == 'de_DE' else 3
                    if len(tr) != expected:
                        print(f'[{domain}] {locale}: {key!r} needs {expected} plural forms')
                        problems += 1
                elif tr.count('%') != key.count('%'):
                    print(f'[{domain}] {locale}: placeholders differ in {key!r}')
                    problems += 1
    if problems:
        sys.exit(1)

    for domain, target in TARGETS.items():
        out = target['dir']
        out.mkdir(parents=True, exist_ok=True)
        write_po(out / f'{domain}.pot', target['project'], target['table'], None, found[domain])
        for locale in LOCALES:
            base = out / f"{target['prefix']}{locale}"
            write_po(base.with_suffix('.po'), target['project'], target['table'], locale, found[domain])
            write_mo(base.with_suffix('.mo'), target['project'], target['table'], locale)
            write_l10n_php(Path(str(base) + '.l10n.php'), domain, target['table'], locale)
        print(f'{domain}: {len(target["table"])} texts → {out.relative_to(ROOT)}')


if __name__ == '__main__':
    main()

"""Lists translatable strings used in the theme and plugin source."""
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
SOURCES = {
    'medcentrum': ROOT / 'wp-content/themes/medcentrum',
    'medcentrum-booking': ROOT / 'wp-content/plugins/medcentrum-booking',
}
STR = r"'((?:[^'\\]|\\.)*)'"
SINGLE = re.compile(r"\b(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*" + STR + r"\s*,\s*'([a-z-]+)'\s*\)")
PLURAL = re.compile(r"\b_n\(\s*" + STR + r"\s*,\s*" + STR + r"\s*,\s*[^,]+,\s*'([a-z-]+)'\s*\)")


def unescape(s):
    return s.replace("\\'", "'").replace('\\\\', '\\')


def extract():
    """Returns {domain: {msgid or (singular, plural): [file:line, ...]}}."""
    found = {d: {} for d in SOURCES}
    for domain, base in SOURCES.items():
        for path in sorted(base.rglob('*.php')):
            text = path.read_text(encoding='utf-8')
            for rx, plural in ((SINGLE, False), (PLURAL, True)):
                for m in rx.finditer(text):
                    if plural:
                        key, dom = (unescape(m.group(1)), unescape(m.group(2))), m.group(3)
                    else:
                        key, dom = unescape(m.group(1)), m.group(2)
                    if dom != domain:
                        raise SystemExit(f'{path}: wrong text domain {dom!r}')
                    line = text.count('\n', 0, m.start()) + 1
                    found[domain].setdefault(key, []).append(f'{path.relative_to(base)}:{line}')
    return found


if __name__ == '__main__':
    for domain, strings in extract().items():
        print(f'== {domain}: {len(strings)}')
        for key in strings:
            print(repr(key))

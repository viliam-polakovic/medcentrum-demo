# MedCentrum – WordPress web s online objednávaním (DE / EN / SK)

Téma inšpirovaná štruktúrou bmg-swiss.ch, vlastný plugin na objednávanie pacientov na konkrétny deň a čas a prepínač jazykov **DE / EN / SK** (predvolená nemčina).

## Čo je v priečinku

| Cesta | Obsah |
|---|---|
| `dist/medcentrum-tema.zip` | Téma na nahratie do WordPressu |
| `dist/medcentrum-objednavanie-plugin.zip` | Plugin s rezervačným kalendárom |
| `wp-content/themes/medcentrum/` | Zdrojové súbory témy |
| `wp-content/plugins/medcentrum-booking/` | Zdrojové súbory pluginu |
| `tools/i18n/` | Preklady textov (DE, SK) a skript, ktorý z nich vyrobí súbory pre WordPress |
| `tools/generate-images.mjs` | Generátor zástupných ilustrácií (SVG) |
| `playground/` | Lokálny náhľad (WordPress Playground) s ukážkovými dátami v 3 jazykoch |

## Inštalácia na hosting

1. **Pluginy → Pridať nový → Nahrať plugin** → `medcentrum-objednavanie-plugin.zip` → Aktivovať.
2. **Vzhľad → Témy → Pridať novú → Nahrať tému** → `medcentrum-tema.zip` → Aktivovať.
3. **Nastavenia → Všeobecné**: názov webu, popis (pod logom), jazyk webu **Deutsch**, časové pásmo.
4. **Nastavenia → Trvalé odkazy**: „Názov príspevku“.
5. **Jazyky (Polylang)** – pozri nižšie.
6. **Rezervácie → Nastavenia**: služby, ordinačné hodiny, dĺžka termínu, sviatky, e-mail pre notifikácie.
7. Nainštalujte plugin na odosielanie e-mailov (napr. **WP Mail SMTP**), inak potvrdenia často skončia v spame.

### Jazyky DE / EN / SK (Polylang)

Prepínač jazykov v pravom hornom rohu je postavený na bezplatnom plugine **Polylang**. Kým nie je nainštalovaný, web beží v jednom jazyku a v administrácii sa zobrazí upozornenie.

1. **Pluginy → Pridať nový** → vyhľadať „Polylang“ → Inštalovať → Aktivovať. Spustí sa sprievodca.
2. V sprievodcovi pridajte jazyky v poradí **Deutsch** (prvý = predvolený), **English**, **Slovenčina**.
3. Keď sa sprievodca spýta na existujúci obsah, priraďte ho predvolenému jazyku (nemčina).
4. **Jazyky → Nastavenia → Úpravy URL**: zapnite „Skryť informáciu o jazyku v URL pre predvolený jazyk“. Nemčina bude na `/`, angličtina na `/en/`, slovenčina na `/sk/`.
5. Tamže **vypnite „Detekovať jazyk prehliadača“**, aby sa web vždy otvoril po nemecky.
6. Otvorte ľubovoľnú stránku administrácie. Plugin sám vytvorí stránku na objednanie vo všetkých jazykoch („Termin buchen“, „Book an appointment“, „Objednať sa“).
7. Popis webu (pod logom) preložíte v **Jazyky → Preklady**.
8. Články (aktuality) a ďalšie stránky prekladáte cez ikonu „+“ pri jazyku v zozname článkov.

## Rezervačný systém

- Pacient: služba → deň v kalendári → voľný čas → údaje (meno, e-mail, telefón, poisťovňa, poznámka, súhlas GDPR) → potvrdenie. Všetko v jazyku stránky vrátane názvov dní a mesiacov.
- Obsadené časy, víkendy, obedňajšia prestávka a zatvorené dni sa automaticky skryjú. Ten istý termín nemôžu rezervovať dvaja pacienti naraz.
- Klinika dostane e-mail v predvolenom jazyku webu, pacient v jazyku, v ktorom sa objednal.
- **Rezervácie** v administrácii: zoznam (nadchádzajúce / dnes / minulé, filter podľa stavu), zmena stavu, ručné pridanie telefonickej rezervácie, voliteľný e-mail pacientovi pri zmene. Administrácia je v jazyku, ktorý má používateľ nastavený vo svojom profile.
- Služby a poisťovne sa zadávajú s prekladmi na jednom riadku:
  `de: Physiotherapie | en: Physiotherapy | sk: Fyzioterapia`
  Prvý text sa ukladá k rezervácii, preto ho po spustení už nemeňte.
- Formulár sa dá vložiť kamkoľvek shortcodom `[medcentrum_rezervacia]`. Odkaz `…/termin-buchen/?service=Physiotherapie` predvyberie službu.
- Rezervácie vidia iba administrátori a editori.

## Kde zmeniť texty a obrázky

- Texty témy sú v kóde po anglicky a prekladajú sa do nemčiny a slovenčiny. Preklady sú v `tools/i18n/translations.py`. Po úprave spustite:

  ```bash
  python3 tools/i18n/build.py
  ```

  Skript skontroluje, či nič nechýba, a vyrobí súbory `.po`, `.mo` a `.l10n.php` v priečinkoch `languages/`. Preklady sa dajú upravovať aj priamo vo WordPresse pluginom **Loco Translate**.
- Sekcie úvodnej stránky: `wp-content/themes/medcentrum/template-parts/home/*.php`
- Služby, lekári, pracoviská, kontakty, čísla: `wp-content/themes/medcentrum/inc/content.php`
- Obrázky: `wp-content/themes/medcentrum/assets/img/`. Súčasné sú vygenerované ilustrácie (SVG). Nahraďte ich fotkami s rovnakým názvom alebo zmeňte názov súboru v `inc/content.php`.
- Logo: **Vzhľad → Prispôsobiť → Identita webu**.

## Lokálny náhľad

```bash
npx -y @wp-playground/cli@3.1.55 server --login --mount=./wp-content/themes/medcentrum:/wordpress/wp-content/themes/medcentrum --mount=./wp-content/plugins/medcentrum-booking:/wordpress/wp-content/plugins/medcentrum-booking --mount=./playground:/playground --blueprint=./playground/blueprint.json
```

Potom otvorte http://127.0.0.1:9400. Náhľad si sám nainštaluje Polylang, nastaví jazyky DE / EN / SK a ukážkový obsah. Dáta sa po vypnutí stratia.

## Pred spustením naostro

- Doplniť skutočné texty, fotky, mená lekárov, adresy a telefóny (teraz sú vymyslené).
- Zverejniť stránku **Ochrana osobných údajov** v každom jazyku. Odkaz sa automaticky objaví vo formulári aj v pätičke. Pre nemecky hovoriace krajiny doplniť aj **Impressum**.
- Font Barlow sa načítava z Google Fonts – kvôli GDPR odporúčame ho hostovať lokálne (napr. plugin *OMGF*).
- Odkazy „Preisliste“, „Vertragsversicherungen“, „Fragen & Antworten“ a „Impressum“ v pätičke zatiaľ nikam nevedú.

"""German and Slovak translations of the theme and plugin texts.

English is the source language (the texts in the PHP code). Each entry is
  'English text': ('Deutsch', 'Slovensky'),
Plural entries use a tuple key (singular, plural) and lists of forms:
  German 2 forms, Slovak 3 forms (1 / 2–4 / 5+).

After editing run:  python3 tools/i18n/build.py
"""

THEME = {
    # Header, menu, footer
    'Skip to content': ('Zum Inhalt springen', 'Preskočiť na obsah'),
    'Main menu': ('Hauptmenü', 'Hlavné menu'),
    'Footer – important links': ('Fußzeile – wichtige Links', 'Pätička – dôležité odkazy'),
    'Menu': ('Menü', 'Menu'),
    'Open menu': ('Menü öffnen', 'Otvoriť menu'),
    'Close menu': ('Menü schließen', 'Zavrieť menu'),
    'Language': ('Sprache', 'Jazyk'),
    'Book appointment': ('Termin buchen', 'Objednať sa'),
    'Book online': ('Online-Termin buchen', 'Objednať sa online'),
    'For doctors': ('Für Zuweiser', 'Pre lekárov'),
    'For patients': ('Für Patienten', 'Pre pacientov'),
    'Services': ('Leistungen', 'Služby'),
    'Doctors': ('Ärzte', 'Lekári'),
    'About us': ('Über uns', 'O nás'),
    'News': ('Aktuelles', 'Aktuality'),
    'Contact': ('Kontakt', 'Kontakt'),
    'Back to top': ('Zum Seitenanfang', 'Späť hore'),
    'Important links': ('Wichtige Links', 'Dôležité odkazy'),
    'Useful information': ('Nützliche Informationen', 'Užitočné informácie'),
    'Online booking': ('Online-Terminbuchung', 'Online objednávanie'),
    'Opening hours': ('Öffnungszeiten', 'Ordinačné hodiny'),
    'Price list': ('Preisliste', 'Cenník výkonov'),
    'Partner insurers': ('Vertragsversicherungen', 'Zmluvné poisťovne'),
    'FAQ': ('Fragen & Antworten', 'Časté otázky'),
    '© %1$s %2$s – All rights reserved.': ('© %1$s %2$s – Alle Rechte vorbehalten.', '© %1$s %2$s – Všetky práva vyhradené.'),
    'Legal notice': ('Impressum', 'Právne informácie'),
    'Privacy policy': ('Datenschutz', 'Ochrana osobných údajov'),
    'The language switcher (DE / EN / SK) needs the free Polylang plugin.': (
        'Für die Sprachumschaltung (DE / EN / SK) wird das kostenlose Plugin Polylang benötigt.',
        'Prepínanie jazykov (DE / EN / SK) vyžaduje bezplatný plugin Polylang.',
    ),
    'Install Polylang': ('Polylang installieren', 'Nainštalovať Polylang'),

    # Hero
    'What we offer': ('Unser Angebot', 'Naša ponuka'),
    'Modern medicine<br>with a human touch': ('Moderne Medizin<br>mit menschlicher Nähe', 'Moderná medicína<br>s ľudským prístupom'),
    'Our services': ('Unsere Leistungen', 'Naše služby'),

    # About
    'Your health in the best hands': ('Ihre Gesundheit in den besten Händen', 'Vaše zdravie v najlepších rukách'),
    'A medical centre in the heart of Bratislava': ('Ein Gesundheitszentrum im Herzen von Bratislava', 'Zdravotné centrum v srdci Bratislavy'),
    'MedCentrum is a modern medical facility that brings general practice, specialists, diagnostics and a laboratory together under one roof. Book your examination online for a specific day and time – no phone calls and no time in the waiting room.': (
        'MedCentrum ist eine moderne medizinische Einrichtung, die Allgemeinmedizin, Fachärzte, Diagnostik und Labor unter einem Dach vereint. Ihre Untersuchung buchen Sie online für einen bestimmten Tag und eine bestimmte Uhrzeit – ohne Telefonieren und ohne Wartezimmer.',
        'MedCentrum je moderné zdravotnícke zariadenie, ktoré pod jednou strechou spája všeobecnú ambulanciu, odborných lekárov, diagnostiku aj laboratórium. Na vyšetrenie sa objednáte online na konkrétny deň a čas – bez telefonovania a bez čakania v čakárni.',
    ),
    'More about our locations': ('Mehr über unsere Standorte', 'Viac o našich pracoviskách'),
    'experienced doctors and specialists': ('erfahrene Ärztinnen, Ärzte und Spezialisten', 'skúsených lekárov a špecialistov'),
    'locations in Bratislava': ('Standorte in Bratislava', 'pracoviská v Bratislave'),
    'specialist clinics': ('Fachambulanzen', 'odborných ambulancií'),

    # Gallery
    'Photo gallery': ('Bildergalerie', 'Fotogaléria'),
    'Online booking for a specific appointment': ('Online-Buchung zu einem festen Termin', 'Online objednávanie na konkrétny termín'),
    'Examination at the clinic': ('Untersuchung in der Praxis', 'Vyšetrenie v ambulancii'),
    'Modern diagnostics': ('Moderne Diagnostik', 'Moderná diagnostika'),
    'In-house laboratory': ('Eigenes Labor', 'Vlastné laboratórium'),

    # Services
    'Our offer': ('Unser Angebot', 'Naša ponuka'),
    'Our clinics rely on modern diagnostics, individual treatment and a personal approach. We work closely with specialists so you do not have to go from door to door.': (
        'In unseren Praxen setzen wir auf moderne Diagnostik, individuelle Therapien und persönliche Betreuung. Wir arbeiten eng mit Spezialisten zusammen, damit Sie nicht von Tür zu Tür gehen müssen.',
        'V našich ambulanciách sa spoliehame na modernú diagnostiku, individuálnu liečbu a osobný prístup. Úzko spolupracujeme so špecialistami, aby ste nemuseli chodiť od dverí k dverám.',
    ),
    'View': ('Ansicht', 'Zobrazenie'),
    'Cards': ('Karten', 'Karty'),
    'List': ('Liste', 'Zoznam'),
    'Book an appointment': ('Termin vereinbaren', 'Objednať termín'),
    'Previous': ('Zurück', 'Predchádzajúce'),
    'Next': ('Weiter', 'Ďalšie'),
    'General practice': ('Allgemeinmedizin', 'Všeobecná ambulancia'),
    'Comprehensive care for adult patients, from acute complaints to long-term follow-up.': (
        'Umfassende Betreuung erwachsener Patientinnen und Patienten – von akuten Beschwerden bis zur langfristigen Begleitung.',
        'Komplexná starostlivosť o dospelých pacientov, akútne ťažkosti aj dlhodobé sledovanie.',
    ),
    'Neurology': ('Neurologie', 'Neurológia'),
    'Headaches, dizziness, sleep or memory problems. EEG and EMG on site.': (
        'Kopfschmerzen, Schwindel, Schlaf- oder Gedächtnisstörungen. EEG und EMG direkt vor Ort.',
        'Bolesti hlavy, závraty, poruchy spánku či pamäti. Vyšetrenie EEG a EMG na mieste.',
    ),
    'Cardiology': ('Kardiologie', 'Kardiológia'),
    'ECG, echocardiography and Holter monitoring. Prevention and treatment of heart and vascular disease.': (
        'EKG, Echokardiographie und Langzeit-EKG. Vorsorge und Behandlung von Herz- und Gefäßerkrankungen.',
        'EKG, echokardiografia a Holter. Prevencia a liečba ochorení srdca a ciev.',
    ),
    'Orthopaedics': ('Orthopädie', 'Ortopédia'),
    'Back and joint pain, sports injuries. Diagnosis and conservative treatment.': (
        'Rücken- und Gelenkschmerzen, Sportverletzungen. Diagnostik und konservative Behandlung.',
        'Bolesti chrbta, kĺbov a športové úrazy. Diagnostika a konzervatívna liečba.',
    ),
    'Physiotherapy': ('Physiotherapie', 'Fyzioterapia'),
    'Individual exercise, manual techniques and rehabilitation after injuries and surgery.': (
        'Individuelle Übungen, manuelle Techniken und Rehabilitation nach Verletzungen und Operationen.',
        'Individuálne cvičenia, manuálne techniky a rehabilitácia po úrazoch a operáciách.',
    ),
    'Diagnostics and ultrasound': ('Diagnostik und Ultraschall', 'Diagnostika a USG'),
    'Modern ultrasound equipment and fast results during your visit.': (
        'Moderne Ultraschallgeräte und schnelle Befunde direkt bei Ihrem Besuch.',
        'Moderné ultrazvukové prístroje a rýchle vyhodnotenie výsledkov priamo pri návšteve.',
    ),
    'Preventive check-ups': ('Vorsorgeuntersuchungen', 'Preventívne prehliadky'),
    'Regular check-ups covered by insurance and extended packages for companies.': (
        'Regelmäßige Vorsorge, die von der Krankenkasse übernommen wird, sowie erweiterte Pakete für Unternehmen.',
        'Pravidelné prehliadky hradené poisťovňou aj rozšírené balíky pre firmy.',
    ),
    'Laboratory': ('Labor', 'Laboratórium'),
    'Blood tests without waiting, most results on the same day.': (
        'Blutabnahme ohne Wartezeit, die meisten Ergebnisse noch am selben Tag.',
        'Odbery krvi bez čakania, výsledky väčšiny vyšetrení ešte v ten istý deň.',
    ),

    # For doctors / patients
    'Patient referral': ('Patientenzuweisung', 'Odporúčanie pacienta'),
    'I am a doctor and want<br>to refer a patient.': ('Ich bin Zuweiser und möchte<br>einen Patienten anmelden.', 'Som lekár a chcem<br>odoslať pacienta.'),
    'Refer a patient': ('Jetzt zuweisen', 'Odoslať pacienta'),
    'I am a patient and want<br>to book an appointment.': ('Ich bin Patient und möchte<br>einen Termin vereinbaren.', 'Som pacient a chcem<br>sa objednať.'),

    # Team
    'Experienced specialists,<br>exceptional care.': ('Erfahrene Spezialisten,<br>exzellente Betreuung.', 'Skúsení špecialisti,<br>výnimočná starostlivosť.'),
    'Our examinations are carried out by doctors with many years of experience at leading hospitals. From your GP to the neurologist and the physiotherapist, you are in the best hands.': (
        'Unsere Untersuchungen führen Ärztinnen und Ärzte mit langjähriger Erfahrung aus führenden Kliniken durch. Von der Hausärztin über den Neurologen bis zur Physiotherapie sind Sie bei uns in besten Händen.',
        'Vyšetrenia u nás vykonávajú lekári s dlhoročnou praxou z popredných nemocníc. Od všeobecného lekára cez neurológa až po fyzioterapeuta ste v najlepších rukách.',
    ),
    'Head of department, 18 years of experience. Headaches and sleep disorders.': (
        'Chefärztin, 18 Jahre Erfahrung. Kopfschmerzen und Schlafstörungen.',
        'Primárka, 18 rokov praxe. Bolesti hlavy a poruchy spánku.',
    ),
    'Echocardiography, hypertension and prevention of heart disease.': (
        'Echokardiographie, Bluthochdruck und Vorsorge von Herzerkrankungen.',
        'Echokardiografia, hypertenzia a prevencia srdcových ochorení.',
    ),
    'Comprehensive care for adults and preventive check-ups.': (
        'Umfassende Betreuung Erwachsener und Vorsorgeuntersuchungen.',
        'Komplexná starostlivosť o dospelých a preventívne prehliadky.',
    ),
    'Spine, large joints and sports medicine.': ('Wirbelsäule, große Gelenke und Sportmedizin.', 'Chrbtica, veľké kĺby a športová medicína.'),

    # Booking section
    'Book your appointment for a specific day and time': ('Ihr Termin – am Wunschtag zur Wunschzeit', 'Objednajte sa na konkrétny deň a čas'),
    'Choose a service, a free slot in the calendar and fill in your contact details. You will receive a confirmation by e-mail.': (
        'Wählen Sie eine Leistung und einen freien Termin im Kalender und geben Sie Ihre Kontaktdaten ein. Die Bestätigung erhalten Sie per E-Mail.',
        'Vyberte si službu, voľný termín v kalendári a vyplňte kontaktné údaje. Potvrdenie dostanete e-mailom.',
    ),
    'Choose a service': ('Leistung wählen', 'Vyberte službu'),
    'Pick a day and time': ('Tag und Uhrzeit wählen', 'Zvoľte deň a čas'),
    'Fill in your details': ('Daten eingeben', 'Vyplňte údaje'),
    'Confirmation by e-mail': ('Bestätigung per E-Mail', 'Potvrdenie e-mailom'),
    'Prefer to call?': ('Lieber telefonisch?', 'Radšej telefonicky?'),
    'The booking calendar appears once the %s plugin is activated.': (
        'Der Buchungskalender erscheint, sobald das Plugin %s aktiviert ist.',
        'Rezervačný kalendár sa zobrazí po aktivovaní pluginu %s.',
    ),

    # Locations
    'Our locations': ('Unsere Standorte', 'Naše pracoviská'),
    'Always close by.': ('Immer in Ihrer Nähe.', 'Vždy nablízku.'),
    'Find the clinic closest to you. You can book at every location online or by phone.': (
        'Finden Sie die Praxis in Ihrer Nähe. An jedem Standort können Sie online oder telefonisch einen Termin buchen.',
        'Nájdite ambulanciu, ktorá je vám najbližšie. Na každé pracovisko sa môžete objednať online aj telefonicky.',
    ),
    'Map of %s locations': ('Karte der Standorte von %s', 'Mapa pracovísk %s'),
    'Mon – Fri 7:30 – 16:00': ('Mo – Fr 7:30 – 16:00', 'Po – Pi 7:30 – 16:00'),
    'Mon – Fri 7:00 – 18:00': ('Mo – Fr 7:00 – 18:00', 'Po – Pi 7:00 – 18:00'),
    'Mon – Thu 8:00 – 15:00': ('Mo – Do 8:00 – 15:00', 'Po – Št 8:00 – 15:00'),

    # News, pages
    'News and events': ('Neuigkeiten und Veranstaltungen', 'Novinky a podujatia'),
    'All news': ('Alle Beiträge', 'Všetky aktuality'),
    'F j, Y': ('j. F Y', 'j. F Y'),
    'Nothing here yet.': ('Hier gibt es noch nichts.', 'Zatiaľ tu nič nie je.'),
    'Error 404': ('Fehler 404', 'Chyba 404'),
    'Page not found': ('Seite nicht gefunden', 'Stránku sme nenašli'),
    'The link is probably broken or the page has moved.': (
        'Der Link ist vermutlich fehlerhaft oder die Seite wurde verschoben.',
        'Odkaz je pravdepodobne neplatný alebo stránka bola presunutá.',
    ),
    'Back to home page': ('Zur Startseite', 'Späť na úvod'),

    # Theme header (style.css), shown under Appearance → Themes
    'Modern theme for a clinic or medical centre: home page with services, doctors, locations and online booking (requires the “MedCentrum – Online Booking” plugin). Multilingual DE / EN / SK with Polylang.': (
        'Modernes Theme für eine Praxis oder ein Gesundheitszentrum: Startseite mit Leistungen, Ärzteteam, Standorten und Online-Terminbuchung (erfordert das Plugin „MedCentrum – Online Booking“). Mehrsprachig DE / EN / SK mit Polylang.',
        'Moderná téma pre ambulanciu alebo zdravotnícke centrum: úvodná stránka so službami, lekármi, pracoviskami a online objednávaním (vyžaduje plugin „MedCentrum – Online Booking“). Viacjazyčná DE / EN / SK cez Polylang.',
    ),
}

PLUGIN = {
    # Plugin header, shown under Plugins
    'MedCentrum – Online Booking': ('MedCentrum – Online-Terminbuchung', 'MedCentrum – Online objednávanie'),
    'Booking calendar where patients pick a specific day and time. Add the form to any page with the [medcentrum_rezervacia] shortcode. Multilingual (DE / EN / SK), works with Polylang.': (
        'Buchungskalender, in dem Patienten einen bestimmten Tag und eine Uhrzeit wählen. Das Formular fügen Sie mit dem Shortcode [medcentrum_rezervacia] in jede Seite ein. Mehrsprachig (DE / EN / SK), kompatibel mit Polylang.',
        'Rezervačný kalendár, v ktorom sa pacienti objednajú na konkrétny deň a čas. Formulár vložíte na stránku shortcodom [medcentrum_rezervacia]. Viacjazyčný (DE / EN / SK), spolupracuje s Polylangom.',
    ),

    # Bookings in the admin
    'Bookings': ('Buchungen', 'Rezervácie'),
    'Booking': ('Buchung', 'Rezervácia'),
    'Add booking': ('Buchung hinzufügen', 'Pridať rezerváciu'),
    'New booking': ('Neue Buchung', 'Nová rezervácia'),
    'Search': ('Suchen', 'Hľadať'),
    'No bookings': ('Keine Buchungen', 'Žiadne rezervácie'),
    'The trash is empty': ('Der Papierkorb ist leer', 'Kôš je prázdny'),
    'Awaiting confirmation': ('Wartet auf Bestätigung', 'Čaká na potvrdenie'),
    'Confirmed': ('Bestätigt', 'Potvrdená'),
    'Cancelled': ('Storniert', 'Zrušená'),
    'Appointment': ('Termin', 'Termín'),
    'Patient': ('Patient', 'Pacient'),
    'Service': ('Leistung', 'Služba'),
    'Contact': ('Kontakt', 'Kontakt'),
    'Status': ('Status', 'Stav'),
    'Period': ('Zeitraum', 'Obdobie'),
    'Upcoming': ('Bevorstehend', 'Nadchádzajúce'),
    'Today': ('Heute', 'Dnes'),
    'Past': ('Vergangen', 'Minulé'),
    'All': ('Alle', 'Všetky'),
    'All statuses': ('Alle Status', 'Všetky stavy'),
    'Booking details': ('Buchungsdetails', 'Detaily rezervácie'),
    'Date': ('Datum', 'Dátum'),
    'Time': ('Uhrzeit', 'Čas'),
    'First name': ('Vorname', 'Meno'),
    'Last name': ('Nachname', 'Priezvisko'),
    'E-mail': ('E-Mail', 'E-mail'),
    'Phone': ('Telefon', 'Telefón'),
    'Date of birth': ('Geburtsdatum', 'Dátum narodenia'),
    'Health insurer': ('Krankenkasse', 'Zdravotná poisťovňa'),
    'Note from the patient': ('Anmerkung des Patienten', 'Poznámka pacienta'),
    'E-mail the patient about the new status or time': (
        'Patienten per E-Mail über den neuen Status oder Termin informieren',
        'Poslať pacientovi e-mail o zmene stavu alebo termínu',
    ),
    'Booked online': ('Online gebucht', 'Rezervované online'),
    'Entered manually': ('Manuell erfasst', 'Zadané ručne'),
    'Language': ('Sprache', 'Jazyk'),
    'l, F j, Y': ('l, j. F Y', 'l j. F Y'),
    '%1$s at %2$s': ('%1$s um %2$s', '%1$s o %2$s'),

    # E-mails
    'New online booking': ('Neue Online-Buchung', 'Nová online rezervácia'),
    'The booking is waiting for your confirmation:': ('Die Buchung wartet auf Ihre Bestätigung:', 'Rezervácia čaká na vaše potvrdenie:'),
    'Booking details:': ('Buchungsdetails:', 'Detail rezervácie:'),
    'New booking: %1$s – %2$s': ('Neue Buchung: %1$s – %2$s', 'Nová rezervácia: %1$s – %2$s'),
    'Your appointment is confirmed – %s': ('Ihr Termin ist bestätigt – %s', 'Potvrdenie termínu – %s'),
    'We confirm your appointment:': ('Wir bestätigen Ihren Termin:', 'Potvrdzujeme Váš termín:'),
    'Your appointment has been cancelled – %s': ('Ihr Termin wurde storniert – %s', 'Zrušenie termínu – %s'),
    'Your appointment has been cancelled:': ('Ihr Termin wurde storniert:', 'Váš termín bol zrušený:'),
    'We have received your request – %s': ('Wir haben Ihre Anfrage erhalten – %s', 'Prijali sme Vašu požiadavku – %s'),
    'We have received your appointment request. We will send you another e-mail once it is confirmed.': (
        'Wir haben Ihre Terminanfrage erhalten. Sobald der Termin bestätigt ist, erhalten Sie eine weitere E-Mail.',
        'Prijali sme Vašu požiadavku na termín. Po potvrdení Vám pošleme ďalší e-mail.',
    ),
    'Hello,': ('Guten Tag,', 'Dobrý deň,'),
    'Please arrive 10 minutes early and bring your health insurance card.': (
        'Bitte kommen Sie 10 Minuten vor dem Termin und bringen Sie Ihre Versichertenkarte mit.',
        'Prosíme, príďte 10 minút pred termínom a prineste si preukaz poistenca.',
    ),
    'If you need to change or cancel the appointment, call us at %s.': (
        'Wenn Sie den Termin ändern oder absagen möchten, rufen Sie uns unter %s an.',
        'Ak potrebujete termín zmeniť alebo zrušiť, zavolajte nám na %s.',
    ),
    'If you need to change or cancel the appointment, reply to this e-mail.': (
        'Wenn Sie den Termin ändern oder absagen möchten, antworten Sie einfach auf diese E-Mail.',
        'Ak potrebujete termín zmeniť alebo zrušiť, odpovedzte na tento e-mail.',
    ),
    'Kind regards': ('Freundliche Grüße', 'S pozdravom'),

    # Booking page and REST answers
    'Book an appointment': ('Termin buchen', 'Objednať sa'),
    'The request could not be processed.': ('Die Anfrage konnte nicht verarbeitet werden.', 'Požiadavku sa nepodarilo spracovať.'),
    'Too many bookings were made from this device. Please try again later or call us.': (
        'Von diesem Gerät wurden zu viele Buchungen vorgenommen. Bitte versuchen Sie es später erneut oder rufen Sie uns an.',
        'Z tohto zariadenia už bolo vytvorených priveľa rezervácií. Skúste to prosím neskôr alebo nám zavolajte.',
    ),
    'Please check the highlighted fields.': ('Bitte prüfen Sie die markierten Felder.', 'Skontrolujte prosím vyznačené polia.'),
    'Someone else is booking this slot right now. Please try again in a moment.': (
        'Dieser Termin wird gerade von jemand anderem gebucht. Bitte versuchen Sie es gleich noch einmal.',
        'Tento termín si práve rezervuje niekto iný. Skúste to o chvíľu znova.',
    ),
    'This slot is no longer available. Please choose another time.': (
        'Dieser Termin ist nicht mehr frei. Bitte wählen Sie eine andere Uhrzeit.',
        'Tento termín už nie je voľný. Vyberte si prosím iný čas.',
    ),
    'The booking could not be saved. Please call us.': (
        'Die Buchung konnte nicht gespeichert werden. Bitte rufen Sie uns an.',
        'Rezerváciu sa nepodarilo uložiť. Zavolajte nám prosím.',
    ),
    'Please choose a service.': ('Bitte wählen Sie eine Leistung.', 'Vyberte službu.'),
    'Please choose a date and time.': ('Bitte wählen Sie Datum und Uhrzeit.', 'Vyberte dátum a čas.'),
    'Please enter your first name.': ('Bitte geben Sie Ihren Vornamen ein.', 'Zadajte meno.'),
    'Please enter your last name.': ('Bitte geben Sie Ihren Nachnamen ein.', 'Zadajte priezvisko.'),
    'Please enter a valid e-mail address.': ('Bitte geben Sie eine gültige E-Mail-Adresse ein.', 'Zadajte platný e-mail.'),
    'Please enter a valid phone number.': ('Bitte geben Sie eine gültige Telefonnummer ein.', 'Zadajte platné telefónne číslo.'),
    'Please enter a valid date of birth.': ('Bitte geben Sie ein gültiges Geburtsdatum ein.', 'Zadajte platný dátum narodenia.'),
    'Please choose your health insurer.': ('Bitte wählen Sie Ihre Krankenkasse.', 'Vyberte zdravotnú poisťovňu.'),
    'We need your consent to process your data to book the appointment.': (
        'Für die Terminbuchung benötigen wir Ihre Einwilligung zur Datenverarbeitung.',
        'Bez súhlasu so spracovaním údajov nemôžeme termín rezervovať.',
    ),

    # Settings page
    'Booking settings': ('Buchungseinstellungen', 'Nastavenia objednávania'),
    'Settings': ('Einstellungen', 'Nastavenia'),
    'Add the booking form to any page with the %s shortcode. The MedCentrum theme also shows it on the home page.': (
        'Das Buchungsformular fügen Sie mit dem Shortcode %s in jede Seite ein. Das MedCentrum-Theme zeigt es auch auf der Startseite.',
        'Rezervačný formulár vložíte na ľubovoľnú stránku shortcodom %s. Téma MedCentrum ho zobrazuje aj na úvodnej stránke.',
    ),
    'Clinic': ('Praxis', 'Ambulancia'),
    'Name': ('Name', 'Názov'),
    'E-mail for new bookings': ('E-Mail für neue Buchungen', 'E-mail pre nové rezervácie'),
    'Shown to patients who cannot find a suitable slot.': (
        'Wird Patienten angezeigt, die keinen passenden Termin finden.',
        'Zobrazí sa pacientom, keď si nenájdu vhodný termín.',
    ),
    'Services': ('Leistungen', 'Služby'),
    'One service per line. Patients choose it in the first step.': (
        'Eine Leistung pro Zeile. Patienten wählen sie im ersten Schritt.',
        'Jedna služba na riadok. Pacient si ju vyberie v prvom kroku.',
    ),
    'Translations go on the same line: de: Physiotherapie | en: Physiotherapy | sk: Fyzioterapia. The first text is stored with each booking – do not change it once bookings exist.': (
        'Übersetzungen kommen in dieselbe Zeile: de: Physiotherapie | en: Physiotherapy | sk: Fyzioterapia. Der erste Text wird mit jeder Buchung gespeichert – ändern Sie ihn nicht mehr, sobald Buchungen existieren.',
        'Preklady píšte do toho istého riadku: de: Physiotherapie | en: Physiotherapy | sk: Fyzioterapia. Prvý text sa ukladá ku každej rezervácii – keď už existujú rezervácie, nemeňte ho.',
    ),
    'Health insurers': ('Krankenkassen', 'Zdravotné poisťovne'),
    'One per line. Leave empty to hide the field from the form.': (
        'Eine pro Zeile. Leer lassen, um das Feld im Formular auszublenden.',
        'Jedna na riadok. Ak necháte prázdne, pole sa vo formulári nezobrazí.',
    ),
    'Opening hours': ('Öffnungszeiten', 'Ordinačné hodiny'),
    'Day': ('Tag', 'Deň'),
    'Open': ('Geöffnet', 'Ordinuje sa'),
    'From': ('Von', 'Od'),
    'To': ('Bis', 'Do'),
    'Lunch break': ('Mittagspause', 'Obedňajšia prestávka'),
    'Leave empty if no break should be left out of the calendar.': (
        'Leer lassen, wenn keine Pause aus dem Kalender ausgenommen werden soll.',
        'Nechajte prázdne, ak sa prestávka nemá z kalendára vynechať.',
    ),
    'Appointment length': ('Termindauer', 'Dĺžka termínu'),
    'minutes': ('Minuten', 'minút'),
    'Patients per slot': ('Patienten pro Termin', 'Pacientov na jeden termín'),
    'Increase if several doctors see patients at the same time.': (
        'Erhöhen, wenn mehrere Ärztinnen oder Ärzte gleichzeitig behandeln.',
        'Zvýšte, ak v rovnakom čase ordinuje viac lekárov.',
    ),
    'Book ahead': ('Im Voraus buchbar', 'Objednávanie dopredu'),
    'days': ('Tage', 'dní'),
    'Earliest': ('Frühestens in', 'Najskôr o'),
    'hours from now': ('Stunden', 'hodín od teraz'),
    'Closed days': ('Schließtage', 'Zatvorené dni'),
    'One date (YYYY-MM-DD) or range per line – public holidays, vacations. Text after the date is a note.': (
        'Ein Datum (JJJJ-MM-TT) oder ein Zeitraum pro Zeile – Feiertage, Urlaub. Text nach dem Datum dient als Notiz.',
        'Jeden dátum (RRRR-MM-DD) alebo rozsah na riadok – sviatky, dovolenky. Text za dátumom slúži ako poznámka.',
    ),
    'Confirmation': ('Bestätigung', 'Potvrdenie'),
    'Confirm online bookings automatically': ('Online-Buchungen automatisch bestätigen', 'Online rezervácie potvrdiť automaticky'),
    'When off, bookings wait for your confirmation. The patient first gets an e-mail that the request was received, then another one once you confirm.': (
        'Wenn deaktiviert, warten Buchungen auf Ihre Bestätigung. Der Patient erhält zuerst eine E-Mail über den Eingang der Anfrage und nach Ihrer Bestätigung eine weitere.',
        'Ak je vypnuté, rezervácia čaká na vaše potvrdenie. Pacient dostane e-mail o prijatí požiadavky a po potvrdení ďalší.',
    ),
    'Save settings': ('Einstellungen speichern', 'Uložiť nastavenia'),

    # Booking form
    'Book online': ('Termin online buchen', 'Objednajte sa online'),
    'Change': ('Ändern', 'Zmeniť'),
    'Date and time': ('Datum und Uhrzeit', 'Dátum a čas'),
    'Previous month': ('Vorheriger Monat', 'Predchádzajúci mesiac'),
    'Next month': ('Nächster Monat', 'Nasledujúci mesiac'),
    'free slots': ('freie Termine', 'voľné termíny'),
    'Your details': ('Ihre Angaben', 'Vaše údaje'),
    'Choose…': ('Bitte wählen…', 'Vyberte…'),
    'Reason for the visit / note': ('Grund des Besuchs / Anmerkung', 'Dôvod návštevy / poznámka'),
    'I agree to the processing of my personal data for booking and providing medical care.': (
        'Ich willige in die Verarbeitung meiner personenbezogenen Daten zur Terminbuchung und medizinischen Versorgung ein.',
        'Súhlasím so spracovaním osobných údajov na účel objednania a poskytnutia zdravotnej starostlivosti.',
    ),
    'Privacy policy': ('Datenschutzerklärung', 'Ochrana osobných údajov'),
    'Continue': ('Weiter', 'Pokračovať'),
    'Book appointment': ('Verbindlich buchen', 'Záväzne objednať'),
    'Book another appointment': ('Weiteren Termin buchen', 'Objednať ďalší termín'),
    'Your data is sent encrypted and only the clinic staff can see it.': (
        'Ihre Daten werden verschlüsselt übertragen und sind nur für das Praxisteam sichtbar.',
        'Vaše údaje sú prenášané šifrovane a vidí ich iba personál ambulancie.',
    ),
    'Select a day in the calendar.': ('Wählen Sie einen Tag im Kalender.', 'Vyberte deň v kalendári.'),
    'There are no free slots left this month.': ('In diesem Monat sind keine Termine mehr frei.', 'V tomto mesiaci už nie sú voľné termíny.'),
    'Loading available times…': ('Freie Zeiten werden geladen…', 'Načítavam voľné časy…'),
    'The times could not be loaded. Please try again.': (
        'Die Zeiten konnten nicht geladen werden. Bitte versuchen Sie es erneut.',
        'Časy sa nepodarilo načítať. Skúste to prosím znova.',
    ),
    'The calendar could not be loaded. Please refresh the page or call us at %s.': (
        'Der Kalender konnte nicht geladen werden. Bitte laden Sie die Seite neu oder rufen Sie uns unter %s an.',
        'Kalendár sa nepodarilo načítať. Obnovte prosím stránku alebo nám zavolajte na %s.',
    ),
    'The calendar could not be loaded. Please refresh the page.': (
        'Der Kalender konnte nicht geladen werden. Bitte laden Sie die Seite neu.',
        'Kalendár sa nepodarilo načítať. Obnovte prosím stránku.',
    ),
    '%s – fully booked.': ('%s – ausgebucht.', '%s – už bez voľných termínov.'),
    'no free slots': ('keine freien Termine', 'bez voľných termínov'),
    'Morning': ('Vormittag', 'Dopoludnia'),
    'Afternoon': ('Nachmittag', 'Popoludní'),
    'Connection failed. Check your internet connection and try again.': (
        'Verbindung fehlgeschlagen. Prüfen Sie Ihre Internetverbindung und versuchen Sie es erneut.',
        'Spojenie zlyhalo. Skontrolujte pripojenie a skúste to znova.',
    ),
    'The booking could not be completed.': ('Die Buchung konnte nicht abgeschlossen werden.', 'Rezerváciu sa nepodarilo dokončiť.'),
    'Thank you, your appointment is booked': ('Vielen Dank, Ihr Termin ist gebucht', 'Ďakujeme, Váš termín je rezervovaný'),
    'Thank you, we have received your request': ('Vielen Dank, wir haben Ihre Anfrage erhalten', 'Ďakujeme, požiadavku sme prijali'),
    '%1$s – %2$s. We have sent a confirmation to %3$s.': (
        '%1$s – %2$s. Die Bestätigung haben wir an %3$s gesendet.',
        '%1$s – %2$s. Potvrdenie sme poslali na %3$s.',
    ),
    '%1$s – %2$s. We will confirm the appointment by e-mail at %3$s.': (
        '%1$s – %2$s. Wir bestätigen den Termin per E-Mail an %3$s.',
        '%1$s – %2$s. Termín Vám potvrdíme e-mailom na %3$s.',
    ),
    ('%d free slot', '%d free slots'): (
        ['%d freier Termin', '%d freie Termine'],
        ['%d voľný termín', '%d voľné termíny', '%d voľných termínov'],
    ),
}

# Strings WordPress translates from file headers; the extractor does not see them.
HEADER_STRINGS = {
    'medcentrum': {
        'Modern theme for a clinic or medical centre: home page with services, doctors, locations and online booking (requires the “MedCentrum – Online Booking” plugin). Multilingual DE / EN / SK with Polylang.',
    },
    'medcentrum-booking': {
        'MedCentrum – Online Booking',
        'Booking calendar where patients pick a specific day and time. Add the form to any page with the [medcentrum_rezervacia] shortcode. Multilingual (DE / EN / SK), works with Polylang.',
    },
}

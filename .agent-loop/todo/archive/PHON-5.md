# PHON-5: Add Polish phonetic algorithm (pl)

- **Ticket:** PHON-5
- **Lane:** VERIFY
- **Status:** Selected
- **Domain:** phonetic
- **Created:** 2026-08-16T15:31:45+00:00
- **Updated:** 2026-08-16T15:53:34+00:00
- **Summary:** New PhoneticPolish class: Polish digraph/diacritic normalisation plus a documented code table, wired into Phonetic('pl').
- **Validation:** php vendor/bin/phpunit -c phpunit.xml
- **Priority:** 5
- **Wave:** 2
- **Format version:** 1

## Agent Task Brief
Add PhoneticPolish: fold the polish diacritics to their plain letter (so a query typed without diacritics still matches) and merge the digraph/single-letter sound pairs ch=h, cz=c, sz=s, rz=z.

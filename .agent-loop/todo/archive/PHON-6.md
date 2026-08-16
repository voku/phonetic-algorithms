# PHON-6: Add Portuguese phonetic algorithm (pt)

- **Ticket:** PHON-6
- **Lane:** VERIFY
- **Status:** Selected
- **Domain:** phonetic
- **Created:** 2026-08-16T15:31:45+00:00
- **Updated:** 2026-08-16T15:56:39+00:00
- **Summary:** New PhoneticPortuguese class for Portuguese orthography, wired into Phonetic('pt').
- **Validation:** php vendor/bin/phpunit -c phpunit.xml
- **Priority:** 6
- **Wave:** 3
- **Format version:** 1

## Agent Task Brief
Add PhoneticPortuguese: ch/x = /S/, lh, nh, c-cedilla, c/g before e-i, qu, silent h, final m as a nasal n, and folded accents.

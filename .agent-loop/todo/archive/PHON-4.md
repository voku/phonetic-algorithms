# PHON-4: Add Dutch phonetic algorithm (nl)

- **Ticket:** PHON-4
- **Lane:** VERIFY
- **Status:** Selected
- **Domain:** phonetic
- **Created:** 2026-08-16T15:31:45+00:00
- **Updated:** 2026-08-16T15:50:19+00:00
- **Summary:** New PhoneticDutch class: Cologne-phonetics family code table adapted to Dutch orthography (ij, sch, oe, ui, ...), wired into Phonetic('nl').
- **Validation:** php vendor/bin/phpunit -c phpunit.xml
- **Priority:** 4
- **Wave:** 2
- **Format version:** 1

## Agent Task Brief
Add PhoneticDutch: documented dutch rule set for ij/ei/y, oe, ou/au, g and ch as one sound, s/z, f/v, final devoicing and collapsed double letters.

# PHON-2: Add Italian phonetic algorithm (it)

- **Ticket:** PHON-2
- **Lane:** VERIFY
- **Status:** Selected
- **Domain:** phonetic
- **Created:** 2026-08-16T15:31:45+00:00
- **Updated:** 2026-08-16T15:41:57+00:00
- **Summary:** New PhoneticItalian class implementing an explicitly documented Italian orthography-to-sound rule set, wired into Phonetic('it').
- **Validation:** php vendor/bin/phpunit -c phpunit.xml
- **Priority:** 2
- **Wave:** 2
- **Format version:** 1

## Agent Task Brief
Add PhoneticItalian: a documented Italian orthography-to-sound mapping (c/g before front vowels, ch/gh, ci/gi + a-o-u, gli, gn, sc/sci, qu, silent h, collapsed geminates) producing a readable upper-case ascii key.

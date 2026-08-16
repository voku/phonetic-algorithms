# PHON-3: Add Spanish phonetic algorithm (es)

- **Ticket:** PHON-3
- **Lane:** VERIFY
- **Status:** Selected
- **Domain:** phonetic
- **Created:** 2026-08-16T15:31:45+00:00
- **Updated:** 2026-08-16T15:47:06+00:00
- **Summary:** New PhoneticSpanish class implementing the published Spanish-Metaphone rule set, wired into Phonetic('es').
- **Validation:** php vendor/bin/phpunit -c phpunit.xml
- **Priority:** 3
- **Wave:** 2
- **Format version:** 1

## Agent Task Brief
Add PhoneticSpanish: a documented spanish rule set covering b/v, seseo (c-e/i, z, s), yeismo (ll/y), the silent h, g/j, gu/gue and qu, producing a readable upper-case ascii key.

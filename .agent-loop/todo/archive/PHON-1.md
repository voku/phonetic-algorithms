# PHON-1: Validate the already implemented phonetic algorithms (de, en, fr)

- **Ticket:** PHON-1
- **Lane:** VERIFY
- **Status:** Selected
- **Domain:** phonetic
- **Created:** 2026-08-16T15:31:33+00:00
- **Updated:** 2026-08-16T15:37:49+00:00
- **Summary:** Add specification-anchored characterisation tests for PhoneticGerman, PhoneticEnglish and PhoneticFrench and fix any deviation found.
- **Validation:** php vendor/bin/phpunit -c phpunit.xml
- **Priority:** 1
- **Wave:** 1
- **Format version:** 1

## Agent Task Brief
Characterise and validate the three shipped implementations against their published specifications: Koelner Phonetik (de), metaphone (en), SOUNDEX FR (fr). Add spec-anchored tests; fix real deviations only.

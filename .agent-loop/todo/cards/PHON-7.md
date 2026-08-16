# PHON-7: Add Swedish phonetic algorithm (sv)

- **Ticket:** PHON-7
- **Lane:** BACKLOG
- **Status:** Selected
- **Domain:** phonetic
- **Created:** 2026-08-16T15:31:45+00:00
- **Updated:** 2026-08-16T15:56:56+00:00
- **Summary:** New PhoneticSwedish class for Swedish orthography, wired into Phonetic('sv').
- **Next:** Decide fold-vs-expand for a-ring/a-umlaut/o-umlaut first (see finding.2026-08-16 phonetic.diacritic_fold_or_expand); swedish keyboards have these keys, so folding is probably wrong here.
- **Priority:** 7
- **Wave:** 4
- **Format version:** 1

## Agent Task Brief
Add PhoneticSwedish. Design note: the front vowels that soften a consonant include a-ring, a-umlaut and o-umlaut, so the sje-sound rules (sj/skj/stj/sch, sk before a front vowel) and the tje-sound rules (tj/kj, k before a front vowel) have to run BEFORE UTF8::to_ascii folds them - unlike every language shipped so far, where the fold may happen first.

# PHON-5: Add Polish phonetic algorithm (pl)

Add `PhoneticPolish` implementing `PhoneticInterface` and register it as
language `pl` in `Phonetic`.

Two things break a polish name search: a query typed without diacritics
("Walesa" for "Wałęsa") and the digraphs that share a sound with a single
letter ("rz" / "ż", "cz" / "ć", "sz" / "ś", "ch" / "h").

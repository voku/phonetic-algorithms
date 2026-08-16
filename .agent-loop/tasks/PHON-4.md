# PHON-4: Add Dutch phonetic algorithm (nl)

Add `PhoneticDutch` implementing `PhoneticInterface` and register it as
language `nl` in `Phonetic`.

The dutch spelling variants that matter for a name search are "ij" vs. "ei" vs.
"y", "s" vs. "z", "f" vs. "v", "g" vs. "ch", the doubled consonant ("Jansen" /
"Janssen") and the final devoicing ("hond" / "hont").

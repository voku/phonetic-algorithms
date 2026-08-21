# PHON-6: Add Portuguese phonetic algorithm (pt)

Add `PhoneticPortuguese` implementing `PhoneticInterface` and register it as
language `pt` in `Phonetic`.

Applies the PHON-5 finding: an accent is folded, because a portuguese query is
usually typed without one, but "ç" is pre-substituted, because folding it to
"c" would turn an /s/ into a /k/.

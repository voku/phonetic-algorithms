# PHON-2: Add Italian phonetic algorithm (it)

Add `PhoneticItalian` implementing `PhoneticInterface` and register it as
language `it` in `Phonetic`.

Italian orthography is close to phonemic, so the class maps spelling to a
readable upper-case key instead of to digits, in the same spirit as
`PhoneticFrench`.

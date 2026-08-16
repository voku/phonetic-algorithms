# PHON-3: Add Spanish phonetic algorithm (es)

Add `PhoneticSpanish` implementing `PhoneticInterface` and register it as
language `es` in `Phonetic`.

The class merges the sound pairs that spanish spelling keeps apart but
pronunciation does not (b/v, c-z-s under seseo, ll/y under yeismo) and drops
the silent "h", so that the classic spanish misspellings collapse into one key.

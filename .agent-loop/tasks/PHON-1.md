# PHON-1: Validate the already implemented phonetic algorithms (de, en, fr)

Validate `PhoneticGerman`, `PhoneticEnglish` and `PhoneticFrench` against the
specifications named in their own docblocks, using tests that encode the
specification rules instead of the current output.

- de: Koelner Phonetik code table (Postel 1969)
- en: PHP native `metaphone()` after ASCII folding
- fr: SOUNDEX FR (Edouard Berge, v1.2)

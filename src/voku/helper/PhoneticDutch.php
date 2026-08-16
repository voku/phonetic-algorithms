<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * PhoneticDutch-Helper-Class
 *
 * @package voku\helper
 */
final class PhoneticDutch implements PhoneticInterface
{
  /**
   * Phonetic for the dutch language.
   *
   * There is no single published standard for dutch - so this class is an
   * explicitly documented rule set, not an implementation of a named algorithm.
   * It targets the spelling variants that actually split one dutch name into
   * several database rows:
   *
   * - "ij", "ei" and "y" are one sound -> "Meijer" === "Meier" === "Meyer"
   * - "g" and "ch" are one sound       -> "Van Gogh"
   * - "s" and "z" / "f" and "v" are hard to keep apart -> "Vries" === "Fries"
   * - a doubled consonant is inaudible -> "Jansen" === "Janssen"
   * - a final "b"/"d" is spoken as "p"/"t" ("final devoicing") -> "hond" === "hont"
   *
   * The key alphabet:
   * ==============================================================
   * Key  Sound        Written in dutch as
   * ---  -----------  ----------------------------------------
   * Y    /ɛi/         ij, ei, eij, ey, y
   * U    /u/, /y/     oe, u
   * AU   /ʌu/         ou, au (also ouw, auw)
   * G    /x/, /ɣ/     g, ch, gh
   * S    /s/, /z/     s, z, c (before e,i,y)
   * F    /f/, /v/     f, v
   * K    /k/          k, c (before a,o,u or a consonant), ck, q
   * W    /ʋ/          w
   * T    /t/          t, th, dt, final d
   * P    /p/          p, final b
   * KS   /ks/         x
   * --------------------------------------------------------------
   *
   * Additional rules:
   * - "sch" and "au" need no rule of their own: "s" plus "ch" already gives
   *   "SG", and "a" plus "u" already gives the same "AU" that "ou" produces.
   * - a double letter is written once, so a long vowel and a short vowel share
   *   one key ("groot" === "grot"). That is a deliberate simplification: vowel
   *   length is phonemic in dutch, but a doubled letter is the far more common
   *   spelling mistake in a search query.
   * - every char that is not a letter is removed, so the space in a name like
   *   "van der Berg" does not matter for `phonetic_word`.
   *
   * @param string $word
   *
   * @return string
   */
  public function phonetic_word($word): string
  {
    // init
    $word = (string)$word;

    if (!isset($word[0])) {
      return '';
    }

    //
    // 1. normalize: lowercase -> ascii -> letters only -> uppercase
    //

    $word = UTF8::strtolower($word);
    $word = UTF8::to_ascii($word);
    $word = (string)\preg_replace('/[^a-z]/', '', $word);

    if ($word === '') {
      return '';
    }

    $word = \strtoupper($word);

    //
    // 2. calculate the key
    //

    $wordLength = \strlen($word);
    $code = '';

    for ($x = 0; $x < $wordLength;) {
      $char = $word[$x];
      $next = $word[$x + 1] ?? '';
      $isLast = ($x === $wordLength - 1);

      switch ($char) {

        case 'I':
          if ($next === 'J') { // "ij" -> /ɛi/
            $code .= 'Y';
            $x += 2;
            break;
          }

          $code .= 'I';
          $x++;
          break;

        case 'E':
          if ($next === 'I' && ($word[$x + 2] ?? '') === 'J') { // "eij" -> /ɛi/, as in "Meijer"
            $code .= 'Y';
            $x += 3;
            break;
          }

          if ($next === 'I' || $next === 'Y') { // "ei", "ey" -> /ɛi/
            $code .= 'Y';
            $x += 2;
            break;
          }

          $code .= 'E';
          $x++;
          break;

        case 'O':
          if ($next === 'E') { // "oe" -> /u/
            $code .= 'U';
            $x += 2;
            break;
          }

          if ($next === 'U') { // "ou" -> /ʌu/
            $code .= 'AU';
            $x += 2;
            break;
          }

          $code .= 'O';
          $x++;
          break;

        case 'C':
          if ($next === 'H') { // "ch" -> /x/, the same sound as "g"
            $code .= 'G';
            $x += 2;
            break;
          }

          if ($next === 'K') { // "ck" -> /k/
            $code .= 'K';
            $x += 2;
            break;
          }

          if ($next === 'E' || $next === 'I' || $next === 'Y') { // loanwords: "centrum"
            $code .= 'S';
            $x++;
            break;
          }

          $code .= 'K';
          $x++;
          break;

        case 'G':
          if ($next === 'H') { // "gh" -> /x/, as in "Gogh"
            $code .= 'G';
            $x += 2;
            break;
          }

          $code .= 'G';
          $x++;
          break;

        case 'D':
          if ($next === 'T') { // "dt" -> /t/
            $code .= 'T';
            $x += 2;
            break;
          }

          $code .= ($isLast ? 'T' : 'D'); // final devoicing
          $x++;
          break;

        case 'B':
          $code .= ($isLast ? 'P' : 'B'); // final devoicing
          $x++;
          break;

        case 'T':
          if ($next === 'H') { // "th" -> /t/
            $code .= 'T';
            $x += 2;
            break;
          }

          $code .= 'T';
          $x++;
          break;

        case 'Q':
          if ($next === 'U') { // "qu" -> /kʋ/
            $code .= 'KW';
            $x += 2;
            break;
          }

          $code .= 'K';
          $x++;
          break;

        case 'Z': // "s" and "z" are hard to keep apart
          $code .= 'S';
          $x++;
          break;

        case 'V': // "f" and "v" are hard to keep apart
          $code .= 'F';
          $x++;
          break;

        case 'X':
          $code .= 'KS';
          $x++;
          break;

        case 'Y': // the older spelling of "ij"
          $code .= 'Y';
          $x++;
          break;

        default:
          $code .= $char;
          $x++;
          break;
      }

    }

    //
    // 3. write a double letter only once
    //

    $codeLength = \strlen($code);
    $result = '';
    $last = '';

    for ($x = 0; $x < $codeLength; $x++) {
      if ($code[$x] === $last) {
        continue;
      }

      $result .= $code[$x];
      $last = $code[$x];
    }

    return $result;
  }
}

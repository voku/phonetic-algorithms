<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * PhoneticPolish-Helper-Class
 *
 * @package voku\helper
 */
final class PhoneticPolish implements PhoneticInterface
{
  /**
   * Phonetic for the polish language.
   *
   * There is no single published standard for polish - so this class is an
   * explicitly documented rule set, not an implementation of a named algorithm.
   * It answers the two things that break a polish name search:
   *
   * 1. the query is typed without diacritics - "Walesa" for "Wałęsa",
   *    "Lodz" for "Łódź". Every diacritic is folded to its plain latin letter,
   *    so both spellings meet in one key.
   * 2. a digraph and a single letter share one sound:
   *    - "ch" and "h"   are /x/
   *    - "cz", "ć", "ci" and "c" are all written as one "C"
   *    - "sz", "ś", "si" and "s" are all written as one "S"
   *    - "rz", "ż", "ź", "zi" and "z" are all written as one "Z"
   *
   * The key alphabet:
   * ===============================================================
   * Key  Sound                    Written in polish as
   * ---  -----------------------  ---------------------------
   * H    /x/                      h, ch
   * C    /t͡s/, /t͡ʂ/, /t͡ɕ/          c, ć, cz, ci (before a vowel)
   * S    /s/, /ʂ/, /ɕ/            s, ś, sz, si (before a vowel)
   * Z    /z/, /ʐ/, /ʑ/            z, ż, ź, rz, zi (before a vowel)
   * V    /v/                      w
   * L    /l/, /w/                 l, ł
   * K    /k/                      k, q
   * KS   /ks/                     x
   * A/E/O/U                       a/ą, e/ę, o/ó, u
   * ---------------------------------------------------------------
   *
   * Known limits of that simplification:
   * - "rz" is always read as one sound, so the rare word where "r" and "z"
   *   really are two sounds ("marznąć") loses its "r".
   * - "ó" is spoken like "u" but is folded to "o", because that is what a
   *   query typed without diacritics contains.
   * - a double letter is written once.
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
    // 1. normalize: lowercase -> ascii (this is what folds the diacritics)
    //               -> letters only -> uppercase
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
      $afterNext = $word[$x + 2] ?? '';

      switch ($char) {

        case 'C':
          if ($next === 'H') { // "ch" -> /x/, the same sound as "h"
            $code .= 'H';
            $x += 2;
            break;
          }

          if ($next === 'Z') { // "cz" -> /t͡ʂ/
            $code .= 'C';
            $x += 2;
            break;
          }

          if ($next === 'I' && self::isVowel($afterNext)) { // "ci" + vowel -> /t͡ɕ/, the "i" only softens
            $code .= 'C';
            $x += 2;
            break;
          }

          $code .= 'C';
          $x++;
          break;

        case 'S':
          if ($next === 'Z') { // "sz" -> /ʂ/
            $code .= 'S';
            $x += 2;
            break;
          }

          if ($next === 'I' && self::isVowel($afterNext)) { // "si" + vowel -> /ɕ/
            $code .= 'S';
            $x += 2;
            break;
          }

          $code .= 'S';
          $x++;
          break;

        case 'R':
          if ($next === 'Z') { // "rz" -> /ʐ/, the same sound as "ż"
            $code .= 'Z';
            $x += 2;
            break;
          }

          $code .= 'R';
          $x++;
          break;

        case 'Z':
          if ($next === 'I' && self::isVowel($afterNext)) { // "zi" + vowel -> /ʑ/
            $code .= 'Z';
            $x += 2;
            break;
          }

          $code .= 'Z';
          $x++;
          break;

        case 'W': // polish "w" is /v/
          $code .= 'V';
          $x++;
          break;

        case 'Q':
          $code .= 'K';
          $x++;
          break;

        case 'X':
          $code .= 'KS';
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

  /**
   * @param string $char
   *
   * @return bool
   */
  private static function isVowel($char): bool
  {
    return $char === 'A'
           ||
           $char === 'E'
           ||
           $char === 'I'
           ||
           $char === 'O'
           ||
           $char === 'U'
           ||
           $char === 'Y';
  }
}

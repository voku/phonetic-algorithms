<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * PhoneticPortuguese-Helper-Class
 *
 * @package voku\helper
 */
final class PhoneticPortuguese implements PhoneticInterface
{
  /**
   * Phonetic for the portuguese language.
   *
   * There is no single published standard for portuguese - so this class is an
   * explicitly documented rule set, not an implementation of a named algorithm.
   *
   * The key alphabet:
   * ==================================================================
   * Key  Sound          Written in portuguese as
   * ---  -------------  ------------------------------------------
   * S    /s/            s, ss, ç, c (before e,i), z (at the end)
   * Z    /z/            z, s (between two vowels)
   * K    /k/            c (before a,o,u or a consonant), qu, k
   * X    /ʃ/            ch, x
   * J    /ʒ/            j, g (before e,i)
   * G    /g/            g (before a,o,u), gu (before e,i)
   * L    /l/, /ʎ/       l, lh
   * N    /n/, /ɲ/       n, nh, final m
   * R    /ʁ/, /ɾ/       r, rr
   * -    (silent)       h
   * ------------------------------------------------------------------
   *
   * Additional rules:
   * - every accent is folded away ("José" === "Jose", "São" === "Sao"),
   *   because a portuguese query is usually typed without one.
   * - "ç" is the exception: it is rewritten to "ss" *before* the fold. Folding
   *   it to "c" would turn its /s/ into a /k/, and a single "s" would be read
   *   as the /z/ of "casa" - the doubled form keeps it a real /s/.
   * - a final "m" is written as "N", so that "bom" and "bon" share one key.
   * - a double letter is written once, so "ss" and "rr" need no rule of their
   *   own.
   * - "x" is treated as /ʃ/. That is the common case ("Xavier", "peixe"), but
   *   portuguese also uses it for /ks/ ("fixo"), which this rule set does not
   *   detect.
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
    // 1. lowercase
    //

    $word = UTF8::strtolower($word);

    //
    // 2. keep the sound that "to_ascii" would throw away
    //

    $word = \str_replace('ç', 'ss', $word);

    //
    // 3. normalize: ascii -> letters only -> uppercase
    //

    $word = UTF8::to_ascii($word);
    $word = (string)\preg_replace('/[^a-z]/', '', $word);

    if ($word === '') {
      return '';
    }

    $word = \strtoupper($word);

    //
    // 4. calculate the key
    //

    $wordLength = \strlen($word);
    $code = '';

    for ($x = 0; $x < $wordLength;) {
      $char = $word[$x];
      $next = $word[$x + 1] ?? '';
      $afterNext = $word[$x + 2] ?? '';
      $isLast = ($x === $wordLength - 1);

      switch ($char) {

        case 'C':
          if ($next === 'H') { // "ch" -> /ʃ/
            $code .= 'X';
            $x += 2;
            break;
          }

          if (self::isFrontVowel($next)) { // "ce", "ci" -> /s/
            $code .= 'S';
            $x++;
            break;
          }

          $code .= 'K';
          $x++;
          break;

        case 'G':
          if ($next === 'U' && self::isFrontVowel($afterNext)) { // "gue", "gui" -> /g/
            $code .= 'G';
            $x += 2;
            break;
          }

          if (self::isFrontVowel($next)) { // "ge", "gi" -> /ʒ/
            $code .= 'J';
            $x++;
            break;
          }

          $code .= 'G';
          $x++;
          break;

        case 'L':
          if ($next === 'H') { // "lh" -> /ʎ/
            $code .= 'L';
            $x += 2;
            break;
          }

          $code .= 'L';
          $x++;
          break;

        case 'N':
          if ($next === 'H') { // "nh" -> /ɲ/
            $code .= 'N';
            $x += 2;
            break;
          }

          $code .= 'N';
          $x++;
          break;

        case 'M':
          $code .= ($isLast ? 'N' : 'M'); // a final "m" only nasalizes the vowel
          $x++;
          break;

        case 'Q':
          $code .= 'K'; // only ever written as "qu"
          $x += ($next === 'U' ? 2 : 1);
          break;

        case 'S':
          // "s" between two vowels is /z/ ("casa"), everywhere else /s/
          if (
              $x > 0
              &&
              self::isVowel($word[$x - 1])
              &&
              self::isVowel($next)
          ) {
            $code .= 'Z';
            $x++;
            break;
          }

          $code .= 'S';
          $x++;
          break;

        case 'Z':
          $code .= ($isLast ? 'S' : 'Z'); // a final "z" is /s/
          $x++;
          break;

        case 'X':
          $code .= 'X'; // /ʃ/
          $x++;
          break;

        case 'H': // silent, "ch", "lh" and "nh" are handled above
          $x++;
          break;

        case 'W':
          $code .= 'V';
          $x++;
          break;

        case 'Y':
          $code .= 'I';
          $x++;
          break;

        default:
          $code .= $char;
          $x++;
          break;
      }

    }

    //
    // 5. write a double letter only once
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
   * "e" and "i" turn "c" and "g" into a different sound.
   *
   * @param string $char
   *
   * @return bool
   */
  private static function isFrontVowel($char): bool
  {
    return $char === 'E' || $char === 'I';
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
           $char === 'U';
  }
}

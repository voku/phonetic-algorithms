<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * PhoneticSpanish-Helper-Class
 *
 * @package voku\helper
 */
final class PhoneticSpanish implements PhoneticInterface
{
  /**
   * Phonetic for the spanish language.
   *
   * There is no single published standard for spanish - so this class is an
   * explicitly documented rule set, not an implementation of a named algorithm.
   * It folds exactly the sound pairs that spanish spelling keeps apart while
   * the pronunciation does not, because those are the spellings people
   * actually get wrong:
   *
   * - "b" and "v" are the same sound  -> "vaca" === "baca"
   * - "seseo": "c" (before e,i), "z" and "s" are the same sound in most of the
   *            spanish speaking world -> "casa" === "caza"
   * - "yeísmo": "ll" and "y" are the same sound -> "llave" === "yave"
   * - "h" is silent                   -> "hola" === "ola"
   * - "g" (before e,i) and "j" are the same sound -> "gente" === "jente"
   *
   * The key alphabet:
   * =============================================================
   * Key  Sound      Written in spanish as
   * ---  ---------  -----------------------------------------
   * B    /b/        b, v, w
   * K    /k/        c (before a,o,u or a consonant), qu, k
   * S    /s/, /θ/   s, z, c (before e,i)
   * C    /t͡ʃ/       ch
   * G    /g/        g (before a,o,u or a consonant), gu (before e,i)
   * GU   /gw/       gü (before e,i)
   * J    /x/        j, g (before e,i)
   * Y    /ʝ/        ll, y
   * KS   /ks/       x
   * N    /n/        n
   * NI   /ɲ/        ñ
   * -    (silent)   h
   * -------------------------------------------------------------
   *
   * Additional rules:
   * - "ñ" is rewritten as "ni" before anything else, so that "año" and "ano"
   *   stay two different words.
   * - "gü" is rewritten as "gw", so that "vergüenza" keeps the /w/ that a plain
   *   "gue" does not have.
   * - every accent is folded away, because a spanish accent marks the stress
   *   and not the sound.
   * - a double letter is written once at the end, so "carro" and "caro" share
   *   one key. That is a deliberate simplification: the rolled "rr" is a real
   *   phoneme, but writing it wrong is the far more common case in a search
   *   query.
   * - "x" is always /ks/. In mexican place and family names it is really /x/
   *   ("México"), which this rule set does not detect.
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
    // 2. keep the two sounds that "to_ascii" would throw away
    //

    $word = \str_replace(['gü', 'ñ'], ['gw', 'ni'], $word);

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

      switch ($char) {

        case 'C':
          if ($next === 'H') { // "ch" -> /t͡ʃ/
            $code .= 'C';
            $x += 2;
            break;
          }

          if (self::isFrontVowel($next)) { // "seseo": "ce", "ci" -> /s/
            $code .= 'S';
            $x++;
            break;
          }

          $code .= 'K';
          $x++;
          break;

        case 'G':
          if ($next === 'W') { // "gü" + e,i -> /gw/
            $code .= 'GU';
            $x += 2;
            break;
          }

          if ($next === 'U' && self::isFrontVowel($afterNext)) { // "gue", "gui" -> /g/, the "u" is silent
            $code .= 'G';
            $x += 2;
            break;
          }

          if (self::isFrontVowel($next)) { // "ge", "gi" -> /x/
            $code .= 'J';
            $x++;
            break;
          }

          $code .= 'G';
          $x++;
          break;

        case 'Q':
          $code .= 'K'; // only ever written as "qu", the "u" is silent
          $x += ($next === 'U' ? 2 : 1);
          break;

        case 'L':
          if ($next === 'L') { // "yeísmo": "ll" -> /ʝ/
            $code .= 'Y';
            $x += 2;
            break;
          }

          $code .= 'L';
          $x++;
          break;

        case 'Z': // "seseo"
          $code .= 'S';
          $x++;
          break;

        case 'V': // "b" and "v" are one sound
        case 'W':
          $code .= 'B';
          $x++;
          break;

        case 'X':
          $code .= 'KS';
          $x++;
          break;

        case 'H': // always silent in spanish
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
}

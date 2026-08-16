<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * PhoneticItalian-Helper-Class
 *
 * @package voku\helper
 */
final class PhoneticItalian implements PhoneticInterface
{
  /**
   * Phonetic for the italian language.
   *
   * Italian orthography is almost phonemic: nearly every letter has exactly one
   * sound, and the few ambiguous letters are resolved by the letter that
   * follows them. Because of that this algorithm does not fold the word into
   * digits (like "Kölner Phonetik" does for german) but into a readable
   * upper-case key, in the same spirit as the "SOUNDEX FR"-algorithm.
   *
   * The key alphabet:
   * ============================================================
   * Key  Sound                Written in italian as
   * ---  -------------------  ---------------------------------
   * K    /k/                  c (before a,o,u or a consonant),
   *                           ch, q, k
   * C    /t͡ʃ/                 c (before e,i), ci (before a,o,u)
   * G    /g/                  g (before a,o,u or a consonant), gh
   * J    /d͡ʒ/                 g (before e,i), gi (before a,o,u)
   * X    /ʃ/                  sc (before e,i), sci (before a,o,u)
   * N    /n/ and /ɲ/          n, gn
   * L    /l/ and /ʎ/          l, gli
   * S    /s/ and /z/          s
   * Z    /t͡s/ and /d͡z/        z
   * V    /v/                  v, w
   * KS   /ks/                 x
   * I    /i/                  i, y
   * -    (silent)             h
   * ------------------------------------------------------------
   *
   * Additional rules:
   * - a double letter ("gemination") is written once, so that a missing or an
   *   additional double letter - the most common italian misspelling - does not
   *   change the key: "pizza" -> "PIZA", "sucesso" === "successo" -> "SUCESO".
   * - every accent is folded away ("città" -> "CITA"), because an italian
   *   accent marks the stress and not the sound.
   * - every char that is not a letter is removed.
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
    // 2. write every double letter only once, so that the most common italian
    //    misspelling does not change the key
    //

    $word = self::removeDoubleLetters($word);

    //
    // 3. calculate the key
    //

    $wordLength = \strlen($word);
    $code = '';

    for ($x = 0; $x < $wordLength;) {
      $char = $word[$x];
      $next = $word[$x + 1] ?? '';
      $afterNext = $word[$x + 2] ?? '';

      switch ($char) {

        case 'C':
          if ($next === 'H') { // "ch" -> /k/
            $code .= 'K';
            $x += 2;
            break;
          }

          if ($next === 'I' && self::isBackVowel($afterNext)) { // "ci" + a,o,u -> /t͡ʃ/
            $code .= 'C';
            $x += 2;
            break;
          }

          if (self::isFrontVowel($next)) { // "c" + e,i -> /t͡ʃ/
            $code .= 'C';
            $x++;
            break;
          }

          $code .= 'K';
          $x++;
          break;

        case 'G':
          if ($next === 'H') { // "gh" -> /g/
            $code .= 'G';
            $x += 2;
            break;
          }

          if ($next === 'N') { // "gn" -> /ɲ/
            $code .= 'N';
            $x += 2;
            break;
          }

          if ($next === 'L' && $afterNext === 'I') { // "gli" -> /ʎ/
            $code .= 'L';
            $x += 3;
            break;
          }

          if ($next === 'I' && self::isBackVowel($afterNext)) { // "gi" + a,o,u -> /d͡ʒ/
            $code .= 'J';
            $x += 2;
            break;
          }

          if (self::isFrontVowel($next)) { // "g" + e,i -> /d͡ʒ/
            $code .= 'J';
            $x++;
            break;
          }

          $code .= 'G';
          $x++;
          break;

        case 'S':
          if ($next === 'C') {
            if ($afterNext === 'I' && self::isBackVowel($word[$x + 3] ?? '')) { // "sci" + a,o,u -> /ʃ/
              $code .= 'X';
              $x += 3;
              break;
            }

            if (self::isFrontVowel($afterNext)) { // "sc" + e,i -> /ʃ/
              $code .= 'X';
              $x += 2;
              break;
            }

            $code .= 'SK'; // "sc" + a,o,u or a consonant -> /sk/
            $x += 2;
            break;
          }

          $code .= 'S';
          $x++;
          break;

        case 'H': // always silent in italian
          $x++;
          break;

        case 'Q': // only ever written as "qu" -> /kw/
          $code .= 'K';
          $x++;
          break;

        case 'W': // only in loanwords
          $code .= 'V';
          $x++;
          break;

        case 'X':
          $code .= 'KS';
          $x++;
          break;

        case 'Y': // only in loanwords
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
    // 4. the mapping itself can create a new double letter, e.g. "acqua" -> "AKKUA"
    //

    return self::removeDoubleLetters($code);
  }

  /**
   * @param string $word
   *
   * @return string
   */
  private static function removeDoubleLetters($word): string
  {
    $length = \strlen($word);
    $result = '';
    $last = '';

    for ($x = 0; $x < $length; $x++) {
      if ($word[$x] === $last) {
        continue;
      }

      $result .= $word[$x];
      $last = $word[$x];
    }

    return $result;
  }

  /**
   * "e" and "i" turn "c", "g" and "sc" into a soft sound.
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
   * "a", "o" and "u" keep the hard sound, so a soft sound needs a written "i".
   *
   * @param string $char
   *
   * @return bool
   */
  private static function isBackVowel($char): bool
  {
    return $char === 'A' || $char === 'O' || $char === 'U';
  }
}

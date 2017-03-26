<?php

namespace voku\helper;

/**
 * PhoneticAlgorithms-Helper-Class
 *
 * @package voku\helper
 */
final class PhoneticAlgorithms
{
  /**
   * Phonetic for the english language via "metaphone"-algorithm.
   *
   * @param string $sentence
   *
   * @return array
   */
  public static function english_phonetic_sentence($sentence)
  {
    $words = UTF8::str_to_words($sentence);

    $return = array();
    foreach ($words as $word) {
      $code = self::english_phonetic_word($word);

      if ($code !== '') {
        $return[] = $code;
      }
    }

    return $return;
  }

  /**
   * Phonetic for the english language via "metaphone"-algorithm.
   *
   * "The algorithm was published in 1995 by Hans Lawrence Philips." -->
   * - http://php.net/manual/en/function.metaphone.php
   *
   *
   * @param $word
   *
   * @return string
   */
  public static function english_phonetic_word($word)
  {
    return metaphone(UTF8::to_ascii($word));
  }

  /**
   * Phonetic for the german language via "Kölner Phonetik"-algorithm.
   *
   * @param string $sentence
   *
   * @return array
   */
  public static function german_phonetic_sentence($sentence)
  {
    $words = UTF8::str_to_words($sentence);

    $return = array();
    foreach ($words as $word) {
      $code = self::german_phonetic_word($word);

      if ($code !== '') {
        $return[] = $code;
      }
    }

    return $return;
  }

  /**
   * Phonetic for the german language via "Kölner Phonetik"-algorithm.
   *
   * "The algorithm was published in 1969 by Hans Joachim Postel." -->
   * - https://en.wikipedia.org/wiki/Cologne_phonetics
   * - http://www.uni-koeln.de/phil-fak/phonetik/Lehre/MA-Arbeiten/magister_wilz.pdf
   *
   * Task:
   * ============================================
   * Letter         Contect                  Code
   * -------------  -----------------------  ----
   * A,E,I,J,O,U,Y                            0
   * H                                        -
   * B                                        1
   * P              not before H              1
   * D,T            not before C,S,Z          2
   * F,V,W                                    3
   * P              before H                  3
   * G,K,Q                                    4
   * C              at the begin
   *                before A,H,K,L,O,Q,R,U,X  4
   * C              before A,H,K,O,Q,U,X
   *                exception after S,Z       4
   * X              not after C,K,Q          48
   * L                                        5
   * M,N                                      6
   * R                                        7
   * S,Z                                      8
   * C              after R,S,Z               8
   * C              at the begin,
   *                exception before
   *                A,H,K,L,O,Q,R,U,X         8
   * C              not before A,H,K,O,Q,U,X  8
   * D,T            before C,S,Z              8
   * X              after C,K,Q               8
   * --------------------------------------------
   *
   * @param string $word
   *
   * @return string
   */
  public static function german_phonetic_word($word)
  {
    // init
    $code = '';
    $word = (string)$word;

    if (!isset($word[0])) {
      return '';
    }

    //
    // 1. convert into lowercase
    //

    $word = UTF8::strtolower($word);

    //
    // 2. substitute some chars
    //

    $substitutionSearch = array(
        'ä',
        'ö',
        'ü',
        'ß',
        'ph',
    );
    $substitutionReplace = array(
        'a',
        'o',
        'u',
        'ss',
        'f',
    );
    $word = str_replace($substitutionSearch, $substitutionReplace, $word);

    //
    // 3. convert into ascii
    //

    $word = UTF8::to_ascii($word);

    //
    // 4. remove every char that are noz a letter
    //

    $word = preg_replace('/[^a-zA-Z]/', '', $word);

    //
    // 5. calculate the code
    //

    $wordlen = strlen($word);
    $char = str_split($word);

    if ($char[0] == 'c') {  // at the begin, exception before
      if ($wordlen === 1) {
        $code = '4';
        $x = 1;
      } else {
        switch ($char[1]) { // before a,h,k,l,o,q,r,u,x
          case 'a':
          case 'h':
          case 'k':
          case 'l':
          case 'o':
          case 'q':
          case 'r':
          case 'u':
          case 'x':
            $code = '4';
            break;
          default:
            $code = '8';
            break;
        }
        $x = 1;
      }
    } else {
      $x = 0;
    }

    for (; $x < $wordlen; $x++) {

      switch ($char[$x]) {
        // A, E, I, J, O, U, Y === 0
        case 'a':
        case 'e':
        case 'i':
        case 'j':
        case 'o':
        case 'u':
        case 'y':
          $code .= '0';
          break;
        case 'b':
          $code .= '1';
          break;
        case 'p':
          $code .= '1';
          break;
        case 'd':
        case 't':
          if ($x + 1 < $wordlen) {
            switch ($char[$x + 1]) {
              case 'c':
              case 's':
              case 'z':
                $code .= '8';
                break;
              default:
                $code .= '2';
                break;
            }
          } else {
            $code .= '2';
          }
          break;
        case 'f':
        case 'v':
        case 'w':
          $code .= '3';
          break;
        case 'g':
        case 'k':
        case 'q':
          $code .= '4';
          break;
        case 'c':
          if ($x + 1 < $wordlen) {
            switch ($char[$x + 1]) {
              case 'a':
              case 'h':
              case 'k':
              case 'o':
              case 'q':
              case 'u':
              case 'x':
                switch ($char[$x - 1]) {
                  case 'r':
                  case 's':
                  case 'z':
                    $code .= '8';
                    break;
                  default:
                    $code .= '4';
                }
                break;
              default:
                $code .= '8';
                break;
            }
          } else {
            $code .= '8';
          }
          break;
        case 'x':
          if ($x > 0) {
            switch ($char[$x - 1]) {
              case 'c':
              case 'k':
              case 'q':
                $code .= '8';
                break;
              default:
                $code .= '48';
                break;
            }
          } else {
            $code .= '48';
          }
          break;
        case 'l':
          $code .= '5';
          break;
        case 'm':
        case 'n':
          $code .= '6';
          break;
        case 'r':
          $code .= '7';
          break;
        case 's':
        case 'z':
          $code .= '8';
          break;
      }

    }

    //
    // 6. remove duplicate codes
    //       && remove all '0'-codes, exception at the begin
    //

    $codelen = strlen($code);
    $num = str_split($code);
    $lastCode = '';
    $phoneticCode = '';

    /** @noinspection ForeachInvariantsInspection */
    for ($x = 0; $x < $codelen; $x++) {
      $currentCode = $num[$x];

      if ($lastCode === $currentCode) {
        continue;
      }

      if (
          $x !== 0 // keep the first '0'
          &&
          $currentCode === '0'
      ) {
        continue;
      }

      $phoneticCode .= $currentCode;

      $lastCode = $currentCode;
    }

    //
    // 7. fallback to a empty string
    //

    if ($phoneticCode === null) {
      return '';
    }

    return $phoneticCode;
  }
}

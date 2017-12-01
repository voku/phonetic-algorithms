<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * PhoneticEnglish-Helper-Class
 *
 * @package voku\helper
 */
final class PhoneticEnglish implements PhoneticInterface
{
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
  public function phonetic_word($word): string
  {
    return \metaphone(UTF8::to_ascii($word));
  }
}

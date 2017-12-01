<?php

namespace voku\helper;

/**
 * PhoneticInterface-Interface
 *
 * @package voku\helper
 */
interface PhoneticInterface
{
  /**
   * @param string $word
   *
   * @return string
   */
  public function phonetic_word($word): string;
}

<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * Phonetic-Helper-Class
 *
 * @package voku\helper
 */
final class Phonetic
{
  /**
   * @var array
   */
  private $availableLanguages = array(
      'de' => 'German',
      'en' => 'English',
      'fr' => 'French',
  );

  /**
   * @var PhoneticInterface
   */
  private $phonetic;

  /**
   * @var string
   */
  private $language;

  /**
   * @var StopWords
   */
  private $stopWords;

  /**
   * PhoneticAlgorithms constructor.
   *
   * @param string $language
   *
   * @throws PhoneticExceptionLanguageNotExists
   * @throws PhoneticExceptionClassNotExists
   */
  public function __construct($language = 'de')
  {
    if (\array_key_exists($language, $this->availableLanguages) === false) {
      throw new PhoneticExceptionLanguageNotExists('language not supported: ' . $language);
    }

    $className = '\\voku\\helper\\Phonetic' . $this->availableLanguages[$language];
    if (\class_exists($className) === false) {
      throw new PhoneticExceptionClassNotExists('phonetic class not found: ' . $className);
    }

    $this->language = $language;
    $this->stopWords = new StopWords();
    $this->phonetic = new $className;
  }

  /**
   * @param string $needle
   * @param array  $haystack
   *
   * @return array
   */
  public function phonetic_matches($needle, array $haystack)
  {
    $needleResult = $this->phonetic_sentence($needle);
    if (\count($needleResult) === 0) {
      return array();
    }

    $isAssoc = true;
    $tmpCounter = 0;
    foreach ($haystack as $keyTmp => $valueTmp) {
      if ($keyTmp !== $tmpCounter) {
        $isAssoc = false;
        break;
      }

      $tmpCounter++;
    }

    $haystackResult = $this->phonetic_sentence($haystack);
    if (\count($haystackResult) === 0) {
      return array();
    }

    $result = array();
    foreach ($haystackResult as $haystackResultKey => $haystackResultWords) {
      foreach ($haystackResultWords as $haystackWord => $haystackCode) {

        foreach ($needleResult as $needleWord => $needleCode) {
          if ($haystackCode === $needleCode) {
            $result[$haystackResultKey][$needleWord] = $haystackWord;
          }
        }

      }
    }

    if (\count($result) > 0) {
      \uasort(
          $result, function ($a, $b) {
            if ($a == $b) {
              return 1;
            }

            return (\count($a) > \count($b)) ? -1 : 1;
          }
      );

      if ($isAssoc) {
        $resultTmp = array();
        foreach ($result as $keyTmp => $resultInner) {
          foreach ($resultInner as $resultInnerKey => $resultInnerValue) {
            $resultTmp[$resultInnerValue] = $resultInnerKey;
          }
        }

        return $resultTmp;
      }
    }

    return $result;
  }

  /**
   * Phonetic for more then one word.
   *
   * @param string|string[] $input
   * @param bool            $useStopWords
   * @param false|int       $skipShortWords
   *
   * @param int             $key
   *
   * @return array <p>key === orig word<br />value === code word</p>
   */
  public function phonetic_sentence($input, $useStopWords = true, $skipShortWords = 2, $key = null)
  {
    // init
    $words = array();
    static $STOP_WORDS_CACHE = array();

    if ($skipShortWords === false) {
      $skipShortWords = null;
    }

    if (\is_array($input) === true) {
      foreach ($input as $inputKey => $inputString) {
        $words[$inputKey] = UTF8::str_to_words($inputString, '', true, $skipShortWords);
      }
    } else {
      $words = UTF8::str_to_words($input, '', true, $skipShortWords);
    }

    if (
        $useStopWords === true
        &&
        !isset($STOP_WORDS_CACHE[$this->language])
    ) {
      try {
        $STOP_WORDS_CACHE[$this->language] = $this->stopWords->getStopWordsFromLanguage($this->language);
      } catch (StopWordsLanguageNotExists $e) {
        $STOP_WORDS_CACHE[$this->language] = array();
      }
    }

    $return = array();
    foreach ($words as $wordKey => $word) {

      if (\is_array($word) === true) {
        foreach ($word as $wordInner) {
          $return = \array_replace_recursive(
              $return,
              $this->phonetic_sentence($wordInner, $useStopWords, $skipShortWords, $key !== null ? $key : $wordKey)
          );
        }

        continue;
      }

      if (
          $useStopWords === true
          &&
          \in_array($word, $STOP_WORDS_CACHE[$this->language], true)
      ) {
        continue;
      }

      $code = $this->phonetic->phonetic_word($word);
      if ($code !== '') {
        if ($key !== null) {
          $return[$key][$word] = $code;
        } else {
          $return[$word] = $code;
        }
      }
    }

    return $return;
  }

  /**
   * Phonetic for one word.
   *
   * @param string $word
   *
   * @return string
   */
  public function phonetic_word($word)
  {
    return $this->phonetic->phonetic_word($word);
  }
}

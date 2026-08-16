<?php

use voku\helper\Phonetic;
use voku\helper\PhoneticPolish;

/**
 * Class PolishPhoneticAlgorithmsTest
 */
class PolishPhoneticAlgorithmsTest extends \PHPUnit\Framework\TestCase
{
  /**
   * One row per rule of the code table in the class docblock.
   *
   * @return array
   */
  public function ruleProvider(): array
  {
    return [
      # "ch" and "h" = /x/ = "H"
      ['chleb', 'HLEB'],
      ['herbata', 'HERBATA'],
      # "c", "ć", "cz" and "ci" + vowel = "C"
      ['co', 'CO'],
      ['czas', 'CAS'],
      ['ćma', 'CMA'],
      ['ciasto', 'CASTO'],
      # "s", "ś", "sz" and "si" + vowel = "S"
      ['sok', 'SOK'],
      ['szkoła', 'SKOLA'],
      ['śnieg', 'SNIEG'],
      ['siano', 'SANO'],
      # "z", "ż", "ź", "rz" and "zi" + vowel = "Z"
      ['zima', 'ZIMA'],
      ['żaba', 'ZABA'],
      ['rzeka', 'ZEKA'],
      ['źle', 'ZLE'],
      # "w" = /v/ = "V"
      ['woda', 'VODA'],
      # "l" and "ł" = "L"
      ['lato', 'LATO'],
      ['łatwo', 'LATVO'],
      # "q" = "K", "x" = "KS"
      ['quiz', 'KUIZ'],
      ['xero', 'KSERO'],
      # the nasal vowels fold to their plain letter, so that a query without
      # diacritics still matches - the nasal /n/ is deliberately not written out
      ['ręka', 'REKA'],
      ['reka', 'REKA'],
      ['mąka', 'MAKA'],
      ['maka', 'MAKA'],
      # "ó" folds to "o", because that is what a query without diacritics has
      ['łódź', 'LODZ'],
      ['lodz', 'LODZ'],
      # a double letter is written once
      ['anna', 'ANA'],
    ];
  }

  /**
   * @param string $word
   * @param string $expected
   *
   * @dataProvider ruleProvider
   */
  public function testRules($word, $expected)
  {
    $phonetic = new PhoneticPolish();
    self::assertSame($expected, $phonetic->phonetic_word($word), 'tested: ' . $word);
  }

  /**
   * @return array
   */
  public function charProvider(): array
  {
    return [
        ['A', 'A'],
        ['B', 'B'],
        ['C', 'C'],
        ['D', 'D'],
        ['E', 'E'],
        ['F', 'F'],
        ['G', 'G'],
        ['H', 'H'],
        ['I', 'I'],
        ['J', 'J'],
        ['K', 'K'],
        ['L', 'L'],
        ['M', 'M'],
        ['N', 'N'],
        ['O', 'O'],
        ['P', 'P'],
        ['Q', 'K'],
        ['R', 'R'],
        ['S', 'S'],
        ['T', 'T'],
        ['U', 'U'],
        ['V', 'V'],
        ['W', 'V'],
        ['X', 'KS'],
        ['Y', 'Y'],
        ['Z', 'Z'],
    ];
  }

  /**
   * @param string $char
   * @param string $expected
   *
   * @dataProvider charProvider
   */
  public function testChars($char, $expected)
  {
    $phonetic = new PhoneticPolish();
    self::assertSame($expected, $phonetic->phonetic_word($char), 'tested: ' . $char);
  }

  /**
   * Acceptance criterion 1: a query typed without diacritics has to find the
   * correctly spelled word.
   */
  public function testDiacriticFreeSpellingMatches()
  {
    $phonetic = new PhoneticPolish();

    $pairs = [
        ['Wałęsa', 'Walesa'],
        ['Łódź', 'Lodz'],
        ['Kraków', 'Krakow'],
        ['Gdańsk', 'Gdansk'],
        ['Wróbel', 'Wrobel'],
        ['Kowalczyk', 'Kowalczyk'],
    ];

    foreach ($pairs as $pair) {
      list($polish, $plain) = $pair;

      self::assertSame(
          $phonetic->phonetic_word($polish),
          $phonetic->phonetic_word($plain),
          'tested: ' . $polish . ' vs. ' . $plain
      );
    }
  }

  /**
   * Acceptance criterion 2: a digraph and the single letter that shares its
   * sound have to meet in one key.
   */
  public function testDigraphAndSingleLetterCollapse()
  {
    $phonetic = new PhoneticPolish();

    $pairs = [
        ['chleb', 'hleb'],       // ch === h
        ['czas', 'cas'],         // cz === c
        ['szkoła', 'skoła'],     // sz === s
        ['rzeka', 'żeka'],       // rz === ż
        ['Grzegorz', 'Gzegoz'],  // both at once
    ];

    foreach ($pairs as $pair) {
      list($left, $right) = $pair;

      self::assertSame(
          $phonetic->phonetic_word($left),
          $phonetic->phonetic_word($right),
          'tested: ' . $left . ' vs. ' . $right
      );
    }
  }

  public function testOutputIsAlwaysUpperCaseAscii()
  {
    $phonetic = new PhoneticPolish();

    foreach (['Łódź', 'Wałęsa', 'Müller', '中文空白', "Ol'ga", '123'] as $word) {
      $code = $phonetic->phonetic_word($word);

      self::assertSame(
          1,
          \preg_match('/^[A-Z]*$/', $code),
          'tested: ' . $word . ' => ' . $code
      );
    }
  }

  public function testIsEmptyString()
  {
    $phonetic = new PhoneticPolish();

    self::assertSame('', $phonetic->phonetic_word(''));
    self::assertSame('', $phonetic->phonetic_word(' '));
    self::assertSame('', $phonetic->phonetic_word("\n"));
    self::assertSame('', $phonetic->phonetic_word('123'));
  }

  public function testPolishPhoneticWord()
  {
    $testArray = [
        'Warszawa'    => 'VARSAVA',
        'Kraków'      => 'KRAKOV',
        'Wrocław'     => 'VROCLAV',
        'Poznań'      => 'POZNAN',
        'Szczecin'    => 'SCECIN',
        'Nowak'       => 'NOVAK',
        'Kowalski'    => 'KOVALSKI',
        'Wiśniewski'  => 'VISNIEVSKI',
        'Grzegorz'    => 'GZEGOZ',
        'Przemyśl'    => 'PZEMYSL',
        'Moelleken'   => 'MOELEKEN',
    ];

    $phonetic = new PhoneticPolish();
    foreach ($testArray as $before => $after) {
      self::assertSame($after, $phonetic->phonetic_word($before), 'tested: ' . $before);
    }
  }

  public function testViaTheLanguageFacade()
  {
    $facade = new Phonetic('pl');
    $direct = new PhoneticPolish();

    foreach (['Wałęsa', 'Grzegorz', 'szkoła', 'Łódź'] as $word) {
      self::assertSame($direct->phonetic_word($word), $facade->phonetic_word($word), 'tested: ' . $word);
    }

    self::assertSame(
        ['Walesa' => 'VALESA', 'Lodz' => 'LODZ'],
        $facade->phonetic_sentence('Walesa Lodz', false, false)
    );
  }
}

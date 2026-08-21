<?php

use voku\helper\Phonetic;
use voku\helper\PhoneticDutch;

/**
 * Class DutchPhoneticAlgorithmsTest
 */
class DutchPhoneticAlgorithmsTest extends \PHPUnit\Framework\TestCase
{
  /**
   * One row per rule of the code table in the class docblock.
   *
   * @return array
   */
  public function ruleProvider(): array
  {
    return [
      # "ij", "ei", "eij" and "y" = /ɛi/ = "Y"
      ['ijs', 'YS'],
      ['wijn', 'WYN'],
      ['eind', 'YNT'],
      ['meijer', 'MYER'],
      ['meier', 'MYER'],
      ['meyer', 'MYER'],
      # "oe" = /u/ = "U"
      ['boek', 'BUK'],
      ['doen', 'DUN'],
      # "ou" and "au" = /ʌu/ = "AU"
      ['koud', 'KAUT'],
      ['blauw', 'BLAUW'],
      ['vrouw', 'FRAUW'],
      # "g" and "ch" = /x/ = "G"
      ['goed', 'GUT'],
      ['gogh', 'GOG'],
      ['lachen', 'LAGEN'],
      ['nacht', 'NAGT'],
      # "sch" needs no rule of its own
      ['school', 'SGOL'],
      ['schouten', 'SGAUTEN'],
      # "s" and "z" = "S"
      ['zon', 'SON'],
      ['son', 'SON'],
      # "f" and "v" = "F"
      ['vis', 'FIS'],
      ['fis', 'FIS'],
      # "k", "c" before a,o,u or a consonant, "ck" and "qu"
      ['kat', 'KAT'],
      ['cola', 'KOLA'],
      ['bakker', 'BAKER'],
      ['jonckheer', 'JONKHER'], // the "h" is a real sound in dutch, only "gh" is silent
      ['quota', 'KWOTA'],
      # "c" before e,i,y = "S" (loanwords)
      ['centrum', 'SENTRUM'],
      # final devoicing
      ['hond', 'HONT'],
      ['hont', 'HONT'],
      ['web', 'WEP'],
      ['wordt', 'WORT'],
      # "th" = "T"
      ['thuis', 'TUIS'],
      # "x" = "KS"
      ['taxi', 'TAKSI'],
      # a long vowel is written once
      ['groot', 'GROT'],
      ['maan', 'MAN'],
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
    $phonetic = new PhoneticDutch();
    self::assertSame($expected, $phonetic->phonetic_word($word), 'tested: ' . $word);
  }

  /**
   * @return array
   */
  public function charProvider(): array
  {
    return [
        ['A', 'A'],
        ['B', 'P'],
        ['C', 'K'],
        ['D', 'T'],
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
        ['V', 'F'],
        ['W', 'W'],
        ['X', 'KS'],
        ['Y', 'Y'],
        ['Z', 'S'],
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
    $phonetic = new PhoneticDutch();
    self::assertSame($expected, $phonetic->phonetic_word($char), 'tested: ' . $char);
  }

  /**
   * The dutch name variants this class exists for.
   */
  public function testNameVariantsCollapse()
  {
    $phonetic = new PhoneticDutch();

    $groups = [
        ['Meijer', 'Meier', 'Meyer'],
        ['Jansen', 'Janssen'],
        ['Vries', 'Fries'],
        ['Nijhof', 'Nijhoff', 'Nyhof'],
        ['Bakker', 'Baker'],
        ['Schouten', 'Sgouten'],
    ];

    foreach ($groups as $group) {
      $expected = $phonetic->phonetic_word($group[0]);

      foreach ($group as $variant) {
        self::assertSame($expected, $phonetic->phonetic_word($variant), 'tested: ' . $variant);
      }
    }
  }

  public function testOutputIsAlwaysUpperCaseAscii()
  {
    $phonetic = new PhoneticDutch();

    foreach (['IJsselmeer', 'Müller', 'Łódź', '中文空白', "van 't Hof", '123'] as $word) {
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
    $phonetic = new PhoneticDutch();

    self::assertSame('', $phonetic->phonetic_word(''));
    self::assertSame('', $phonetic->phonetic_word(' '));
    self::assertSame('', $phonetic->phonetic_word("\n"));
    self::assertSame('', $phonetic->phonetic_word('123'));
  }

  public function testDutchPhoneticWord()
  {
    $testArray = [
        'Amsterdam'  => 'AMSTERDAM',
        'Rotterdam'  => 'ROTERDAM',
        'Utrecht'    => 'UTREGT',
        'Groningen'  => 'GRONINGEN',
        'IJsselmeer' => 'YSELMER',
        'Gogh'       => 'GOG',
        'Rembrandt'  => 'REMBRANT',
        'Vermeer'    => 'FERMER',
        'Hendrik'    => 'HENDRIK',
        'Willem'     => 'WILEM',
        'Moelleken'  => 'MULEKEN',
    ];

    $phonetic = new PhoneticDutch();
    foreach ($testArray as $before => $after) {
      self::assertSame($after, $phonetic->phonetic_word($before), 'tested: ' . $before);
    }
  }

  public function testViaTheLanguageFacade()
  {
    $facade = new Phonetic('nl');
    $direct = new PhoneticDutch();

    foreach (['Meijer', 'Janssen', 'schouten', 'hond'] as $word) {
      self::assertSame($direct->phonetic_word($word), $facade->phonetic_word($word), 'tested: ' . $word);
    }

    self::assertSame(
        ['Meijer' => 'MYER', 'Janssen' => 'JANSEN'],
        $facade->phonetic_sentence('Meijer Janssen', false, false)
    );
  }
}

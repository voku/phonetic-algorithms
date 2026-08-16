<?php

use voku\helper\Phonetic;
use voku\helper\PhoneticPortuguese;

/**
 * Class PortuguesePhoneticAlgorithmsTest
 */
class PortuguesePhoneticAlgorithmsTest extends \PHPUnit\Framework\TestCase
{
  /**
   * One row per rule of the code table in the class docblock.
   *
   * @return array
   */
  public function ruleProvider(): array
  {
    return [
      # "c" before a,o,u or a consonant, "qu" and "k" = /k/ = "K"
      ['casa', 'KAZA'],
      ['cor', 'KOR'],
      ['claro', 'KLARO'],
      ['quero', 'KERO'],
      # "c" before e,i and "ç" = /s/ = "S"
      ['cedo', 'SEDO'],
      ['cinco', 'SINKO'],
      ['moça', 'MOSA'],
      ['açúcar', 'ASUKAR'],
      # "s" between two vowels = /z/ = "Z"
      ['casa', 'KAZA'],
      ['coisa', 'KOIZA'],
      # "ss" stays /s/
      ['massa', 'MASA'],
      # "ch" and "x" = /ʃ/ = "X"
      ['chave', 'XAVE'],
      ['peixe', 'PEIXE'],
      ['Xavier', 'XAVIER'],
      # "j" and "g" before e,i = /ʒ/ = "J"
      ['janela', 'JANELA'],
      ['gente', 'JENTE'],
      ['girar', 'JIRAR'],
      # "g" before a,o,u and "gu" before e,i = /g/ = "G"
      ['gato', 'GATO'],
      ['guerra', 'GERA'],
      # "lh" and "nh"
      ['filho', 'FILO'],
      ['vinho', 'VINO'],
      # "h" is silent
      ['hoje', 'OJE'],
      ['homem', 'OMEN'],
      # a final "m" is a nasal "n"
      ['bom', 'BON'],
      ['bon', 'BON'],
      # a final "z" is /s/
      ['paz', 'PAS'],
      ['luz', 'LUS'],
      # accents are folded away
      ['jose', 'JOZE'],
      ['josé', 'JOZE'],
      ['sao', 'SAO'],
      ['são', 'SAO'],
      ['avo', 'AVO'],
      ['avô', 'AVO'],
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
    $phonetic = new PhoneticPortuguese();
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
        ['C', 'K'],
        ['D', 'D'],
        ['E', 'E'],
        ['F', 'F'],
        ['G', 'G'],
        ['H', ''],
        ['I', 'I'],
        ['J', 'J'],
        ['K', 'K'],
        ['L', 'L'],
        ['M', 'N'],
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
        ['X', 'X'],
        ['Y', 'I'],
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
    $phonetic = new PhoneticPortuguese();
    self::assertSame($expected, $phonetic->phonetic_word($char), 'tested: ' . $char);
  }

  /**
   * Acceptance criterion: an accent is folded, but "ç" is not - otherwise its
   * /s/ would become the /k/ of a plain "c".
   */
  public function testCedillaSurvivesTheAccentFold()
  {
    $phonetic = new PhoneticPortuguese();

    self::assertSame('MOSA', $phonetic->phonetic_word('moça'));
    self::assertSame('MOKA', $phonetic->phonetic_word('moca'));
    self::assertNotSame(
        $phonetic->phonetic_word('moca'),
        $phonetic->phonetic_word('moça')
    );

    // ... while a real accent changes nothing
    self::assertSame(
        $phonetic->phonetic_word('avo'),
        $phonetic->phonetic_word('avô')
    );
  }

  public function testOutputIsAlwaysUpperCaseAscii()
  {
    $phonetic = new PhoneticPortuguese();

    foreach (['São Paulo', 'açúcar', 'Müller', 'Łódź', '中文空白', '123'] as $word) {
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
    $phonetic = new PhoneticPortuguese();

    self::assertSame('', $phonetic->phonetic_word(''));
    self::assertSame('', $phonetic->phonetic_word(' '));
    self::assertSame('', $phonetic->phonetic_word("\n"));
    self::assertSame('', $phonetic->phonetic_word('123'));
  }

  public function testPortuguesePhoneticWord()
  {
    $testArray = [
        // only an intervocalic "s" is /z/, so the "s" before "b" stays "S"
        'Lisboa'    => 'LISBOA',
        'Porto'     => 'PORTO',
        'Coimbra'   => 'KOIMBRA',
        'Braga'     => 'BRAGA',
        'Silva'     => 'SILVA',
        'Santos'    => 'SANTOS',
        'Oliveira'  => 'OLIVEIRA',
        'Gonçalves' => 'GONSALVES',
        'Rodrigues' => 'RODRIGES',
        'Carvalho'  => 'KARVALO',
        'Moelleken' => 'MOELEKEN',
    ];

    $phonetic = new PhoneticPortuguese();
    foreach ($testArray as $before => $after) {
      self::assertSame($after, $phonetic->phonetic_word($before), 'tested: ' . $before);
    }
  }

  public function testViaTheLanguageFacade()
  {
    $facade = new Phonetic('pt');
    $direct = new PhoneticPortuguese();

    foreach (['Gonçalves', 'Carvalho', 'chave', 'bom'] as $word) {
      self::assertSame($direct->phonetic_word($word), $facade->phonetic_word($word), 'tested: ' . $word);
    }

    self::assertSame(
        ['chave' => 'XAVE', 'filho' => 'FILO'],
        $facade->phonetic_sentence('chave filho', false, false)
    );
  }
}

<?php

use voku\helper\Phonetic;
use voku\helper\PhoneticSpanish;

/**
 * Class SpanishPhoneticAlgorithmsTest
 */
class SpanishPhoneticAlgorithmsTest extends \PHPUnit\Framework\TestCase
{
  /**
   * One row per rule of the code table in the class docblock.
   *
   * @return array
   */
  public function ruleProvider(): array
  {
    return [
      # "b", "v" and "w" = /b/ = "B"
      ['boca', 'BOKA'],
      ['vaca', 'BAKA'],
      ['whisky', 'BISKY'],
      # "c" before a,o,u or a consonant = /k/ = "K"
      ['casa', 'KASA'],
      ['cosa', 'KOSA'],
      ['cuna', 'KUNA'],
      ['clase', 'KLASE'],
      # "qu" and "k" = /k/ = "K"
      ['queso', 'KESO'],
      ['quito', 'KITO'],
      ['kilo', 'KILO'],
      # "seseo": "c" before e,i and "z" and "s" = /s/ = "S"
      ['cero', 'SERO'],
      ['cielo', 'SIELO'],
      ['zapato', 'SAPATO'],
      ['sopa', 'SOPA'],
      # "ch" = /t͡ʃ/ = "C"
      ['chico', 'CIKO'],
      ['mucho', 'MUCO'],
      # "g" before a,o,u or a consonant = /g/ = "G"
      ['gato', 'GATO'],
      ['goma', 'GOMA'],
      ['grande', 'GRANDE'],
      # "gu" before e,i = /g/ = "G", the "u" is silent
      ['guerra', 'GERA'],
      ['guitarra', 'GITARA'],
      # "gü" before e,i = /gw/ = "GU"
      ['vergüenza', 'BERGUENSA'],
      ['pingüino', 'PINGUINO'],
      # "g" before e,i and "j" = /x/ = "J"
      ['gente', 'JENTE'],
      ['girar', 'JIRAR'],
      ['jefe', 'JEFE'],
      # "yeísmo": "ll" and "y" = /ʝ/ = "Y"
      ['llave', 'YABE'],
      ['yema', 'YEMA'],
      # "x" = /ks/ = "KS"
      ['examen', 'EKSAMEN'],
      # "h" is silent
      ['hola', 'OLA'],
      ['hueso', 'UESO'],
      # "ñ" is kept apart from "n"
      ['año', 'ANIO'],
      ['ano', 'ANO'],
      ['españa', 'ESPANIA'],
      # accents mark the stress, not the sound
      ['jose', 'JOSE'],
      ['josé', 'JOSE'],
      ['garcia', 'GARSIA'],
      ['garcía', 'GARSIA'],
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
    $phonetic = new PhoneticSpanish();
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
        ['M', 'M'],
        ['N', 'N'],
        ['O', 'O'],
        ['P', 'P'],
        ['Q', 'K'],
        ['R', 'R'],
        ['S', 'S'],
        ['T', 'T'],
        ['U', 'U'],
        ['V', 'B'],
        ['W', 'B'],
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
    $phonetic = new PhoneticSpanish();
    self::assertSame($expected, $phonetic->phonetic_word($char), 'tested: ' . $char);
  }

  /**
   * The whole point of the class: spellings that sound the same share one key.
   */
  public function testHomophoneSpellingsCollapse()
  {
    $phonetic = new PhoneticSpanish();

    $pairs = [
        ['vaca', 'baca'],       // b === v
        ['casa', 'caza'],       // seseo
        ['cocer', 'coser'],     // seseo
        ['llave', 'yave'],      // yeísmo
        ['hola', 'ola'],        // silent h
        ['gente', 'jente'],     // g before e === j
        ['carro', 'caro'],      // collapsed double letter
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

  /**
   * "ñ" is a sound of its own and must not fall back to "n".
   */
  public function testTildeIsNotFoldedAwayLikeAnAccent()
  {
    $phonetic = new PhoneticSpanish();

    foreach ([['año', 'ano'], ['cañón', 'canon'], ['sueño', 'sueno']] as $pair) {
      list($tilde, $plain) = $pair;

      self::assertNotSame(
          $phonetic->phonetic_word($plain),
          $phonetic->phonetic_word($tilde),
          'tested: ' . $tilde . ' vs. ' . $plain
      );
    }
  }

  public function testOutputIsAlwaysUpperCaseAscii()
  {
    $phonetic = new PhoneticSpanish();

    foreach (['España', 'vergüenza', 'Müller', 'Łódź', '中文空白', '123'] as $word) {
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
    $phonetic = new PhoneticSpanish();

    self::assertSame('', $phonetic->phonetic_word(''));
    self::assertSame('', $phonetic->phonetic_word(' '));
    self::assertSame('', $phonetic->phonetic_word("\n"));
    self::assertSame('', $phonetic->phonetic_word('123'));
  }

  public function testSpanishPhoneticWord()
  {
    $testArray = [
        'Madrid'     => 'MADRID',
        'Barcelona'  => 'BARSELONA',
        'Sevilla'    => 'SEBIYA',
        'Zaragoza'   => 'SARAGOSA',
        'García'     => 'GARSIA',
        'Rodríguez'  => 'RODRIGES',
        'Hernández'  => 'ERNANDES',
        'Fernández'  => 'FERNANDES',
        'Jiménez'    => 'JIMENES',
        'Gimenez'    => 'JIMENES',
        'Vázquez'    => 'BASKES',
        'Basques'    => 'BASKES',
        'Iglesias'   => 'IGLESIAS',
        'Moelleken'  => 'MOEYEKEN',
    ];

    $phonetic = new PhoneticSpanish();
    foreach ($testArray as $before => $after) {
      self::assertSame($after, $phonetic->phonetic_word($before), 'tested: ' . $before);
    }
  }

  public function testViaTheLanguageFacade()
  {
    $facade = new Phonetic('es');
    $direct = new PhoneticSpanish();

    foreach (['Jiménez', 'llave', 'vergüenza', 'año'] as $word) {
      self::assertSame($direct->phonetic_word($word), $facade->phonetic_word($word), 'tested: ' . $word);
    }

    self::assertSame(
        ['llave' => 'YABE', 'vaca' => 'BAKA'],
        $facade->phonetic_sentence('llave vaca', false, false)
    );
  }
}

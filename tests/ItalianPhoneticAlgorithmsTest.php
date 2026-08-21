<?php

use voku\helper\Phonetic;
use voku\helper\PhoneticItalian;

/**
 * Class ItalianPhoneticAlgorithmsTest
 */
class ItalianPhoneticAlgorithmsTest extends \PHPUnit\Framework\TestCase
{
  /**
   * One row per rule of the code table in the class docblock.
   *
   * @return array
   */
  public function ruleProvider(): array
  {
    return [
      # "c" before e,i = /t͡ʃ/ = "C"
      ['cena', 'CENA'],
      ['cibo', 'CIBO'],
      # "ci" before a,o,u = /t͡ʃ/ = "C" (the "i" is only a spelling helper)
      ['ciao', 'CAO'],
      ['bacio', 'BACO'],
      ['ciuffo', 'CUFO'],
      # "c" before a,o,u or a consonant = /k/ = "K"
      ['casa', 'KASA'],
      ['cosa', 'KOSA'],
      ['cuore', 'KUORE'],
      ['clima', 'KLIMA'],
      # "ch" = /k/ = "K"
      ['chiesa', 'KIESA'],
      ['perche', 'PERKE'],
      # "g" before e,i = /d͡ʒ/ = "J"
      ['gelato', 'JELATO'],
      ['giro', 'JIRO'],
      # "gi" before a,o,u = /d͡ʒ/ = "J"
      ['giorno', 'JORNO'],
      ['giallo', 'JALO'],
      # "g" before a,o,u or a consonant = /g/ = "G"
      ['gatto', 'GATO'],
      ['gonna', 'GONA'],
      ['grande', 'GRANDE'],
      # "gh" = /g/ = "G"
      ['ghiaccio', 'GIACO'],
      ['spaghetti', 'SPAGETI'],
      # "gn" = /ɲ/ = "N"
      ['bagno', 'BANO'],
      ['gnocchi', 'NOKI'],
      # "gli" = /ʎ/ = "L"
      ['famiglia', 'FAMILA'],
      ['moglie', 'MOLE'],
      # "sc" before e,i = /ʃ/ = "X"
      ['pesce', 'PEXE'],
      ['uscita', 'UXITA'],
      # "sci" before a,o,u = /ʃ/ = "X"
      ['sciopero', 'XOPERO'],
      ['sciarpa', 'XARPA'],
      # "sc" before a,o,u or a consonant = /sk/ = "SK"
      ['pesca', 'PESKA'],
      ['scuola', 'SKUOLA'],
      ['scrivere', 'SKRIVERE'],
      ['schema', 'SKEMA'],
      ['maschio', 'MASKIO'],
      # "q" = /k/ = "K"
      ['quando', 'KUANDO'],
      ['acqua', 'AKUA'],
      # "h" is silent
      ['hotel', 'OTEL'],
      ['ho', 'O'],
      # "z" stays "Z"
      ['pizza', 'PIZA'],
      ['zio', 'ZIO'],
      # loanword letters
      ['whisky', 'VISKI'],
      ['xilofono', 'KSILOFONO'],
      ['yogurt', 'IOGURT'],
      ['kiwi', 'KIVI'],
      # accents mark the stress, not the sound
      ['citta', 'CITA'],
      ['città', 'CITA'],
      ['perché', 'PERKE'],
      ['piu', 'PIU'],
      ['più', 'PIU'],
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
    $phonetic = new PhoneticItalian();
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
        ['V', 'V'],
        ['W', 'V'],
        ['X', 'KS'],
        ['Y', 'I'],
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
    $phonetic = new PhoneticItalian();
    self::assertSame($expected, $phonetic->phonetic_word($char), 'tested: ' . $char);
  }

  /**
   * A missing or an additional double letter is the most common italian
   * misspelling and must not change the key.
   */
  public function testGeminationIsIgnored()
  {
    $phonetic = new PhoneticItalian();

    $pairs = [
        ['successo', 'sucesso'],
        ['pizza', 'piza'],
        ['gonna', 'gona'],
        ['bello', 'belo'],
        ['caffè', 'cafe'],
    ];

    foreach ($pairs as $pair) {
      list($correct, $misspelled) = $pair;

      self::assertSame(
          $phonetic->phonetic_word($correct),
          $phonetic->phonetic_word($misspelled),
          'tested: ' . $correct . ' vs. ' . $misspelled
      );
    }
  }

  /**
   * Words that only differ in the "soft/hard"-rule have to stay different.
   */
  public function testSoftAndHardSoundsStayDistinguishable()
  {
    $phonetic = new PhoneticItalian();

    $pairs = [
        ['cena', 'chena'],
        ['bacio', 'baco'],
        ['gelato', 'ghelato'],
        ['pesce', 'pesca'],
    ];

    foreach ($pairs as $pair) {
      list($soft, $hard) = $pair;

      self::assertNotSame(
          $phonetic->phonetic_word($hard),
          $phonetic->phonetic_word($soft),
          'tested: ' . $soft . ' vs. ' . $hard
      );
    }
  }

  /**
   * Verifies that generated phonetic keys contain only upper-case ASCII.
   */
  public function testOutputIsAlwaysUpperCaseAscii()
  {
    $phonetic = new PhoneticItalian();

    foreach (['Città', 'perché', 'Müller', 'Łódź', '中文空白', 'Sant\'Angelo', '123'] as $word) {
      $code = $phonetic->phonetic_word($word);

      self::assertSame(
          1,
          \preg_match('/^[A-Z]*$/', $code),
          'tested: ' . $word . ' => ' . $code
      );
    }
  }

  /**
   * Verifies that empty and non-letter input produces an empty phonetic key.
   */
  public function testIsEmptyString()
  {
    $phonetic = new PhoneticItalian();

    self::assertSame('', $phonetic->phonetic_word(''));
    self::assertSame('', $phonetic->phonetic_word(' '));
    self::assertSame('', $phonetic->phonetic_word("\n"));
    self::assertSame('', $phonetic->phonetic_word('123'));
  }

  /**
   * Pins representative Italian words to their expected phonetic keys.
   */
  public function testItalianPhoneticWord()
  {
    $testArray = [
        'Roma'                => 'ROMA',
        'Milano'              => 'MILANO',
        'Napoli'              => 'NAPOLI',
        'Firenze'             => 'FIRENZE',
        'Venezia'             => 'VENEZIA',
        'Giuseppe'            => 'JUSEPE',
        'Giuseppi'            => 'JUSEPI',
        'Francesco'           => 'FRANCESKO',
        'Luigi'               => 'LUIJI',
        'Rossi'               => 'ROSI',
        'Bianchi'             => 'BIANKI',
        'Esposito'            => 'ESPOSITO',
        'Moelleken'           => 'MOELEKEN',
        // "sch" is /sk/ in italian ("schema"), so it does NOT become "X" here
        'Müller-Lüdenscheidt' => 'MULERLUDENSKEIDT',
    ];

    $phonetic = new PhoneticItalian();
    foreach ($testArray as $before => $after) {
      self::assertSame($after, $phonetic->phonetic_word($before), 'tested: ' . $before);
    }
  }

  /**
   * Verifies that direct Italian encoding matches the language facade.
   */
  public function testViaTheLanguageFacade()
  {
    $facade = new Phonetic('it');
    $direct = new PhoneticItalian();

    foreach (['Giuseppe', 'gnocchi', 'famiglia', 'pesce'] as $word) {
      self::assertSame($direct->phonetic_word($word), $facade->phonetic_word($word), 'tested: ' . $word);
    }

    self::assertSame(
        ['sciopero' => 'XOPERO', 'gnocchi' => 'NOKI'],
        $facade->phonetic_sentence('sciopero gnocchi', false, false)
    );
  }
}

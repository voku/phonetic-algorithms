<?php

use voku\helper\Phonetic;
use voku\helper\PhoneticFrench;

/**
 * Class FrenchPhoneticAlgorithmsTest
 */
class FrenchPhoneticAlgorithmsTest extends \PHPUnit\Framework\TestCase
{
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
        ['Q', 'Q'],
        ['R', 'R'],
        ['S', 'S'],
        ['T', 'T'],
        ['U', 'U'],
        ['V', 'V'],
        ['W', 'W'],
        ['X', 'X'],
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
    $phonetic = new PhoneticFrench();
    self::assertEquals($expected, $phonetic->phonetic_word($char));
  }

  public function testEmptyWordReturnsEmptyIndex()
  {
    $phonetic = new PhoneticFrench();
    $index = $phonetic->phonetic_word('');
    self::assertEquals('', $index);
  }

  public function testFrenchPhoneticSentence()
  {
    $testArray = [
        'Müller Lüdenscheidt'         => ['Müller' => 'MUL', 'Lüdenscheidt' => 'LUDANCHEID'],
        'Müller-Lüdenscheidt'         => ['Müller-Lüdenscheidt' => 'MULERLUDANCHEID'],
        "\n \t"                       => [],
        "test \xc2\x88"               => ['test' => 'TES'],
        'Ã ¤'                         => ['Ã' => 'A'],
        'test'                        => ['test' => 'TES'],
        'text'                        => ['text' => 'TEX'],
        'Ein Satz mit vielen Wortern' => [
            'Ein'     => 'IN',
            'Satz'    => 'SATZ',
            'mit'     => 'MI',
            'vielen'  => 'VIELAN',
            'Wortern' => 'ORTERN',
        ],
        '中 文 空 白'                     => ['中' => 'ZON', '文' => 'WAN', '空' => 'KON', '白' => 'BAI'],

    ];


    $phonetic = new Phonetic('fr');
    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, $phonetic->phonetic_sentence($before, false, false), 'tested: ' . $before);
      }
    }
  }

  public function testFrenchPhoneticWord()
  {
    $testArray = [
        'Breschnew'           => 'BRECHNEW',
        'Müller Lüdenscheidt' => 'MULERLUDANCHEID',
        'Müller-Lüdenscheidt' => 'MULERLUDANCHEID',
        'müller'              => 'MUL',
        'schmidt'             => 'CHMID',
        'schneider'           => 'CHNAID',
        'Wikipedia'           => 'OUIKIPEDIA',
        'Kirsche'             => 'KIRCH',
        'Kirche'              => 'KIRCH',
        'Kirchä'              => 'KIRCHA',
        'Kircha'              => 'KIRCHA',
        'Vogel'               => 'VOJEL',
        'Fogel'               => 'FOJEL',
        'Wolke'               => 'OLK',
        'Volke'               => 'VOLK',
        'Lifetimes'           => 'LIFETIM',
        'livetimes'           => 'LIVETIM',
        'livtimes'            => 'LIVTIM',
        'livetimez'           => 'LIVETIM',
        'liwe-timez'          => 'LIWETIM',
        'Jackson'             => 'JAKSON',
        'Jakson'              => 'JAKSON',
        'Jäkson'              => 'JAKSON',
        'Jäksoon'             => 'JAKSOUN',
        'Stau'                => 'STO',
        'Staub'               => 'STOB',
        'Twen'                => 'TWAN',
        'Sven'                => 'SVAN',
        'Zven'                => 'ZVAN',
        'Zwen'                => 'ZWAN',
        'Hans'                => 'AN',
        'Franz'               => 'FRANZ',
        'Schokolade'          => 'CHOKOLAD',
        'Raddampfer'          => 'RADANPF',
        'Ã'                   => 'A',
        ' '                   => '',
        ''                    => '',
        "\n"                  => '',
        "test\xc2\x88"        => 'TES',
        'Düsseldorf'          => 'DUSELDORF',
        'DÃ¼sseldorf'         => 'DASELDORF',
        'Ã¤'                  => 'A',
        'test'                => 'TES',
        'text'                => 'TEX',
        'Moelleken'           => 'MOELEKAN',
        'Mölleken'            => 'MOLEKAN',
        'Möleken'             => 'MOLEKAN',
        'Moeleken'            => 'MOELEKAN',
        'Moellecken'          => 'MOELEKAN',
        'Möllecken'           => 'MOLEKAN',
        'Mölecken'            => 'MOLEKAN',
        '中文空白'                => 'ZONGWENKONGBAI',
        '中文空'                 => 'ZONGWENKON',
        'Maier'               => 'MAI',
        'Mayr'                => 'MAIR',
        'Mayer'               => 'MAI',
        'Meier'               => 'MEI',
        'Meyer'               => 'MEI',
        'Major'               => 'MAJOR',
        'Beier'               => 'BEI',
        'Fischer'             => 'FISK',
        'Vischer'             => 'VISK',
        'Auerbach'            => 'AUERBACH',
        'Ohrbach'             => 'ORBACH',
        'Moskowitz'           => 'MOSKOUITZ',
        'Moskovitz'           => 'MOSKOVITZ',
        'Mozkovits'           => 'MOZKOVI',
        'Ceniow'              => 'SENIOU',
        'Tsenyuv'             => 'TSENIUV',
        'Holubica'            => 'OLUBIKA',
        'Golubitsa'           => 'GOLUBITSA',
        'Leben'               => 'LEBAN',
        'Lübyien'             => 'LUBIN',
        'Lybien'              => 'LIBIN',

    ];

    $phonetic = new PhoneticFrench();
    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, $phonetic->phonetic_word($before), 'tested: ' . $before);
      }
    }
  }

  public function testIsEmptyString()
  {
    $phonetic = new PhoneticFrench();
    self::assertSame('', $phonetic->phonetic_word(''));
  }

  /**
   * @param string $expected
   * @param string $word
   *
   * @dataProvider wordsProvider
   */
  public function testWords($expected, $word)
  {
    $phonetic = new PhoneticFrench();
    self::assertEquals($expected, $phonetic->phonetic_word($word));
  }

  /**
   * @return array
   */
  public function wordsProvider(): array
  {
    return [
        ['OLJ', 'Holger'],
        ['OLTERSDORF', 'Woltersdorf'],
        ['BRECHNEW', 'Breschnew'],
        ['MULERLUDANCHEID', 'Müller-Lüdenscheidt'],
        ['OUIKIPEDIA', 'Wikipedia'],
    ];
  }
}

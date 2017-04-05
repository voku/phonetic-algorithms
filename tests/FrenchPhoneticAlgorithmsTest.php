<?php

use voku\helper\Phonetic;
use voku\helper\PhoneticFrench;

/**
 * Class FrenchPhoneticAlgorithmsTest
 */
class FrenchPhoneticAlgorithmsTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @return array
   */
  public function charProvider()
  {
    return array(
        array('A', 'A'),
        array('B', 'B'),
        array('C', 'C'),
        array('D', 'D'),
        array('E', 'E'),
        array('F', 'F'),
        array('G', 'G'),
        array('H', 'H'),
        array('I', 'I'),
        array('J', 'J'),
        array('K', 'K'),
        array('L', 'L'),
        array('M', 'M'),
        array('N', 'N'),
        array('O', 'O'),
        array('P', 'P'),
        array('Q', 'Q'),
        array('R', 'R'),
        array('S', 'S'),
        array('T', 'T'),
        array('U', 'U'),
        array('V', 'V'),
        array('W', 'W'),
        array('X', 'X'),
        array('Y', 'Y'),
        array('Z', 'Z'),
    );
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
    $testArray = array(
        'Müller Lüdenscheidt'         => array('Müller' => 'MUL', 'Lüdenscheidt' => 'LUDANCHEID'),
        'Müller-Lüdenscheidt'         => array('Müller-Lüdenscheidt' => 'MULERLUDANCHEID'),
        "\n \t"                       => array(),
        "test \xc2\x88"               => array('test' => 'TES'),
        'Ã ¤'                         => array('Ã' => 'A'),
        'test'                        => array('test' => 'TES'),
        'text'                        => array('text' => 'TEX'),
        'Ein Satz mit vielen Wortern' => array('Ein'     => 'IN',
                                               'Satz'    => 'SATZ',
                                               'mit'     => 'MI',
                                               'vielen'  => 'VIELAN',
                                               'Wortern' => 'ORTERN',
        ),
        '中 文 空 白'                     => array('中' => 'ZON', '文' => 'WAN', '空' => 'KON', '白' => 'BAI'),

    );


    $phonetic = new Phonetic('fr');
    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, $phonetic->phonetic_sentence($before, false, false), 'tested: ' . $before);
      }
    }
  }

  public function testFrenchPhoneticWord()
  {
    $testArray = array(
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

    );

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
  public function wordsProvider()
  {
    return array(
        array('OLJ', 'Holger'),
        array('OLTERSDORF', 'Woltersdorf'),
        array('BRECHNEW', 'Breschnew'),
        array('MULERLUDANCHEID', 'Müller-Lüdenscheidt'),
        array('OUIKIPEDIA', 'Wikipedia'),
    );
  }
}

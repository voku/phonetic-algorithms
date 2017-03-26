<?php

use voku\helper\PhoneticAlgorithms;

/**
 * Class englishPhoneticAlgorithmsTest
 */
class englishPhoneticAlgorithmsTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @return array
   */
  public function charProvider()
  {
    return array(
        array('A', 'A'),
        array('B', 'B'),
        array('C', 'K'),
        array('D', 'T'),
        array('E', 'E'),
        array('F', 'F'),
        array('G', 'K'),
        array('H', ''),
        array('I', 'I'),
        array('J', 'J'),
        array('K', 'K'),
        array('L', 'L'),
        array('M', 'M'),
        array('N', 'N'),
        array('O', 'O'),
        array('P', 'P'),
        array('Q', 'K'),
        array('R', 'R'),
        array('S', 'S'),
        array('T', 'T'),
        array('U', 'U'),
        array('V', 'F'),
        array('W', ''),
        array('X', 'S'),
        array('Y', ''),
        array('Z', 'S'),
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
    self::assertEquals($expected, PhoneticAlgorithms::english_phonetic_word($char));
  }

  public function testEmptyWordReturnsEmptyIndex()
  {
    $index = PhoneticAlgorithms::english_phonetic_word('');
    self::assertEquals('', $index);
  }

  public function testEnglishPhoneticSentence()
  {
    $testArray = array(
        'Müller Lüdenscheidt'         => array('MLR', 'LTNSXTT'),
        'Müller-Lüdenscheidt'         => array('MLRLTNSXTT'),
        "\n \t"                       => array(),
        "test \xc2\x88"               => array('TST'),
        'Sind wir Düssel dorf'        => array('SNT', 'WR', 'TSL', 'TRF'),
        'Sind wir Düssel dorf ?'      => array('SNT', 'WR', 'TSL', 'TRF'),
        'Sind wir Düssel dorf?'       => array('SNT', 'WR', 'TSL', 'TRF'),
        'Sind wir Düssel-dorf'        => array('SNT', 'WR', 'TSLTRF'),
        'Ã ¤'                         => array('A'),
        'test'                        => array('TST'),
        'text'                        => array('TKST'),
        'Ein Satz mit vielen Wortern' => array('EN', 'STS', 'MT', 'FLN', 'WRTRN'),
        '中 文 空 白'                     => array('SHNK', 'WN', 'KNK', 'B'),

    );

    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, PhoneticAlgorithms::english_phonetic_sentence($before), 'tested: ' . $before);
      }
    }
  }

  public function testEnglishPhoneticWord()
  {
    $testArray = array(
        'Breschnew'           => 'BRSXN',
        'Müller Lüdenscheidt' => 'MLRLTNSXTT',
        'Müller-Lüdenscheidt' => 'MLRLTNSXTT',
        'müller'              => 'MLR',
        'schmidt'             => 'SXMTT',
        'schneider'           => 'SXNTR',
        'Wikipedia'           => 'WKPT',
        'Kirsche'             => 'KRSX',
        'Kirche'              => 'KRX',
        'Kirchä'              => 'KRX',
        'Kircha'              => 'KRX',
        'Vogel'               => 'FJL',
        'Fogel'               => 'FJL',
        'Wolke'               => 'WLK',
        'Volke'               => 'FLK',
        'Lifetimes'           => 'LFTMS',
        'livetimes'           => 'LFTMS',
        'livtimes'            => 'LFTMS',
        'livetimez'           => 'LFTMS',
        'liwe-timez'          => 'LWTMS',
        'Jackson'             => 'JKSN',
        'Jakson'              => 'JKSN',
        'Jäkson'              => 'JKSN',
        'Jäksoon'             => 'JKSN',
        'Stau'                => 'ST',
        'Staub'               => 'STB',
        'Twen'                => 'TWN',
        'Sven'                => 'SFN',
        'Zven'                => 'SFN',
        'Zwen'                => 'SWN',
        'Hans'                => 'HNS',
        'Franz'               => 'FRNS',
        'Schokolade'          => 'SXKLT',
        'Raddampfer'          => 'RTMPFR',
        'Ã'                   => 'A',
        ' '                   => '',
        ''                    => '',
        "\n"                  => '',
        "test\xc2\x88"        => 'TST',
        'Düsseldorf'          => 'TSLTRF',
        'DÃ¼sseldorf'         => 'TSLTRF',
        'Ã¤'                  => 'A',
        'test'                => 'TST',
        'text'                => 'TKST',
        'Moelleken'           => 'MLKN',
        'Mölleken'            => 'MLKN',
        'Möleken'             => 'MLKN',
        'Moeleken'            => 'MLKN',
        'Moellecken'          => 'MLKN',
        'Möllecken'           => 'MLKN',
        'Mölecken'            => 'MLKN',
        'Ein'                 => 'EN',
        'Satz'                => 'STS',
        'mit'                 => 'MT',
        'vielen'              => 'FLN',
        'Wortern'             => 'WRTRN',
        '中文空白'                => 'SHNKWNKNKB',
        '中文空'                 => 'SHNKWNKNK',
        'Maier'               => 'MR',
        'Mayr'                => 'MR',
        'Mayer'               => 'MYR',
        'Meier'               => 'MR',
        'Meyer'               => 'MYR',
        'Major'               => 'MJR',
        'Beier'               => 'BR',
        'Fischer'             => 'FSXR',
        'Vischer'             => 'FSXR',
        'Auerbach'            => 'ARBX',
        'Ohrbach'             => 'ORBX',
        'Moskowitz'           => 'MSKWTS',
        'Moskovitz'           => 'MSKFTS',
        'Mozkovits'           => 'MSKFTS',
        'Ceniow'              => 'SN',
        'Tsenyuv'             => 'TSNYF',
        'Holubica'            => 'HLBK',
        'Golubitsa'           => 'KLBTS',
        'Leben'               => 'LBN',
        'Lübyien'             => 'LBYN',
        'Lybien'              => 'LBN',

    );

    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, PhoneticAlgorithms::english_phonetic_word($before), 'tested: ' . $before);
      }
    }
  }

  public function testIsEmptyString()
  {
    self::assertSame('', PhoneticAlgorithms::english_phonetic_word(''));
  }

  /**
   * @param string $expected
   * @param string $word
   *
   * @dataProvider wordsProvider
   */
  public function testWords($expected, $word)
  {
    self::assertEquals($expected, PhoneticAlgorithms::english_phonetic_word($word));
  }

  /**
   * @return array
   */
  public function wordsProvider()
  {
    return array(
        array('HLJR', 'Holger'),
        array('WLTRSTRF', 'Woltersdorf'),
        array('BRSXN', 'Breschnew'),
        array('MLRLTNSXTT', 'Müller-Lüdenscheidt'),
        array('WKPT', 'Wikipedia'),
    );
  }
}

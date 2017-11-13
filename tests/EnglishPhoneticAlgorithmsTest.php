<?php

use voku\helper\Phonetic;
use voku\helper\PhoneticEnglish;

/**
 * Class EnglishPhoneticAlgorithmsTest
 */
class EnglishPhoneticAlgorithmsTest extends \PHPUnit\Framework\TestCase
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
    $phonetic = new PhoneticEnglish();
    self::assertEquals($expected, $phonetic->phonetic_word($char));
  }

  public function testEmptyWordReturnsEmptyIndex()
  {
    $phonetic = new PhoneticEnglish();
    $index = $phonetic->phonetic_word('');
    self::assertEquals('', $index);
  }

  public function testEnglishPhoneticSentence()
  {
    $testArray = array(
        'Müller Lüdenscheidt'         => array('Müller' => 'MLR', 'Lüdenscheidt' => 'LTNSXTT'),
        'Müller-Lüdenscheidt'         => array('Müller-Lüdenscheidt' => 'MLRLTNSXTT'),
        "\n \t"                       => array(),
        "test \xc2\x88"               => array('test' => 'TST'),
        'Sind wir Düssel dorf'        => array('Sind' => 'SNT', 'wir' => 'WR', 'Düssel' => 'TSL', 'dorf' => 'TRF'),
        'Sind wir Düssel dorf ?'      => array('Sind' => 'SNT', 'wir' => 'WR', 'Düssel' => 'TSL', 'dorf' => 'TRF'),
        'Sind wir Düssel dorf?'       => array('Sind' => 'SNT', 'wir' => 'WR', 'Düssel' => 'TSL', 'dorf' => 'TRF'),
        'Sind wir Düssel-dorf'        => array('Sind' => 'SNT', 'wir' => 'WR', 'Düssel-dorf' => 'TSLTRF'),
        'Ã ¤'                         => array('Ã' => 'A'),
        'test'                        => array('test' => 'TST'),
        'text'                        => array('text' => 'TKST'),
        'Ein Satz mit vielen Wortern' => array('Ein'     => 'EN',
                                               'Satz'    => 'STS',
                                               'mit'     => 'MT',
                                               'vielen'  => 'FLN',
                                               'Wortern' => 'WRTRN',
        ),
        '中 文 空 白'                     => array('中' => 'SHNK', '文' => 'WN', '空' => 'KNK', '白' => 'B'),

    );


    $phonetic = new Phonetic('en');
    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, $phonetic->phonetic_sentence($before, false, false), 'tested: ' . $before);
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

    $phonetic = new PhoneticEnglish();
    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, $phonetic->phonetic_word($before), 'tested: ' . $before);
      }
    }
  }

  public function testIsEmptyString()
  {
    $phonetic = new PhoneticEnglish();
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
    $phonetic = new PhoneticEnglish();
    self::assertEquals($expected, $phonetic->phonetic_word($word));
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

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
  public function charProvider(): array
  {
    return [
        ['A', 'A'],
        ['B', 'B'],
        ['C', 'K'],
        ['D', 'T'],
        ['E', 'E'],
        ['F', 'F'],
        ['G', 'K'],
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
        ['V', 'F'],
        ['W', ''],
        ['X', 'S'],
        ['Y', ''],
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
    $testArray = [
        'Müller Lüdenscheidt'         => ['Müller' => 'MLR', 'Lüdenscheidt' => 'LTNSXTT'],
        'Müller-Lüdenscheidt'         => ['Müller-Lüdenscheidt' => 'MLRLTNSXTT'],
        "\n \t"                       => [],
        "test \xc2\x88"               => ['test' => 'TST'],
        'Sind wir Düssel dorf'        => ['Sind' => 'SNT', 'wir' => 'WR', 'Düssel' => 'TSL', 'dorf' => 'TRF'],
        'Sind wir Düssel dorf ?'      => ['Sind' => 'SNT', 'wir' => 'WR', 'Düssel' => 'TSL', 'dorf' => 'TRF'],
        'Sind wir Düssel dorf?'       => ['Sind' => 'SNT', 'wir' => 'WR', 'Düssel' => 'TSL', 'dorf' => 'TRF'],
        'Sind wir Düssel-dorf'        => ['Sind' => 'SNT', 'wir' => 'WR', 'Düssel-dorf' => 'TSLTRF'],
        'Ã ¤'                         => ['Ã' => 'A'],
        'test'                        => ['test' => 'TST'],
        'text'                        => ['text' => 'TKST'],
        'Ein Satz mit vielen Wortern' => [
            'Ein'     => 'EN',
            'Satz'    => 'STS',
            'mit'     => 'MT',
            'vielen'  => 'FLN',
            'Wortern' => 'WRTRN',
        ],
        '中 文 空 白'                     => ['中' => 'SHNK', '文' => 'WN', '空' => 'KNK', '白' => 'B'],

    ];


    $phonetic = new Phonetic('en');
    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, $phonetic->phonetic_sentence($before, false, false), 'tested: ' . $before);
      }
    }
  }

  public function testEnglishPhoneticWord()
  {
    $testArray = [
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

    ];

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
  public function wordsProvider(): array
  {
    return [
        ['HLJR', 'Holger'],
        ['WLTRSTRF', 'Woltersdorf'],
        ['BRSXN', 'Breschnew'],
        ['MLRLTNSXTT', 'Müller-Lüdenscheidt'],
        ['WKPT', 'Wikipedia'],
    ];
  }
}

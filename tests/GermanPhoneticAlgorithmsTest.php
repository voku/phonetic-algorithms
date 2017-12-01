<?php

use voku\helper\Phonetic;
use voku\helper\PhoneticGerman;

/**
 * Class GermanPhoneticAlgorithmsTest
 */
class GermanPhoneticAlgorithmsTest extends \PHPUnit\Framework\TestCase
{
  /**
   * @return array
   */
  public function cProvider(): array
  {
    return [
        ['CA', '4'],
        ['CC', '8'],
        ['CL', '45'],
        ['CR', '47'],
        ['CX', '48'],
        ['CB', '81'],
        ['CD', '82'],
        ['CF', '83'],
        ['CG', '84'],
        ['CM', '86'],
        ['PCA', '14'],
        ['PCC', '18'],
        ['PCX', '148'],
        ['PCP', '181'],
        ['PCD', '182'],
        ['PCF', '183'],
        ['PCG', '184'],
        ['PCL', '185'],
        ['PCM', '186'],
        ['PCR', '187'],
    ];
  }

  /**
   * @return array
   */
  public function cRuleProvider(): array
  {
    return [
      # C as initial sound before A, H, K, L, O, Q, R, U, X = '4'
      ['CA', '4'],
      ['CH', '4'],
      ['CK', '4'],
      ['CL', '45'],
      ['CO', '4'],
      ['CQ', '4'],
      ['CR', '47'],
      ['CU', '4'],
      ['CX', '48'],
      # Ca as initial sound NOT before A, H, K, L, O, Q, R, U, X = '8'
      ['CB', '81'],
      ['CC', '8'],
      ['CD', '82'],
      ['CE', '8'],
      ['CF', '83'],
      ['CG', '84'],
      ['CI', '8'],
      ['CJ', '8'],
      ['CM', '86'],
      ['CN', '86'],
      ['CP', '81'],
      ['CS', '8'],
      ['CT', '82'],
      ['CV', '83'],
      ['CW', '83'],
      ['CY', '8'],
      ['CZ', '8'],
      # C after S, Z = '8'
      ['SC', '8'],
      ['ZC', '8'],
      ['SCX', '8'],
      ['ZCX', '8'],
      # C before A, H, K, O, Q, U, X but NOT after S, Z = '4'
      ['BCA', '14'],
      ['BCH', '14'],
      ['BCK', '14'],
      ['BCO', '14'],
      ['BCQ', '14'],
      ['BCU', '14'],
      ['BCX', '148'],
      # C not before A, H, K, O, Q, U, X = '8'
      ['BCB', '181'],
      ['BCC', '18'],
      ['BCD', '182'],
      ['BCE', '18'],
      ['BCF', '183'],
      ['BCG', '184'],
      ['BCI', '18'],
      ['BCJ', '18'],
      ['BCL', '185'],
      ['BCM', '186'],
      ['BCN', '186'],
      ['BCP', '181'],
      ['BCR', '187'],
      ['BCS', '18'],
      ['BCT', '182'],
      ['BCV', '183'],
      ['BCW', '183'],
      ['BCY', '18'],
      ['BCZ', '18'],
    ];
  }

  /**
   * @return array
   */
  public function charProvider(): array
  {
    return [
        ['A', '0'],
        ['B', '1'],
        ['C', '4'],
        ['D', '2'],
        ['E', '0'],
        ['F', '3'],
        ['G', '4'],
        ['H', ''],
        ['I', '0'],
        ['J', '0'],
        ['K', '4'],
        ['L', '5'],
        ['M', '6'],
        ['N', '6'],
        ['O', '0'],
        ['P', '1'],
        ['Q', '4'],
        ['R', '7'],
        ['S', '8'],
        ['T', '2'],
        ['U', '0'],
        ['V', '3'],
        ['W', '3'],
        ['X', '48'],
        ['Y', '0'],
        ['Z', '8'],
    ];
  }

  /**
   * @return array
   */
  public function dProvider(): array
  {
    return [
        ['DA', '2'],
        ['DC', '8'],
        ['DB', '21'],
        ['DF', '23'],
        ['DG', '24'],
        ['DL', '25'],
        ['DM', '26'],
        ['DR', '27'],
        ['DX', '248'],
    ];
  }

  /**
   * @return array
   */
  public function dtRuleProvider(): array
  {
    return [
      # D not before C, S, Z = '2'
      ['DA', '2'],
      ['DB', '21'],
      ['DD', '2'],
      ['DE', '2'],
      ['DF', '23'],
      ['DF', '23'],
      ['DG', '24'],
      ['DH', '2'],
      ['DI', '2'],
      ['DJ', '2'],
      ['DK', '24'],
      ['DL', '25'],
      ['DM', '26'],
      ['DN', '26'],
      ['DO', '2'],
      ['DP', '21'],
      ['DQ', '24'],
      ['DR', '27'],
      ['DT', '2'],
      ['DU', '2'],
      ['DV', '23'],
      ['DW', '23'],
      ['DX', '248'],
      ['DY', '2'],
      # T not before C, S, Z = '2'
      ['TA', '2'],
      ['TB', '21'],
      ['TD', '2'],
      ['TE', '2'],
      ['TF', '23'],
      ['TF', '23'],
      ['TG', '24'],
      ['TH', '2'],
      ['TI', '2'],
      ['TJ', '2'],
      ['TK', '24'],
      ['TL', '25'],
      ['TM', '26'],
      ['TN', '26'],
      ['TO', '2'],
      ['TP', '21'],
      ['TQ', '24'],
      ['TR', '27'],
      ['TT', '2'],
      ['TU', '2'],
      ['TV', '23'],
      ['TW', '23'],
      ['TX', '248'],
      ['TY', '2'],
      # D before C, S, Z = '8'
      ['DC', '8'],
      ['DS', '8'],
      ['DZ', '8'],
      # T before C, S, Z = '8'
      ['TC', '8'],
      ['TS', '8'],
      ['TZ', '8'],
    ];
  }

  /**
   * @return array
   */
  public function pProvider(): array
  {
    return [
        ['PA', '1'],
        ['PH', '3'],
        ['PD', '12'],
        ['PF', '13'],
        ['PG', '14'],
        ['PL', '15'],
        ['PM', '16'],
        ['PR', '17'],
        ['PC', '18'],
        ['PX', '148'],
    ];
  }

  /**
   * @return array
   */
  public function pRuleProvider(): array
  {
    return [
      # P not before H = '1'
      ['PA', '1'],
      ['PB', '1'],
      ['PC', '18'],
      ['PD', '12'],
      ['PE', '1'],
      ['PF', '13'],
      ['PG', '14'],
      ['PG', '14'],
      ['PI', '1'],
      ['PJ', '1'],
      ['PK', '14'],
      ['PL', '15'],
      ['PM', '16'],
      ['PN', '16'],
      ['PO', '1'],
      ['PP', '1'],
      ['PQ', '14'],
      ['PR', '17'],
      ['PS', '18'],
      ['PT', '12'],
      ['PU', '1'],
      ['PV', '13'],
      ['PW', '13'],
      ['PX', '148'],
      ['PY', '1'],
      ['PZ', '18'],
      # P before H = '3'
      ['PH', '3'],
    ];
  }

  /**
   * @return array
   */
  public function singleCharacterProvider(): array
  {
    return [
      # A, E, I, J, O, U, Y = '0'
      ['A', '0'],
      ['E', '0'],
      ['I', '0'],
      ['J', '0'],
      ['O', '0'],
      ['U', '0'],
      ['Y', '0'],
      # H = ''
      ['H', ''],
      # B, P = '1'
      ['B', '1'],
      ['P', '1'],
      # D, T = '2'
      ['D', '2'],
      ['T', '2'],
      # F, V, W = '3'
      ['F', '3'],
      ['V', '3'],
      ['W', '3'],
      # G, K, Q = '4'
      ['G', '4'],
      ['K', '4'],
      ['Q', '4'],
      # C = '4'
      ['C', '4'],
      # X = '48'
      ['X', '48'],
      # L = '5',
      ['L', '5'],
      # M, N
      ['M', '6'],
      ['N', '6'],
      # R = '7'
      ['R', '7'],
      # S, Z = '8'
      ['S', '8'],
      ['Z', '8'],
    ];
  }

  /**
   * @return array
   */
  public function tProvider(): array
  {
    return [
        ['TA', '2'],
        ['TC', '8'],
        ['TB', '21'],
        ['TF', '23'],
        ['TG', '24'],
        ['TL', '25'],
        ['TM', '26'],
        ['TR', '27'],
        ['TX', '248'],
    ];
  }

  /**
   * @param string $word
   * @param string $expected
   *
   * @dataProvider cProvider
   */
  public function testC($word, $expected)
  {
    $phonetic = new PhoneticGerman();
    self::assertEquals($expected, $phonetic->phonetic_word($word));
  }

  /**
   * @param string $char
   * @param string $expected
   *
   * @dataProvider charProvider
   */
  public function testChars($char, $expected)
  {
    $phonetic = new PhoneticGerman();
    self::assertEquals($expected, $phonetic->phonetic_word($char));
  }

  /**
   * @param $word
   * @param $expected
   *
   * @dataProvider dProvider
   */
  public function testD($word, $expected)
  {
    $phonetic = new PhoneticGerman();
    self::assertEquals($expected, $phonetic->phonetic_word($word));
  }

  public function testEmptyWordReturnsEmptyIndex()
  {
    $phonetic = new PhoneticGerman();
    $index = $phonetic->phonetic_word('');
    self::assertEquals('', $index);
  }

  public function testGermanPhoneticSentence()
  {
    $testArray = [
        'Müller Lüdenscheidt'                  => ['Müller' => '657', 'Lüdenscheidt' => '52682'],
        'Müller-Lüdenscheidt'                  => ['Müller-Lüdenscheidt' => '65752682'],
        "\n \t"                                => [],
        "test \xc2\x88"                        => ['test' => '282'],
        'Sind wir Düssel dorf'                 => ['Sind' => '862', 'wir' => '37', 'Düssel' => '285', 'dorf' => '273'],
        'Sind wir Düssel dorf ?'               => ['Sind' => '862', 'wir' => '37', 'Düssel' => '285', 'dorf' => '273'],
        'Sind wir Düssel dorf?'                => ['Sind' => '862', 'wir' => '37', 'Düssel' => '285', 'dorf' => '273'],
        'Sind wir Düssel-dorf'                 => ['Sind' => '862', 'wir' => '37', 'Düssel-dorf' => '285273'],
        'Ã ¤'                                  => ['Ã' => '0'],
        'test'                                 => ['test' => '282'],
        'text'                                 => ['text' => '2482'],
        'Hauptfilter Feinstaub F9 für PF 1400' => [
            'Hauptfilter' => '0123527',
            'Feinstaub'   => '36821',
            'F'           => '3',
            'für'         => '37',
            'PF'          => '13',
        ],
        'Ein Satz mit vielen Wortern'          => [
            'Ein'     => '06',
            'Satz'    => '8',
            'mit'     => '62',
            'vielen'  => '356',
            'Wortern' => '37276',
        ],
        '中 文 空 白'                              => [
            '中' => '064',
            '文' => '06',
            '空' => '064',
            '白' => '0',
        ],

    ];

    $phonetic = new Phonetic('de');
    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, $phonetic->phonetic_sentence($before, false, false), 'tested: ' . $before);
      }
    }
  }

  public function testGermanPhoneticWord()
  {
    $testArray = [
        'Breschnew'           => '17863',
        'Müller Lüdenscheidt' => '65752682',
        'Müller-Lüdenscheidt' => '65752682',
        'müller'              => '657',
        'schmidt'             => '862',
        'schneider'           => '8627',
        'Wikipedia'           => '3412',
        'Kirsche'             => '478',
        'Kirche'              => '478',
        'Kirchä'              => '478',
        'Kircha'              => '478',
        'Vogel'               => '345',
        'Fogel'               => '345',
        'Wolke'               => '354',
        'Volke'               => '354',
        'Lifetimes'           => '53268',
        'livetimes'           => '53268',
        'livtimes'            => '53268',
        'livetimez'           => '53268',
        'liwe-timez'          => '53268',
        'Jackson'             => '0486',
        'Jakson'              => '0486',
        'Jäkson'              => '0486',
        'Jäksoon'             => '0486',
        'Stau'                => '82',
        'Staub'               => '821',
        'Twen'                => '236',
        'Sven'                => '836',
        'Zven'                => '836',
        'Zwen'                => '836',
        'Hans'                => '068',
        'Franz'               => '3768',
        'Schokolade'          => '8452',
        'Raddampfer'          => '726137',
        'Ã'                   => '0',
        ' '                   => '',
        ''                    => '',
        "\n"                  => '',
        "test\xc2\x88"        => '282',
        'Düsseldorf'          => '285273',
        'DÃ¼sseldorf'         => '285273',
        'Ã¤'                  => '0',
        'test'                => '282',
        'text'                => '2482',
        'Moelleken'           => '6546',
        'Mölleken'            => '6546',
        'Möleken'             => '6546',
        'Moeleken'            => '6546',
        'Moellecken'          => '6546',
        'Möllecken'           => '6546',
        'Mölecken'            => '6546',
        'Shopping'            => '8164',
        'Shppping'            => '8164',
        'shuping'             => '8164',
        'Ein'                 => '06',
        'Satz'                => '8',
        'mit'                 => '62',
        'vielen'              => '356',
        'Wortern'             => '37276',
        '中文空白'                => '06464',
        '中文空'                 => '06464',
        'Maier'               => '67',
        'Mayr'                => '67',
        'Mayer'               => '67',
        'Meier'               => '67',
        'Meyer'               => '67',
        'Major'               => '67',
        'Beier'               => '17',
        'Fischer'             => '387',
        'Vischer'             => '387',
        'Auerbach'            => '0714',
        'Ohrbach'             => '0714',
        'Moskowitz'           => '68438',
        'Moskovitz'           => '68438',
        'Mozkovits'           => '68438',
        'Ceniow'              => '863',
        'Tsenyuv'             => '863',
        'Holubica'            => '0514',
        'Golubitsa'           => '4518',
        'Leben'               => '516',
        'Lübyien'             => '516',
        'Lybien'              => '516',

    ];

    $phonetic = new PhoneticGerman();
    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, $phonetic->phonetic_word($before), 'tested: ' . $before);
      }
    }
  }

  public function testIsEmptyString()
  {
    $phonetic = new PhoneticGerman();
    self::assertSame('', $phonetic->phonetic_word(''));
  }

  /**
   * @param string $word
   * @param string $expected
   *
   * @dataProvider pProvider
   */
  public function testP($word, $expected)
  {
    $phonetic = new PhoneticGerman();
    self::assertEquals($expected, $phonetic->phonetic_word($word));
  }

  /**
   * @param string $char
   * @param string $expectedCode
   *
   * @dataProvider singleCharacterProvider
   */
  public function testSingleCharacterTransliteration($char, $expectedCode)
  {
    $phonetic = new PhoneticGerman();
    $index = $phonetic->phonetic_word($char);
    self::assertEquals($expectedCode, $index);
  }

  /**
   * @param string $word
   * @param string $expectedCode
   *
   * @dataProvider cRuleProvider
   */
  public function testSpecialRulesForC($word, $expectedCode)
  {
    $phonetic = new PhoneticGerman();
    $index = $phonetic->phonetic_word($word);
    self::assertEquals($expectedCode, $index);
  }

  /**
   * @param string $word
   * @param string $expectedCode
   *
   * @dataProvider dtRuleProvider
   */
  public function testSpecialRulesForDT($word, $expectedCode)
  {
    $phonetic = new PhoneticGerman();
    $index = $phonetic->phonetic_word($word);
    self::assertEquals($expectedCode, $index);
  }

  /**
   * @param string $word
   * @param string $expectedCode
   *
   * @dataProvider pRuleProvider
   */
  public function testSpecialRulesForP($word, $expectedCode)
  {
    $phonetic = new PhoneticGerman();
    $index = $phonetic->phonetic_word($word);
    self::assertEquals($expectedCode, $index);
  }

  /**
   * @param string $word
   * @param string $expectedCode
   *
   * @dataProvider xRuleProvider
   */
  public function testSpecialRulesForX($word, $expectedCode)
  {
    $phonetic = new PhoneticGerman();
    $index = $phonetic->phonetic_word($word);
    self::assertEquals($expectedCode, $index);
  }

  /**
   * @param $word
   * @param $expected
   *
   * @dataProvider tProvider
   */
  public function testT($word, $expected)
  {
    $phonetic = new PhoneticGerman();
    self::assertEquals($expected, $phonetic->phonetic_word($word));
  }

  /**
   * @param string $expected
   * @param string $word
   *
   * @dataProvider wordsProvider
   */
  public function testWords($expected, $word)
  {
    $phonetic = new PhoneticGerman();
    self::assertEquals($expected, $phonetic->phonetic_word($word));
  }

  /**
   * @param string $word
   * @param string $expected
   *
   * @dataProvider xProvider
   */
  public function testX($word, $expected)
  {
    $phonetic = new PhoneticGerman();
    self::assertEquals($expected, $phonetic->phonetic_word($word));
  }

  /**
   * @return array
   */
  public function wordsProvider(): array
  {
    return [
        ['547', 'Holger'],
        ['35278273', 'Woltersdorf'],
        ['17863', 'Breschnew'],
        ['65752682', 'Müller-Lüdenscheidt'],
        ['3412', 'Wikipedia'],
    ];
  }

  /**
   * @return array
   */
  public function xProvider(): array
  {
    return [
        ['AX', '48'],
        ['IX', '048'],
        ['BX', '148'],
        ['DX', '248'],
        ['FX', '348'],
        ['LX', '548'],
        ['MX', '648'],
        ['RX', '748'],
        ['SX', '848'],
        ['XX', '4848'],
    ];
  }

  /**
   * @return array
   */
  public function xRuleProvider(): array
  {
    return [
      # X not after C, K, Q = '48'
      ['AX', '48'],
      ['BX', '148'],
      ['DX', '248'],
      ['EX', '48'],
      ['FX', '348'],
      ['GX', '48'],
      ['HX', '48'],
      ['IX', '048'],
      ['JX', '048'],
      ['LX', '548'],
      ['MX', '648'],
      ['NX', '648'],
      ['OX', '048'],
      ['PX', '148'],
      ['RX', '748'],
      ['SX', '848'],
      ['TX', '248'],
      ['UX', '048'],
      ['VX', '348'],
      ['WX', '348'],
      ['XX', '4848'],
      ['YX', '048'],
      ['ZX', '848'],
      # X after C, K, Q = '8'
      ['CX', '48'],
      ['KX', '48'],
      ['QX', '48'],
    ];
  }
}

<?php

use voku\helper\Phonetic;
use voku\helper\PhoneticGerman;

/**
 * Class GermanPhoneticAlgorithmsTest
 */
class GermanPhoneticAlgorithmsTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @return array
   */
  public function cProvider()
  {
    return array(
        array('CA', '4'),
        array('CC', '8'),
        array('CL', '45'),
        array('CR', '47'),
        array('CX', '48'),
        array('CB', '81'),
        array('CD', '82'),
        array('CF', '83'),
        array('CG', '84'),
        array('CM', '86'),
        array('PCA', '14'),
        array('PCC', '18'),
        array('PCX', '148'),
        array('PCP', '181'),
        array('PCD', '182'),
        array('PCF', '183'),
        array('PCG', '184'),
        array('PCL', '185'),
        array('PCM', '186'),
        array('PCR', '187'),
    );
  }

  /**
   * @return array
   */
  public function cRuleProvider()
  {
    return array(
      # C as initial sound before A, H, K, L, O, Q, R, U, X = '4'
      array('CA', '4'),
      array('CH', '4'),
      array('CK', '4'),
      array('CL', '45'),
      array('CO', '4'),
      array('CQ', '4'),
      array('CR', '47'),
      array('CU', '4'),
      array('CX', '48'),
      # Ca as initial sound NOT before A, H, K, L, O, Q, R, U, X = '8'
      array('CB', '81'),
      array('CC', '8'),
      array('CD', '82'),
      array('CE', '8'),
      array('CF', '83'),
      array('CG', '84'),
      array('CI', '8'),
      array('CJ', '8'),
      array('CM', '86'),
      array('CN', '86'),
      array('CP', '81'),
      array('CS', '8'),
      array('CT', '82'),
      array('CV', '83'),
      array('CW', '83'),
      array('CY', '8'),
      array('CZ', '8'),
      # C after S, Z = '8'
      array('SC', '8'),
      array('ZC', '8'),
      array('SCX', '8'),
      array('ZCX', '8'),
      # C before A, H, K, O, Q, U, X but NOT after S, Z = '4'
      array('BCA', '14'),
      array('BCH', '14'),
      array('BCK', '14'),
      array('BCO', '14'),
      array('BCQ', '14'),
      array('BCU', '14'),
      array('BCX', '148'),
      # C not before A, H, K, O, Q, U, X = '8'
      array('BCB', '181'),
      array('BCC', '18'),
      array('BCD', '182'),
      array('BCE', '18'),
      array('BCF', '183'),
      array('BCG', '184'),
      array('BCI', '18'),
      array('BCJ', '18'),
      array('BCL', '185'),
      array('BCM', '186'),
      array('BCN', '186'),
      array('BCP', '181'),
      array('BCR', '187'),
      array('BCS', '18'),
      array('BCT', '182'),
      array('BCV', '183'),
      array('BCW', '183'),
      array('BCY', '18'),
      array('BCZ', '18'),
    );
  }

  /**
   * @return array
   */
  public function charProvider()
  {
    return array(
        array('A', '0'),
        array('B', '1'),
        array('C', '4'),
        array('D', '2'),
        array('E', '0'),
        array('F', '3'),
        array('G', '4'),
        array('H', ''),
        array('I', '0'),
        array('J', '0'),
        array('K', '4'),
        array('L', '5'),
        array('M', '6'),
        array('N', '6'),
        array('O', '0'),
        array('P', '1'),
        array('Q', '4'),
        array('R', '7'),
        array('S', '8'),
        array('T', '2'),
        array('U', '0'),
        array('V', '3'),
        array('W', '3'),
        array('X', '48'),
        array('Y', '0'),
        array('Z', '8'),
    );
  }

  /**
   * @return array
   */
  public function dProvider()
  {
    return array(
        array('DA', '2'),
        array('DC', '8'),
        array('DB', '21'),
        array('DF', '23'),
        array('DG', '24'),
        array('DL', '25'),
        array('DM', '26'),
        array('DR', '27'),
        array('DX', '248'),
    );
  }

  /**
   * @return array
   */
  public function dtRuleProvider()
  {
    return array(
      # D not before C, S, Z = '2'
      array('DA', '2'),
      array('DB', '21'),
      array('DD', '2'),
      array('DE', '2'),
      array('DF', '23'),
      array('DF', '23'),
      array('DG', '24'),
      array('DH', '2'),
      array('DI', '2'),
      array('DJ', '2'),
      array('DK', '24'),
      array('DL', '25'),
      array('DM', '26'),
      array('DN', '26'),
      array('DO', '2'),
      array('DP', '21'),
      array('DQ', '24'),
      array('DR', '27'),
      array('DT', '2'),
      array('DU', '2'),
      array('DV', '23'),
      array('DW', '23'),
      array('DX', '248'),
      array('DY', '2'),
      # T not before C, S, Z = '2'
      array('TA', '2'),
      array('TB', '21'),
      array('TD', '2'),
      array('TE', '2'),
      array('TF', '23'),
      array('TF', '23'),
      array('TG', '24'),
      array('TH', '2'),
      array('TI', '2'),
      array('TJ', '2'),
      array('TK', '24'),
      array('TL', '25'),
      array('TM', '26'),
      array('TN', '26'),
      array('TO', '2'),
      array('TP', '21'),
      array('TQ', '24'),
      array('TR', '27'),
      array('TT', '2'),
      array('TU', '2'),
      array('TV', '23'),
      array('TW', '23'),
      array('TX', '248'),
      array('TY', '2'),
      # D before C, S, Z = '8'
      array('DC', '8'),
      array('DS', '8'),
      array('DZ', '8'),
      # T before C, S, Z = '8'
      array('TC', '8'),
      array('TS', '8'),
      array('TZ', '8'),
    );
  }

  /**
   * @return array
   */
  public function pProvider()
  {
    return array(
        array('PA', '1'),
        array('PH', '3'),
        array('PD', '12'),
        array('PF', '13'),
        array('PG', '14'),
        array('PL', '15'),
        array('PM', '16'),
        array('PR', '17'),
        array('PC', '18'),
        array('PX', '148'),
    );
  }

  /**
   * @return array
   */
  public function pRuleProvider()
  {
    return array(
      # P not before H = '1'
      array('PA', '1'),
      array('PB', '1'),
      array('PC', '18'),
      array('PD', '12'),
      array('PE', '1'),
      array('PF', '13'),
      array('PG', '14'),
      array('PG', '14'),
      array('PI', '1'),
      array('PJ', '1'),
      array('PK', '14'),
      array('PL', '15'),
      array('PM', '16'),
      array('PN', '16'),
      array('PO', '1'),
      array('PP', '1'),
      array('PQ', '14'),
      array('PR', '17'),
      array('PS', '18'),
      array('PT', '12'),
      array('PU', '1'),
      array('PV', '13'),
      array('PW', '13'),
      array('PX', '148'),
      array('PY', '1'),
      array('PZ', '18'),
      # P before H = '3'
      array('PH', '3'),
    );
  }

  /**
   * @return array
   */
  public function singleCharacterProvider()
  {
    return array(
      # A, E, I, J, O, U, Y = '0'
      array('A', '0'),
      array('E', '0'),
      array('I', '0'),
      array('J', '0'),
      array('O', '0'),
      array('U', '0'),
      array('Y', '0'),
      # H = ''
      array('H', ''),
      # B, P = '1'
      array('B', '1'),
      array('P', '1'),
      # D, T = '2'
      array('D', '2'),
      array('T', '2'),
      # F, V, W = '3'
      array('F', '3'),
      array('V', '3'),
      array('W', '3'),
      # G, K, Q = '4'
      array('G', '4'),
      array('K', '4'),
      array('Q', '4'),
      # C = '4'
      array('C', '4'),
      # X = '48'
      array('X', '48'),
      # L = '5',
      array('L', '5'),
      # M, N
      array('M', '6'),
      array('N', '6'),
      # R = '7'
      array('R', '7'),
      # S, Z = '8'
      array('S', '8'),
      array('Z', '8'),
    );
  }

  /**
   * @return array
   */
  public function tProvider()
  {
    return array(
        array('TA', '2'),
        array('TC', '8'),
        array('TB', '21'),
        array('TF', '23'),
        array('TG', '24'),
        array('TL', '25'),
        array('TM', '26'),
        array('TR', '27'),
        array('TX', '248'),
    );
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
    $testArray = array(
        'Müller Lüdenscheidt'         => array('Müller' => '657', 'Lüdenscheidt' => '52682'),
        'Müller-Lüdenscheidt'         => array('Müller-Lüdenscheidt' => '65752682'),
        "\n \t"                       => array(),
        "test \xc2\x88"               => array('test' => '282'),
        'Sind wir Düssel dorf'        => array('Sind' => '862', 'wir' => '37', 'Düssel' => '285', 'dorf' => '273'),
        'Sind wir Düssel dorf ?'      => array('Sind' => '862', 'wir' => '37', 'Düssel' => '285', 'dorf' => '273'),
        'Sind wir Düssel dorf?'       => array('Sind' => '862', 'wir' => '37', 'Düssel' => '285', 'dorf' => '273'),
        'Sind wir Düssel-dorf'        => array('Sind' => '862', 'wir' => '37', 'Düssel-dorf' => '285273'),
        'Ã ¤'                         => array('Ã' => '0'),
        'test'                        => array('test' => '282'),
        'text'                        => array('text' => '2482'),
        'Hauptfilter Feinstaub F9 für PF 1400' => array(
            'Hauptfilter' => '0123527',
            'Feinstaub' => '36821',
            'F' => '3',
            'für' => '37',
            'PF' => '13',
        ),
        'Ein Satz mit vielen Wortern' => array(
            'Ein'     => '06',
            'Satz'    => '8',
            'mit'     => '62',
            'vielen'  => '356',
            'Wortern' => '37276',
        ),
        '中 文 空 白' => array(
            '中' => '064',
            '文' => '06',
            '空' => '064',
            '白' => '0',
        ),

    );

    $phonetic = new Phonetic('de');
    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, $phonetic->phonetic_sentence($before, false, false), 'tested: ' . $before);
      }
    }
  }

  public function testGermanPhoneticWord()
  {
    $testArray = array(
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

    );

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
  public function wordsProvider()
  {
    return array(
        array('547', 'Holger'),
        array('35278273', 'Woltersdorf'),
        array('17863', 'Breschnew'),
        array('65752682', 'Müller-Lüdenscheidt'),
        array('3412', 'Wikipedia'),
    );
  }

  /**
   * @return array
   */
  public function xProvider()
  {
    return array(
        array('AX', '48'),
        array('IX', '048'),
        array('BX', '148'),
        array('DX', '248'),
        array('FX', '348'),
        array('LX', '548'),
        array('MX', '648'),
        array('RX', '748'),
        array('SX', '848'),
        array('XX', '4848'),
    );
  }

  /**
   * @return array
   */
  public function xRuleProvider()
  {
    return array(
      # X not after C, K, Q = '48'
      array('AX', '48'),
      array('BX', '148'),
      array('DX', '248'),
      array('EX', '48'),
      array('FX', '348'),
      array('GX', '48'),
      array('HX', '48'),
      array('IX', '048'),
      array('JX', '048'),
      array('LX', '548'),
      array('MX', '648'),
      array('NX', '648'),
      array('OX', '048'),
      array('PX', '148'),
      array('RX', '748'),
      array('SX', '848'),
      array('TX', '248'),
      array('UX', '048'),
      array('VX', '348'),
      array('WX', '348'),
      array('XX', '4848'),
      array('YX', '048'),
      array('ZX', '848'),
      # X after C, K, Q = '8'
      array('CX', '48'),
      array('KX', '48'),
      array('QX', '48'),
    );
  }
}

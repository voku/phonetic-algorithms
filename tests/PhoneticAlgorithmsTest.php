<?php

use voku\helper\Phonetic;

/**
 * Class PhoneticAlgorithmsTest
 */
class PhoneticAlgorithmsTest extends \PHPUnit_Framework_TestCase
{
  public function testPhoneticMatches()
  {
    $phonetic = new Phonetic('de');
    $tests = array(
        'Moelleken',  // '6546',
        'Mölleken',   // '6546',
        'Möleken',    // '6546',
        'Moeleken',   // '6546',
        'oder',       // '027',
        'was',        // '38',
        'Moellekenn', // '6546',
        'Moellecken', // '6546',
        'Möllecken',  // '6546',
        'Mölecken',   // '6546',
    );
    self::assertSame(
        array(
            'Mölecken'   => 'Moelleken',
            'Moellecken' => 'Moelleken',
            'Möllecken'  => 'Moelleken',
            'Moellekenn' => 'Moelleken',
            'Moeleken'   => 'Moelleken',
            'Möleken'    => 'Moelleken',
            'Mölleken'   => 'Moelleken',
            'Moelleken'  => 'Moelleken',
        ),
        $phonetic->phonetic_matches('Moelleken', $tests)
    );
  }

  public function testPhoneticMatchesSentence()
  {
    $phonetic = new Phonetic('de');
    $tests = array(
        123 => 'Ostern ganz fix: Schnelle Deko-Ideen selber machen',
        342 => 'Wäsche sortieren in 4 Schritten',
        621 => 'Saubere & glatte Wäsche dank Persil!',
        631 => 'Geld sparen beim Geschirrspülen in der Spülmaschine',
        311 => 'Sooo viel gratis: Die Henkel Familien-Aktion',
        312 => 'NEU: Der Sprühkopf für einfacheres Reinigen',
        313 => 'Das erste Mal: Wäsche färben',
        314 => 'Das erste Mal: Gardinen waschen',
        315 => 'Wäsche waschen & trocknen im Keller',
    );
    self::assertSame(
        array(
            315 => array(
                'Wasche'  => 'Wäsche',
                'troknen' => 'trocknen',
            ),
            342 => array(
                'Wasche' => 'Wäsche',
            ),
            621 => array(
                'Wasche' => 'Wäsche',
            ),
            313 => array(
                'Wasche' => 'Wäsche',
            ),
        ),
        $phonetic->phonetic_matches('Wasche troknen? Wie geht das?', $tests)
    );
  }

  public function testPhoneticSentence()
  {
    $phonetic = new Phonetic('de');
    self::assertSame(
        array('Moelleken' => '6546'),
        $phonetic->phonetic_sentence('Moelleken oder was?')
    );

    self::assertSame(
        array(
            'Moelleken' => '6546',
            'oder'      => '027',
            'was'       => '38',
        ),
        $phonetic->phonetic_sentence('Moelleken oder was?', false)
    );

    self::assertSame(
        array(
            'Moelleken' => '6546',
        ),
        $phonetic->phonetic_sentence('Moelleken oder was?', true)
    );

    self::assertSame(
        array(
            'Moelleken' => '6546',
        ),
        $phonetic->phonetic_sentence('Moelleken oder was?', false, 4)
    );
  }

  public function testPhoneticWord()
  {
    $phonetic = new Phonetic('de');
    self::assertSame('6546', $phonetic->phonetic_word('Moelleken'));
  }
}

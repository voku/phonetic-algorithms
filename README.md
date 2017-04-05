[![Build Status](https://travis-ci.org/voku/phonetic-algorithms.svg?branch=master)](https://travis-ci.org/voku/phonetic-algorithms)
[![Coverage Status](https://coveralls.io/repos/github/voku/phonetic-algorithms/badge.svg?branch=master)](https://coveralls.io/github/voku/phonetic-algorithms?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/voku/phonetic-algorithms/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/voku/phonetic-algorithms/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/6b5ba69c2fa640d6b3ce13e784e4cf64)](https://www.codacy.com/app/voku/phonetic-algorithms?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=voku/phonetic-algorithms&amp;utm_campaign=Badge_Grade)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0a9c0c3c-099f-44ab-b800-f1da0742d5af/mini.png)](https://insight.sensiolabs.com/projects/0a9c0c3c-099f-44ab-b800-f1da0742d5af)
[![Latest Stable Version](https://poser.pugx.org/voku/phonetic-algorithms/v/stable)](https://packagist.org/packages/voku/phonetic-algorithms) 
[![Total Downloads](https://poser.pugx.org/voku/phonetic-algorithms/downloads)](https://packagist.org/packages/voku/phonetic-algorithms) 
[![Latest Unstable Version](https://poser.pugx.org/voku/phonetic-algorithms/v/unstable)](https://packagist.org/packages/voku/phonetic-algorithms)
[![License](https://poser.pugx.org/voku/phonetic-algorithms/license)](https://packagist.org/packages/voku/phonetic-algorithms)

# Phonetic-Algorithms

## Description

- "PhoneticGerman"-Class: 

A phonetic algorithms for the german language via "Kölner Phonetik": [en.wikipedia.org/wiki/Cologne_phonetics](https://en.wikipedia.org/wiki/Cologne_phonetics)

- "PhoneticEnglish"-Class: 

A phonetic algorithms for the english language via "metaphone": [en.wikipedia.org/wiki/Metaphone](https://en.wikipedia.org/wiki/Metaphone)

- "PhoneticFrench"-Class:

A phonetic algorithms for the french language via "SOUNDEX FR": [www.roudoudou.com/phonetic.php](http://www.roudoudou.com/phonetic.php)

* [Installation](#installation)
* [Usage](#usage)
* [History](#history)

## Installation

1. Install and use [composer](https://getcomposer.org/doc/00-intro.md) in your project.
2. Require this package via composer:

```sh
composer require voku/phonetic-algorithms
```

## Usage

You the "phonetic_word"-method if you need a fuzzy-search for single words e.g. last-names or product-names.

```php
use voku\helper\Phonetic;

$words = array(
  'Moelleken',
  'Mölleken',
  'Möleken',
  'Moeleken',
  'Moellecken',
  'Möllecken',
  'Mölecken',
);
$phonetic = new Phonetic('de');
foreach ($words as $word) {
  $phonetic->phonetic_word($string); // '6546'
}
```

You can use the "phonetic_sentence"-method to process sentences.

```php
use voku\helper\Phonetic;

$string = 'Ein Satz mit vielen Wortern';
$phonetic = new Phonetic('de');
$phonetic->phonetic_sentence($string, (bool) false, (false|int) false); 

// [
//   'Ein' => '06', 
//   'Satz' => '8', 
//   'mit' => '62', 
//   'vielen' => '356', 
//   'Wortern' => '37276'
// ]
```

You can use the "phonetic_matches"-method to search for words in an array of words.

```php
use voku\helper\Phonetic;

$phonetic = new Phonetic('de');

$tests = array(
    'Moelleken',  // '6546',
    'Mölleken',   // '6546',
    'Möleken',    // '6546',
    'Moeleken',   // '6546',
    'oder',       // '027',
    'was',        // '38',
    'Moellecken', // '6546',
    'Möllecken',  // '6546',
    'Mölecken',   // '6546',
);

$phonetic->phonetic_matches('Moelleken', $tests);
    
// [
//   'Moelleken'  => 'Moelleken',
//   'Mölleken'   => 'Moelleken',
//   'Möleken'    => 'Moelleken',
//   'Moeleken'   => 'Moelleken',
//   'Moellecken' => 'Moelleken',
//   'Möllecken'  => 'Moelleken',
//   'Mölecken'   => 'Moelleken',
// ]
```

## History
See [CHANGELOG](CHANGELOG.md) for the full history of changes.

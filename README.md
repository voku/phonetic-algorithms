[![Build Status](https://travis-ci.org/voku/phonetic-algorithms.svg?branch=master)](https://travis-ci.org/voku/phonetic-algorithms)
[![Coverage Status](https://coveralls.io/repos/github/voku/phonetic-algorithms/badge.svg?branch=master)](https://coveralls.io/github/voku/phonetic-algorithms?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/voku/phonetic-algorithms/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/voku/phonetic-algorithms/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/c6e5213d2fc0421fa0923c992b6035c1)](https://www.codacy.com/app/voku/phonetic-algorithms?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=voku/phonetic-algorithms&amp;utm_campaign=Badge_Grade)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7841fd87-ea3e-4ce2-9be8-e0100fbc1c25/mini.png)](https://insight.sensiolabs.com/projects/7841fd87-ea3e-4ce2-9be8-e0100fbc1c25)[![Latest Stable Version](https://poser.pugx.org/voku/phonetic-algorithms/v/stable)](https://packagist.org/packages/voku/phonetic-algorithms) 
[![Total Downloads](https://poser.pugx.org/voku/phonetic-algorithms/downloads)](https://packagist.org/packages/voku/phonetic-algorithms) 
[![Latest Unstable Version](https://poser.pugx.org/voku/phonetic-algorithms/v/unstable)](https://packagist.org/packages/voku/phonetic-algorithms)
[![License](https://poser.pugx.org/voku/phonetic-algorithms/license)](https://packagist.org/packages/voku/phonetic-algorithms)

# Phonetic-Algorithms

## Description

- PhoneticAlgorithms::german_phonetic_word(): 

A good phonetic algorithms for the german language via "Kölner Phonetik": [en.wikipedia.org/wiki/Cologne_phonetics](https://en.wikipedia.org/wiki/Cologne_phonetics)

- PhoneticAlgorithms::english_phonetic_word(): 

A good phonetic algorithms for the english language via "metaphone": [en.wikipedia.org/wiki/Metaphone](https://en.wikipedia.org/wiki/Metaphone)


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

You the "*_phonetic_word"-method if you need a fuzzy-search for single words e.g. names.

```php
use voku\helper\PhoneticAlgorithms;

$words = array(
  'Moelleken',
  'Mölleken',
  'Möleken',
  'Moeleken',
  'Moellecken',
  'Möllecken',
  'Mölecken',
);
foreach ($words as $word) {
  PhoneticAlgorithms::german_phonetic_word($string); // '6546'
}
```

You can use the "*_phonetic_sentence"-method to process sentences.

```php
use voku\helper\PhoneticAlgorithms;

$string = 'Ein Satz mit vielen Wortern';
PhoneticAlgorithms::german_phonetic_sentence($string); // array('06', '8', '62', '356', '37276')
```

## History
See [CHANGELOG](CHANGELOG.md) for the full history of changes.

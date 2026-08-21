[![Build Status](https://github.com/voku/phonetic-algorithms/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/voku/phonetic-algorithms/actions)
[![Coverage Status](https://coveralls.io/repos/github/voku/phonetic-algorithms/badge.svg?branch=master)](https://coveralls.io/github/voku/phonetic-algorithms?branch=master)
[![Latest Stable Version](https://poser.pugx.org/voku/phonetic-algorithms/v/stable)](https://packagist.org/packages/voku/phonetic-algorithms) 
[![Total Downloads](https://poser.pugx.org/voku/phonetic-algorithms/downloads)](https://packagist.org/packages/voku/phonetic-algorithms) 
[![Latest Unstable Version](https://poser.pugx.org/voku/phonetic-algorithms/v/unstable)](https://packagist.org/packages/voku/phonetic-algorithms)
[![License](https://poser.pugx.org/voku/phonetic-algorithms/license)](https://packagist.org/packages/voku/phonetic-algorithms)

# Phonetic-Algorithms

## Description

Fuzzy searching for words that sound alike but are written differently.

| Code | Class                  | Algorithm                                                                                        | Key    |
| ---- | ---------------------- | ------------------------------------------------------------------------------------------------ | ------ |
| `de` | `PhoneticGerman`       | "Kölner Phonetik" ([Wikipedia](https://en.wikipedia.org/wiki/Cologne_phonetics))                    | digits |
| `en` | `PhoneticEnglish`      | "metaphone" ([Wikipedia](https://en.wikipedia.org/wiki/Metaphone)), via the native PHP function     | letters |
| `es` | `PhoneticSpanish`      | rule set documented in the class                                                                   | letters |
| `fr` | `PhoneticFrench`       | "SOUNDEX FR" ([roudoudou.com](http://www.roudoudou.com/phonetic.php))                               | letters |
| `it` | `PhoneticItalian`      | rule set documented in the class                                                                   | letters |
| `nl` | `PhoneticDutch`        | rule set documented in the class                                                                   | letters |
| `pl` | `PhoneticPolish`       | rule set documented in the class                                                                   | letters |
| `pt` | `PhoneticPortuguese`   | rule set documented in the class                                                                   | letters |

"Kölner Phonetik", "metaphone" and "SOUNDEX FR" are published algorithms and are
implemented as published.

The other languages have no single published standard, so they are **rule sets
that are documented in the class itself**: every class carries its complete
sound table in the docblock, and every row of that table is pinned by a test.
They are built for the mistakes people really make in that language, for example:

```php
(new Phonetic('es'))->phonetic_word('vaca');   // 'BAKA'
(new Phonetic('es'))->phonetic_word('baca');   // 'BAKA'   - "b" and "v" are one sound

(new Phonetic('nl'))->phonetic_word('Meijer'); // 'MYER'
(new Phonetic('nl'))->phonetic_word('Meyer');  // 'MYER'   - "ij", "ei" and "y" are one sound

(new Phonetic('pl'))->phonetic_word('Wałęsa'); // 'VALESA'
(new Phonetic('pl'))->phonetic_word('Walesa'); // 'VALESA' - a query without diacritics still matches
```

Codes from different languages are not comparable with each other: pick the
language of the data you are searching in.

* [Installation](#installation)
* [Usage](#usage)
* [Development](#development)
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

## Development

Coding-agent work in this repository runs through
[`voku/agent-loop`](https://github.com/voku/agent-loop): plan, approve,
implement, validate with recorded evidence, review, close. The board, the task
contracts and the run receipts live under `.agent-loop/`.

```sh
make agent_loop_install
bin/agent-loop init status
bin/agent-loop board summary
```

See [docs/agent-loop.md](docs/agent-loop.md) for the setup and
[docs/agent-loop-dogfood.md](docs/agent-loop-dogfood.md) for what the workflow
did and did not catch while these languages were added.

## History
See [CHANGELOG](CHANGELOG.md) for the full history of changes.

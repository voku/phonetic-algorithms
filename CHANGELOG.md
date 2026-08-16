# Change log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Fixed
- "PhoneticGerman": "C" after "R" is no longer coded as "8"

-> The "Kölner Phonetik" code table only names "S" and "Z" as the exception for
   "C" before "A, H, K, O, Q, U, X", so "R" was an extra rule that is not in the
   specification. This changes the code of words like "Kirche" ("478" -> "474")
   and therefore un-merges them from words like "Kirsche" ("478"). A phonetic
   index that was built with an older release has to be rebuilt.

## [5.0.0] - 2021-01-11
### Changed
- update "Portable UTF8" from v5 -> v6

## [4.0.0] - 2017-12-23
### Changed
- update "Portable UTF8" from v4 -> v5

-> this is a breaking change without API-changes - but the requirement from 
   "Portable UTF8" has been changed (it no longer requires all polyfills from Symfony)

## [3.0.2] - 2017-12-03
### Changed
- update "voku/stop-words"

## [3.0.1] - 2017-12-01
### Changed
- drop support for PHP < 7.0 v2 
- update phpunit-config

## [3.0.0] - 2017-11-13
### Changed
- drop support for PHP < 7.0
- use "strict_types"

## [2.1.0] - 2017-05-12
### Changed
- use stop-words from "voku/stop-words"

## [2.0.0] - 2017-04-05
### Changed
- re-write -> "object oriented"

## [1.0.0] - 2017-03-27
### Changed
- init 


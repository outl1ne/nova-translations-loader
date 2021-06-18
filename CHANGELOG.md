# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.2] - 18-06-2021

### Changed

- Bumped minimum Laravel version to `7.21` to ensure availability of `app()->getFallbackLocale()`

## [3.1.1] - 23-11-2020

### Changed

- Fix `->addLines` stopping Laravel from loading its own translations

## [3.1.0] - 20-11-2020

### Changed

- Rework Laravel translations loading so it merges both published and package translations

## [3.0.0] - 20-11-2020

### Changed

- Dropped PHP 7.1, Laravel 6 and Nova 2 support

## [2.0.4] - 23-11-2020

### Changed

- Translations loading fixes

## [2.0.3] - 18-11-2020

### Changed

- Fixed PHP 7.4 fn usage

## [2.0.2] - 18-11-2020

### Changed

- Load Laravel translations directly from JSON files
- Removed `__nova()` helper

## [2.0.1] - 17-11-2020

### Added

- Added `__nova()` translation function that fixes `__()` JSON loader shortcomings

## [2.0.0] - 03-11-2020

### Changed

- Fixed translations publishing
- Changed the logic from class with static functions to a trait

## [1.0.5] - 22-10-2020

### Changed

- Fixed PHP 8 support

## [1.0.4] - 22-10-2020

### Changed

- Translations are now loaded during `ServingNova` event to leave time for changing the locale

## [1.0.3] - 17-09-2020

### Added

- Translations are now also loaded into Laravel Translator

## [1.0.2] - 15-09-2020

### Changed

- Fixed missing import

## [1.0.1] - 15-09-2020

### Changed

- Fixed incorrect variable names
- Automatically trim right `/` off package translations directory

## [1.0.0] - 15-09-2020

### Added

- Initial release

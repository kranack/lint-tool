# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.3.0] - 2021-05-27

### Changed

- Change composer-cleanup-plugin dependency to use `dev-fix/composer-update` branch instead of last version (support Composer 2)
- Fix PHPStan error in HomebrewScanner

## [0.2.1] - 2020-06-23

### Changed

- Change php-parallel-lint dependency to use `php-parallel-lint/php-parallel-lint` instead of `grogy/php-parallel-lint` (marked as abandoned)
- Filter the executables scanned in error after the scan

## [0.2.0] - 2020-03-03

### Added

- No-local option for lint command. Can be used to skip all local PHP binaries for linting targeted files
- Full option for lint command. Can be used to force using all PHP binaries (even if another similar version has already been used)
- New test file for `OS` class

### Changed

- Require file now search for parent directory containing the composer.json
- The lint command now filters PHP binaries comparing their versions (skip similar versions). This behaviour can be overrided by the `full` option
- All the Scanner types are now stored in a class `Scanner_Type` in order to be used outside the `Environment` class
- Update README with all missing options for the `lint` command

## [0.1.0] - 2020-02-28

### Added

- install command (with force option), creating a conf file lising the available PHP executable paths
- lint command (with min option), linting PHP files in a folder
- create tools to check environment such as OS and PHP install types (Homebrew, Macports, Local)
- composer require file, to use the tool within a composer project
- travis and github CI configurations

[Unreleased]: https://github.com/kranack/lint-tool/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/kranack/lint-tool/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/kranack/lint-tool/releases/tag/v0.1.0
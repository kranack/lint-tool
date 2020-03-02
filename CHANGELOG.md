# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- Require file now search for parent directory containing the composer.json

## [0.1.0] - 2020-02-28

### Added

- install command (with force option), creating a conf file lising the available PHP executable paths
- lint command (with min option), linting PHP files in a folder
- create tools to check environment such as OS and PHP install types (Homebrew, Macports, Local)
- composer require file, to use the tool within a composer project
- travis and github CI configurations

[0.1.0]: https://github.com/kranack/lint-tool/releases/tag/v0.1.0
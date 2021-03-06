# Lint Tool

![Main workflow](https://github.com/kranack/lint-tool/workflows/Main%20workflow/badge.svg) [![Build Status](https://travis-ci.org/kranack/lint-tool.svg?branch=master)](https://travis-ci.org/kranack/lint-tool) ![GitHub tag (latest SemVer)](https://img.shields.io/github/v/tag/kranack/lint-tool)

## How it works

This lint tool use your PHP binaries to lint your sources files.
The first step to use it is to setup your environment by launching the `install` command. During the installation, it'll scan your computer searching for PHP binaries and save paths in your configuration.

This tool mainly supports [Homebrew], other tools may or may not be well supported.

## Install

Install composer dependencies with `composer install` and run `./lint-tools install`.

### Update existing installation

To update your configuration, run `./lint-tools install --force`.

### List cached PHP instances

To list your PHP instances cached during your last installation, run `./lint-tools install --list`

## Usage

After the install, you could run the lint command `./lint-tools lint [-m <min>] [--exclude] [--colors] [--full] [--no-local] <folder>` where folder is the directory containing PHP sources to lint.
The min option can be used to define the minimal PHP version to lint with. [Semantic Versioning][Semver] format is used.

## Documentation

- [Semantic Versioning][Semver]
- [Version constraint package used][VersionPackage]
- [Linting package used][LintPackage]

[Semver]: https://semver.org/
[VersionPackage]: https://github.com/phar-io/version#version-constraints
[LintPackage]: https://github.com/JakubOnderka/PHP-Parallel-Lint
[Homebrew]: https://brew.sh/

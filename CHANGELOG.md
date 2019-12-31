# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.5.0 - 2018-07-31

### Added

- Nothing.

### Changed

- [zfcampus/zf-rest#115](https://github.com/zfcampus/zf-rest/pull/115) modifies how the query whitelist is generated. If an input filter exists for a `GET` request,
  the input names will be merged with the whitelist.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.4.0 - 2018-05-02

### Added

- [zfcampus/zf-rest#107](https://github.com/zfcampus/zf-rest/pull/107) adds support for PHP 7.2.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zfcampus/zf-rest#107](https://github.com/zfcampus/zf-rest/pull/107) removes support for HHVM.

### Fixed

- Nothing.

## 1.3.3 - 2016-10-11

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Updates the `composer.json` to have a minimum supported api-tools-api-problem version
  of 1.2.2; this is necessary for the fixes in zfcampus/zf-rest#103 and zfcampus/zf-rest#105 to work correctly.

## 1.3.2 - 2016-10-11

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-rest#103](https://github.com/zfcampus/zf-rest/pull/103) and
  [zfcampus/zf-rest#105](https://github.com/zfcampus/zf-rest/pull/105) fix an issue with
  providing a `Throwable` in order to create an `ApiProblem` from within a
  `RestController`.

## 1.3.1 - 2016-07-12

### Added

- [zfcampus/zf-rest#100](https://github.com/zfcampus/zf-rest/pull/100) adds configuration to the
  `composer.json` to allow laminas-component-installer to auto-inject the
  `Laminas\ApiTools\Rest` module into application configuration during installation.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.3.0 - 2016-07-12

### Added

- [zfcampus/zf-rest#99](https://github.com/zfcampus/zf-rest/pull/99) adds support for v3
  releases of Laminas components, while retaining compatibility for v2
  releases.
- [zfcampus/zf-rest#96](https://github.com/zfcampus/zf-rest/pull/96) adds a `Content-Location`
  header to responses returned from `RestController::create()`, per
  [RFC 7231](https://tools.ietf.org/html/rfc7231#section-3.1.4.2).

### Deprecated

- Nothing.

### Removed

- [zfcampus/zf-rest#99](https://github.com/zfcampus/zf-rest/pull/99) removes support for PHP 5.5.

### Fixed

- [zfcampus/zf-rest#70](https://github.com/zfcampus/zf-rest/pull/70) updates how the
  `RestController` retrieves the identifier from `Laminas\ApiTools\Hal\Entity` instances to
  use the new `getId()` method introduced in api-tools-hal 1.4.
- [zfcampus/zf-rest#94](https://github.com/zfcampus/zf-rest/pull/94) updates the
  `RestController` to return Problem Details with a status of 400 if the
  page size requested by the client is below zero.

## 1.2.1 - 2016-07-12

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-rest#97](https://github.com/zfcampus/zf-rest/pull/97) fixes `Location` header
  generation in the `RestController::create()` method to only use the `href`
  property of the relational link; previously, if you'd defined additional
  properties, these were also incorrectly serialized in the generated link.

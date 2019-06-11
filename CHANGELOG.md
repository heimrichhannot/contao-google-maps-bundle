# Changelog
All notable changes to this project will be documented in this file.

## [1.1.0] - 2019-06-11

### Added
- new command parameters:
    - clean-before-migration
    - skip-unsupported-field-warnings

### Removed
- published from google maps entity

### Changed
- dlh migrate command now also imports overlay publish states
- dlh migrate command now adds a higher attention to overlay coordinates rather than addresses

## [1.0.2] - 2019-06-11

### Changed
- dlh migrate command name to match other heimrichhannot bundles (see README)

## [1.0.1] - 2019-06-06

### Fixed
- Migration command now properly sets `tl_google_map.type` to `base`

## [1.0.0] - 2019-06-05

### Fixed
- hide controls if not activated (`zoomControl`, `mapTypeControl`, `scaleControl`, `streetViewControl`, `rotateControl`, `fullscreenControl`)
- Twig `Extension` renderJs function did render css (typo) 

### Added
- responsive map configuration

# Changelog
All notable changes to this project will be documented in this file.

## [1.4.0] - 2019-12-02
- also consider googlemaps_apiKey config key when checking for api key (MapManager)
- removed symfony framework dependency
- some code enhancements

## [1.3.1] - 2019-08-30

### Fixed
- `$markerVariableMapping` bug
- api key bug

## [1.3.0] - 2019-07-02

### Added
- `ElevationService` to get the elevation data for coordinates

## [1.2.5] - 2019-06-19

### Fixed
- `responsive` datacontainer check on `tl_google_map_overlay` (set closed on demand)

## [1.2.4] - 2019-06-18

### Added
- add `tl_list_config_element.php` de language file

## [1.2.3] - 2019-06-14

### Added
- markerVariableMapping in overlay manager

## [1.2.2] - 2019-06-12

### Added
- possibility to override overlays in `MapManager::prepareMap()` method

## [1.2.1] - 2019-06-12

### Removed
- `autoOpen` flag since it has never been needed with the egeloen implementation

## [1.2.0] - 2019-06-12

### Added
- new events: `DlhMigrationModifyMapEvent`, `DlhMigrationModifyOverlayEvent`

### Fixed
- infowindow issue for click event in dlh migration command

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

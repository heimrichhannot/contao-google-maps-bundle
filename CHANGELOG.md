# Changelog
All notable changes to this project will be documented in this file.

## [2.10.0] - 2023-06-05
- Added: BeforeRenderApiEvent ([#28])
- Changed: reworked privacy center listener ([#28])

## [2.9.0] - 2023-01-31
- Added: polygon creation ([#24])
- Added: english translation ([#23])

## [2.8.2] - 2022-07-04
- Added: github issue template
- Fixed: migration always want to update schema ([#22])

## [2.8.1] - 2022-04-27
- Fixed: class loading issue in privacy center listener

## [2.8.0] - 2022-04-27
- Changed: moved privacy center integration to own listener
- Changed: added privacy center < 2.0 to conflicts

## [2.7.1] - 2022-04-14
- Fixed: missing param in ConfigElementType

## [2.7.0] - 2022-04-11
- Added: support for [entity finder](https://github.com/heimrichhannot/contao-utils-bundle/blob/master/docs/commands/entity_finder.md)
- Changed: raised heimrichhannot/contao-utils-bundle dependency to 2.213
- Fixed: ConfigElementType exception if reader bundle is not installed
- Fixed: removed travis config

## [2.6.0] - 2022-03-14
- Changed: minimum php dependency is now php 7.4
- Changed: updated coordinate SQL data types ([#20], [@qzminski])
- Changed: refactored MigrateDlhCommand to Symfony Command and removed some deprecations
- Fixed: MigrateDlhCommand not compatible with doctrine 3 ([#20], [@qzminski])
- Fixed: tl_page rootfallback palette not supported
- Fixed: warnings in php 8+

## [2.5.2] - 2022-03-14
- Fixed: initialize MapRendererListener in service container
- Fixed: twig dependency
- Fixed: warnings in php 8+
- Fixed: exception due missing patchwork/utf8 dependencies in contao 4.13 (replace calls with native php functions)

## [2.5.1] - 2022-02-01
- Fixed: readme

## [2.5.0] - 2022-02-01

- Added: support for [hofff/contao-consent-bridge](https://github.com/hofff/contao-consent-bridge), thanks @dmolineus!

## [2.4.0] - 2021-11-02
- Changed: some refactoring for contao 4.9
- Fixed: remaining template util usages
- Fixed: error when custom overlay icon anchor position not set
- Fixed: `map.stylesheet` listener was removed in 2.1.0
- Fixed: unnecessary container call

## [2.3.0] - 2021-10-29
- Changed: use twig support bundle to render map
- Fixed: template data not passed to maps renderer

## [2.2.2] - 2021-10-28
- Fixed: maps sizing mode css not working

## [2.2.1] - 2021-10-28
- Fixed: missing labels for user right legends

## [2.2.0] - 2021-10-28
- Changed: use GoogleMapsPrepareExternalItemEvent in list and reader config element ([#15])
- Deprecated: ListGoogleMapBeforeRenderEvent (use GoogleMapsPrepareExternalItemEvent instead) ([#15])
- Deprecated: ReaderGoogleMapBeforeRenderEvent (use GoogleMapsPrepareExternalItemEvent instead) ([#15])

## [2.1.1] - 2021-10-27
- Fixed: return values in GoogleMapsPrepareExternalItemEvent

## [2.1.0] - 2021-10-27
- Added: option to render [list](https://github.com/heimrichhannot/contao-list-bundle) as google map ([#14])
- Added: GoogleMapsPrepareExternalItemEvent to customize Marker data from external sources (currently only implemented for Lists rendering added in this version)
- Fixed: uneven script inclusion for consent bars
- Fixed: marker click event info window option not working with empty offset fields

> This version adds functionality previously provided by contao-google-maps-list-bundle. 
> Please uninstall contao-google-maps-list-bundle bundle in order to update to this version as they are not compatible.
> You need to update your list configs as the `HeimrichHannot\GoogleMapsListBundle\DefaultList` and `HeimrichHannot\GoogleMapsListBundle\DefaultItem` classes 
> are no longer necessary and exist.

## [2.0.0] - 2021-09-27

- Added: support for multiple maps on one page
- Added: dry-run option to migration command
- Added: content element and frontend module migration to migration command
- Added: generic BeforeRenderMapEvent
- Added: default values for aspect ratio (16:9)
- Added: kml support ([#7], thanks to [@rabaus])
- Added: user better error message if no api key is set ([#8])
- Added: php8 support ([#10], thanks to [@rabaus])
- [BREAKING] Removed: support for contao 4.4 and symfony 3 (thanks [@rabaus]!)
- [BREAKING] Changed: DlhMigrationModifyMapEvent legacy map type to object
- [BREAKING] Changed: DlhMigrationModifyOverlayEvent legacy map and legacyOverlay type to object
- [BREAKING] Changed: removed mapGoogleJs variable from map template
- [BREAKING] Changed: renamed Bundle class to HeimrichHannotGoogleMapsBundle to reflect coding standards
- Changed: migration command to not require installed delahaye/dlh_googlemaps module
- Changed: refactored GoogleMapListConfigElementType to implement ListConfigElementTypeInterface
- Changed: refactored GoogleMapReaderConfigElementType to implement ReaderConfigElementTypeInterface
- Changed: refactored HookListener to ReplaceInsertTagsListener
- Changed: enhanced warning MapManager::setCenter()
- Changed: removed library overrides
- Fixed: some deprecation notices
- Fixed: `composer.json` twig dependency (thanks to fritzmg)
- Fixed: `EventDispatcher` interface
- Fixed: insert tag inclusion
- Fixed: decoding of styles ([#11], thanks to [@rabaus])
- Added: symfony depencies


[#28]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/28
[#24]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/24
[#23]: https://github.com/heimrichhannot/contao-google-maps-bundle/issues/23
[#22]: https://github.com/heimrichhannot/contao-google-maps-bundle/issues/22
[#20]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/20
[#15]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/15
[#14]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/14
[#11]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/11
[#10]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/10
[#8]: https://github.com/heimrichhannot/contao-google-maps-bundle/issues/8
[#7]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/7

[@rabaus]: https://github.com/rabauss
[@qzminski]: https://github.com/qzminski

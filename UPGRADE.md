# Upgrade notices

## 1.x to 2.x

* Renamed Bundle class from `HeimrichHannotContaoGoogleMapsBundle` to `HeimrichHannotGoogleMapsBundle`. You may need to update loadAfter setup, twig paths and file paths (public folder)
* DlhMigrationModifyMapEvent: legacyMap parameter is now type stdClass instead of Model
* DlhMigrationModifyOverlayEvent: legacyOverlay and legacyMap parameters are now type stdClass instead of Model
* Templates:
    * gmap_map template: mapGoogleJs variable removed. Update your custom templates accordingly
    
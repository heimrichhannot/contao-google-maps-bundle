# Developer documentation

## PHP Events

| Name                               | Description                                                               |
|------------------------------------|---------------------------------------------------------------------------|
| BeforeRenderApiEvent               | Before render the google maps api                                         |
| BeforeRenderMapEvent               | Dispatched before a map is rendered.                                      |
| DlhMigrationModifyMapEvent         | Triggered just before a the migrated map is being saved                   |
| DlhMigrationModifyOverlayEvent     | Triggered just before a the migrated overlay is being saved               |
| ListGoogleMapBeforeRenderEvent     | Dispatched before head over data to map renderer in list config element   |
| ReaderGoogleMapBeforeRenderEvent   | Dispatched before head over data to map renderer in reader config element |
| GoogleMapsPrepareExternalItemEvent | Map external item on overlay object (see readme for an example)           |
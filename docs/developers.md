# Developer documentation

## PHP Events

| Name                               | Description                                                               |
|------------------------------------|---------------------------------------------------------------------------|
| BeforeRenderApiEvent               | Before render the google maps api                                         |
| BeforeRenderMapEvent               | Dispatched before a map is rendered.                                      |
| DlhMigrationModifyMapEvent         | Triggered just before a the migrated map is being saved                   |
| DlhMigrationModifyOverlayEvent     | Triggered just before a the migrated overlay is being saved               |
| GoogleMapsPrepareExternalItemEvent | Map external item on overlay object (see readme for an example)           |
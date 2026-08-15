/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

/**
 * Styling and interaction for GeoJSON data layers, replacing what KmlLayer
 * rendered from the KML file itself.
 *
 * Feature properties follow the simplestyle spec
 * (https://github.com/mapbox/simplestyle-spec); features without their own
 * style fall back to the overlay settings.
 */
(function (window, document) {
    'use strict';

    var HUH = (window.HuhGoogleMaps = window.HuhGoogleMaps || {});

    /** Feature properties mapped to Google style options; first match wins. */
    var STYLE_PROPERTIES = {
        strokeColor: ['stroke', 'stroke-color'],
        strokeWeight: ['stroke-width', 'stroke-weight'],
        strokeOpacity: ['stroke-opacity'],
        fillColor: ['fill', 'fill-color'],
        fillOpacity: ['fill-opacity'],
        zIndex: ['zIndex', 'z-index'],
    };

    var TITLE_PROPERTIES = ['name', 'title', 'Name', 'NAME'];

    var DESCRIPTION_PROPERTIES = ['description', 'Beschreibung', 'desc'];

    /** Never shown in the property table. */
    var HIDDEN_PROPERTIES = [
        'stroke', 'stroke-color', 'stroke-width', 'stroke-weight',
        'stroke-opacity', 'fill', 'fill-color', 'fill-opacity',
        'icon', 'icon-scale', 'marker-color', 'marker-size', 'marker-symbol',
        'zIndex', 'z-index', 'styleUrl', 'styleHash',
        // Structural: where a feature sits in the source file.
        'folder', 'folderPath',
    ];

    function firstProperty(feature, names) {
        for (var i = 0; i < names.length; i++) {
            var value = feature.getProperty(names[i]);

            if (value !== undefined && value !== null && value !== '') {
                return value;
            }
        }

        return null;
    }

    function escapeHtml(value) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(String(value)));

        return div.innerHTML;
    }

    /** Converted descriptions carry plain-text URLs; make them clickable. */
    function linkify(text) {
        return escapeHtml(text).replace(
            /(https?:\/\/[^\s<]+)/g,
            '<a href="$1" target="_blank" rel="noopener">$1</a>'
        );
    }

    /** Builds the style for a single feature, over the configured fallback. */
    function buildStyle(feature, config) {
        var style = {};
        var key;

        for (key in config.style) {
            if (Object.prototype.hasOwnProperty.call(config.style, key)) {
                style[key] = config.style[key];
            }
        }

        if (config.useFeatureStyles) {
            for (key in STYLE_PROPERTIES) {
                if (!Object.prototype.hasOwnProperty.call(STYLE_PROPERTIES, key)) {
                    continue;
                }

                var value = firstProperty(feature, STYLE_PROPERTIES[key]);

                if (value !== null) {
                    style[key] = value;
                }
            }

            var icon = feature.getProperty('icon');

            if (icon) {
                style.icon = icon;
            }
        }

        return style;
    }

    /**
     * Composes the info window markup, or null when the feature carries
     * nothing worth showing.
     */
    function buildContent(feature) {
        var title = firstProperty(feature, TITLE_PROPERTIES);
        var description = firstProperty(feature, DESCRIPTION_PROPERTIES);
        var rows = [];

        feature.forEachProperty(function (value, name) {
            if (HIDDEN_PROPERTIES.indexOf(name) !== -1) {
                return;
            }

            if (TITLE_PROPERTIES.indexOf(name) !== -1 || DESCRIPTION_PROPERTIES.indexOf(name) !== -1) {
                return;
            }

            if (value === undefined || value === null || value === '') {
                return;
            }

            rows.push(
                '<tr><th scope="row">' + escapeHtml(name) + '</th>'
                + '<td>' + linkify(value) + '</td></tr>'
            );
        });

        if (!title && !description && !rows.length) {
            return null;
        }

        var html = '<div class="huh-geojson-infowindow">';

        if (title) {
            html += '<h3>' + escapeHtml(title) + '</h3>';
        }

        if (description) {
            html += '<div class="huh-geojson-infowindow__text">'
                + linkify(description).replace(/\n/g, '<br>')
                + '</div>';
        }

        if (rows.length) {
            html += '<table class="huh-geojson-infowindow__properties">'
                + rows.join('')
                + '</table>';
        }

        return html + '</div>';
    }

    /** Extends the bounds by every position in a geometry, at any depth. */
    function extendBounds(bounds, geometry) {
        if (!geometry) {
            return;
        }

        if (typeof geometry.forEachLatLng === 'function') {
            geometry.forEachLatLng(function (latLng) {
                bounds.extend(latLng);
            });
        }
    }

    /**
     * @param {google.maps.Map} map
     * @param {Object} config the huhOverlay options emitted by the overlay
     */
    HUH.initGeoJsonLayer = function (map, config) {
        if (!map || !config) {
            return;
        }

        config.style = config.style || {};

        var data = map.data;

        data.setStyle(function (feature) {
            return buildStyle(feature, config);
        });

        if (config.clickable) {
            if (!HUH._infoWindow) {
                HUH._infoWindow = new google.maps.InfoWindow();
            }

            data.addListener('click', function (event) {
                var content = buildContent(event.feature);

                if (!content) {
                    return;
                }

                HUH._infoWindow.setContent(content);
                // Lines and polygons have no single position of their own.
                HUH._infoWindow.setPosition(event.latLng);
                HUH._infoWindow.open({ map: map });
            });
        }

        if (config.fitBounds) {
            var bounds = new google.maps.LatLngBounds();
            var pending = false;

            var apply = function () {
                if (pending) {
                    return;
                }

                pending = true;

                window.setTimeout(function () {
                    pending = false;

                    if (!bounds.isEmpty()) {
                        map.fitBounds(bounds);
                    }
                }, 0);
            };

            // Features may already have arrived by the time this runs.
            data.forEach(function (feature) {
                extendBounds(bounds, feature.getGeometry());
            });

            data.addListener('addfeature', function (event) {
                extendBounds(bounds, event.feature.getGeometry());
                apply();
            });

            apply();
        }
    };
})(window, document);

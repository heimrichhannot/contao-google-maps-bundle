<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Heimrich & Hannot GmbH.
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Util;

/**
 * Converts KML to GeoJSON.
 *
 * GeoJSON has no style specification, so <Style>/<StyleMap> definitions are
 * flattened into simplestyle feature properties
 * (https://github.com/mapbox/simplestyle-spec), which the GeoJSON layer reads
 * back when rendering.
 */
class KmlConverter
{
    private array $externalIcons = [];

    private array $rewrittenIcons = [];

    /**
     * @param string[] $localIconHosts  hosts whose icon URLs are rewritten to
     *                                  $iconBase, e.g. after mirroring them
     * @param string[] $googleIconHosts hosts whose icons are looked up by
     *                                  basename below $iconBase/$googleIconDir
     */
    public function __construct(
        private readonly string $iconBase = '',
        private readonly array $localIconHosts = [],
        private readonly array $googleIconHosts = ['maps.google.com', 'maps.gstatic.com', 'kh.google.com'],
        private readonly string $googleIconDir = 'google',
        private readonly int $precision = 6,
        private readonly bool $keepFolders = false,
        private readonly ?string $iconRoot = null,
    ) {
    }

    /**
     * @return array{type: string, name?: string, features: array}
     *
     * @throws \RuntimeException when the file cannot be parsed
     */
    public function convertFile(string $file): array
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_file($file);
        libxml_use_internal_errors($previous);

        if (false === $xml) {
            throw new \RuntimeException('not valid XML');
        }

        return $this->convert($xml);
    }

    /**
     * @return array{type: string, name?: string, features: array}
     */
    public function convert(\SimpleXMLElement $xml): array
    {
        $styles = $this->collectStyles($xml);
        $features = [];

        // local-name() so the queries work regardless of namespace prefixes.
        foreach ($xml->xpath('//*[local-name()="Placemark"]') ?: [] as $placemark) {
            $geometry = $this->buildGeometry($placemark);

            if (null === $geometry) {
                continue;
            }

            $features[] = [
                'type' => 'Feature',
                'properties' => $this->buildProperties($placemark, $styles),
                'geometry' => $geometry,
            ];
        }

        $geoJson = [
            'type' => 'FeatureCollection',
        ];
        $name = $xml->xpath('//*[local-name()="Document"]/*[local-name()="name"]')[0] ?? null;

        if (null !== $name && '' !== trim((string) $name)) {
            $geoJson['name'] = trim((string) $name);
        }

        $geoJson['features'] = $features;

        return $geoJson;
    }

    /**
     * External icon URLs left untouched — http:// ones get blocked on a
     * HTTPS page, so they are worth reporting.
     *
     * @return string[]
     */
    public function getExternalIcons(): array
    {
        return array_keys($this->externalIcons);
    }

    /**
     * Rewritten icon paths that do not exist below the icon root.
     *
     * @return string[]
     */
    public function getMissingIcons(): array
    {
        return array_keys(array_filter(
            $this->rewrittenIcons,
            fn (string $path): bool => null !== $this->iconRoot
                && !file_exists(rtrim($this->iconRoot, '/').'/'.ltrim($path, '/')),
        ));
    }

    /**
     * KML colours are AABBGGRR — alpha first, then *reversed* RGB. Getting
     * this wrong silently swaps red and blue.
     *
     * @return array{0: string, 1: float} hex colour and opacity
     */
    public function kmlColorToHex(string $kmlColor): array
    {
        $kmlColor = ltrim(trim($kmlColor), '#');

        if (8 === \strlen($kmlColor)) {
            $alpha = hexdec(substr($kmlColor, 0, 2)) / 255;
            $bb = substr($kmlColor, 2, 2);
            $gg = substr($kmlColor, 4, 2);
            $rr = substr($kmlColor, 6, 2);
        } elseif (6 === \strlen($kmlColor)) {
            // Some editors emit BBGGRR without alpha.
            $alpha = 1.0;
            $bb = substr($kmlColor, 0, 2);
            $gg = substr($kmlColor, 2, 2);
            $rr = substr($kmlColor, 4, 2);
        } else {
            return ['#000000', 1.0];
        }

        return ['#'.strtolower($rr.$gg.$bb), round($alpha, 2)];
    }

    /**
     * @return array{styles: array<string, array>, maps: array<string, string>}
     */
    private function collectStyles(\SimpleXMLElement $xml): array
    {
        $styles = [];
        $maps = [];

        foreach ($xml->xpath('//*[local-name()="Style"][@id]') ?: [] as $style) {
            $styles[(string) $style['id']] = $this->buildStyle($style);
        }

        // Only the "normal" branch is meaningful for a static render.
        foreach ($xml->xpath('//*[local-name()="StyleMap"][@id]') ?: [] as $styleMap) {
            foreach ($styleMap->Pair as $pair) {
                if ('normal' === (string) $pair->key) {
                    $maps[(string) $styleMap['id']] = ltrim((string) $pair->styleUrl, '#');

                    break;
                }
            }
        }

        return [
            'styles' => $styles,
            'maps' => $maps,
        ];
    }

    private function buildStyle(\SimpleXMLElement $style): array
    {
        $properties = [];

        if (isset($style->LineStyle)) {
            if (isset($style->LineStyle->color)) {
                [$hex, $opacity] = $this->kmlColorToHex((string) $style->LineStyle->color);
                $properties['stroke'] = $hex;
                $properties['stroke-opacity'] = $opacity;
            }

            if (isset($style->LineStyle->width)) {
                $properties['stroke-width'] = (float) $style->LineStyle->width;
            }
        }

        if (isset($style->PolyStyle)) {
            if (isset($style->PolyStyle->color)) {
                [$hex, $opacity] = $this->kmlColorToHex((string) $style->PolyStyle->color);
                $properties['fill'] = $hex;
                $properties['fill-opacity'] = $opacity;
            }

            // <fill>0</fill> means outline only.
            if (isset($style->PolyStyle->fill) && '0' === (string) $style->PolyStyle->fill) {
                $properties['fill-opacity'] = 0.0;
            }
        }

        if (isset($style->IconStyle)) {
            if (isset($style->IconStyle->Icon->href)) {
                $properties['icon'] = $this->rewriteIconHref((string) $style->IconStyle->Icon->href);
            }

            if (isset($style->IconStyle->scale)) {
                $properties['icon-scale'] = (float) $style->IconStyle->scale;
            }

            if (isset($style->IconStyle->color)) {
                [$hex] = $this->kmlColorToHex((string) $style->IconStyle->color);
                $properties['marker-color'] = $hex;
            }
        }

        return $properties;
    }

    /**
     * Resolves a styleUrl through any number of StyleMap indirections.
     */
    private function resolveStyle(string $styleUrl, array $styleData): array
    {
        $id = ltrim(trim($styleUrl), '#');

        // Guard against a cyclic document.
        for ($hops = 0; $hops < 10; ++$hops) {
            if (isset($styleData['styles'][$id])) {
                return $styleData['styles'][$id];
            }

            if (!isset($styleData['maps'][$id])) {
                break;
            }

            $id = $styleData['maps'][$id];
        }

        return [];
    }

    private function buildProperties(\SimpleXMLElement $placemark, array $styles): array
    {
        $properties = [];

        if (isset($placemark->name)) {
            $properties['name'] = trim((string) $placemark->name);
        }

        if (isset($placemark->description)) {
            $description = trim((string) $placemark->description);

            if ('' !== $description) {
                $properties['description'] = $description;
            }
        }

        if ($this->keepFolders) {
            $folders = [];

            foreach ($placemark->xpath('ancestor::*[local-name()="Folder"]/*[local-name()="name"]') ?: [] as $folder) {
                $folders[] = trim((string) $folder);
            }

            if ($folders) {
                $properties['folder'] = end($folders);

                if (\count($folders) > 1) {
                    $properties['folderPath'] = implode(' / ', $folders);
                }
            }
        }

        foreach ($placemark->xpath('.//*[local-name()="Data"]') ?: [] as $data) {
            $name = trim((string) $data['name']);
            $value = trim((string) ($data->value ?? ''));

            if ('' === $name || '' === $value || isset($properties[$name])) {
                continue;
            }

            // ExtendedData can carry icon URLs of its own.
            if (preg_match('#\.(png|jpe?g|gif|svg|webp)$#i', $value)) {
                $value = $this->rewriteIconHref($value);
            }

            $properties[$name] = $value;
        }

        if (isset($placemark->styleUrl)) {
            $properties = array_merge($properties, $this->resolveStyle((string) $placemark->styleUrl, $styles));
        }

        return $properties;
    }

    /**
     * Rewrites icon URLs to local paths, so a HTTPS page no longer depends
     * on blocked http:// resources.
     */
    private function rewriteIconHref(string $href): string
    {
        $href = trim($href);

        if ('' === $href) {
            return $href;
        }

        $host = parse_url($href, PHP_URL_HOST);

        if (null === $host || false === $host) {
            return $href;
        }

        $host = strtolower($host);

        if ('' !== $this->iconBase && \in_array($host, array_map(strtolower(...), $this->localIconHosts), true)) {
            $path = ltrim(parse_url($href, PHP_URL_PATH) ?: '', '/');
            // Prefix only: the segment may legitimately appear mid-path.
            $path = preg_replace('#^'.preg_quote(basename($this->iconBase), '#').'/#', '', $path);

            return $this->rewrittenIcons[$this->iconBase.'/'.$path] = $this->iconBase.'/'.$path;
        }

        // Mirrored by basename: one icon is referenced through several URL
        // prefixes, and kh.google.com puts the filename in the query string.
        if ('' !== $this->iconBase && \in_array($host, array_map(strtolower(...), $this->googleIconHosts), true)) {
            $candidate = parse_url($href, PHP_URL_PATH) ?: '';

            if (!str_ends_with(strtolower($candidate), '.png')) {
                $candidate = parse_url($href, PHP_URL_QUERY) ?: '';
            }

            $basename = basename($candidate);

            if ('' !== $basename) {
                $local = $this->iconBase.'/'.$this->googleIconDir.'/'.$basename;

                if (null === $this->iconRoot || file_exists(rtrim($this->iconRoot, '/').'/'.ltrim($local, '/'))) {
                    return $this->rewrittenIcons[$local] = $local;
                }
            }
        }

        $this->externalIcons[$href] = $href;

        return $href;
    }

    /**
     * KML is "lon,lat[,alt]" — already GeoJSON's axis order; altitude is
     * dropped as noise for a 2D map.
     *
     * @return array<int, array{0: float, 1: float}>
     */
    private function parseCoordinates(string $raw): array
    {
        $positions = [];

        foreach (preg_split('/\s+/', trim($raw), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $tuple) {
            $parts = explode(',', $tuple);

            if (\count($parts) < 2) {
                continue;
            }

            $lon = round((float) $parts[0], $this->precision);
            $lat = round((float) $parts[1], $this->precision);

            // Guard against the truncated tuples some editors leave behind.
            if ($lon < -180 || $lon > 180 || $lat < -90 || $lat > 90) {
                continue;
            }

            $positions[] = [$lon, $lat];
        }

        return $positions;
    }

    private function buildGeometry(\SimpleXMLElement $placemark): ?array
    {
        if (isset($placemark->MultiGeometry)) {
            $geometries = [];

            foreach ($placemark->MultiGeometry->children() as $child) {
                $wrapper = new \SimpleXMLElement('<Placemark/>');
                $this->appendXml($wrapper, $child);

                if (null !== ($geometry = $this->buildGeometry($wrapper))) {
                    $geometries[] = $geometry;
                }
            }

            if (!$geometries) {
                return null;
            }

            if (1 === \count($geometries)) {
                return $geometries[0];
            }

            return [
                'type' => 'GeometryCollection',
                'geometries' => $geometries,
            ];
        }

        if (isset($placemark->Point->coordinates)) {
            $positions = $this->parseCoordinates((string) $placemark->Point->coordinates);

            return $positions ? [
                'type' => 'Point',
                'coordinates' => $positions[0],
            ] : null;
        }

        if (isset($placemark->LineString->coordinates)) {
            $positions = $this->parseCoordinates((string) $placemark->LineString->coordinates);

            return \count($positions) >= 2 ? [
                'type' => 'LineString',
                'coordinates' => $positions,
            ] : null;
        }

        if (isset($placemark->Polygon)) {
            return $this->buildPolygon($placemark->Polygon);
        }

        return null;
    }

    private function buildPolygon(\SimpleXMLElement $polygon): ?array
    {
        $rings = [];
        $outer = $polygon->outerBoundaryIs->LinearRing->coordinates ?? null;

        if (null !== $outer) {
            $ring = $this->closeRing($this->parseCoordinates((string) $outer));

            if (\count($ring) >= 4) {
                $rings[] = $ring;
            }
        }

        foreach ($polygon->innerBoundaryIs ?? [] as $inner) {
            if (!isset($inner->LinearRing->coordinates)) {
                continue;
            }

            $ring = $this->closeRing($this->parseCoordinates((string) $inner->LinearRing->coordinates));

            if (\count($ring) >= 4) {
                $rings[] = $ring;
            }
        }

        return $rings ? [
            'type' => 'Polygon',
            'coordinates' => $rings,
        ] : null;
    }

    /**
     * GeoJSON requires a linear ring to repeat its first position last.
     */
    private function closeRing(array $positions): array
    {
        if (\count($positions) >= 3 && $positions[0] !== $positions[\count($positions) - 1]) {
            $positions[] = $positions[0];
        }

        return $positions;
    }

    private function appendXml(\SimpleXMLElement $parent, \SimpleXMLElement $child): void
    {
        $node = $parent->addChild($child->getName(), htmlspecialchars((string) $child));

        foreach ($child->attributes() ?: [] as $name => $value) {
            $node->addAttribute($name, (string) $value);
        }

        foreach ($child->children() as $grandChild) {
            $this->appendXml($node, $grandChild);
        }
    }
}

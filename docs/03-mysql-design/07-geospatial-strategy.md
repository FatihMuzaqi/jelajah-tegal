# Geospatial Strategy

## Decision

Do not copy PostGIS trigger/functions. Use a dedicated catalog_locations row only when a catalog entity has valid coordinates:

- location POINT NOT NULL SRID 4326;
- latitude DECIMAL(10,7) NOT NULL;
- longitude DECIMAL(10,7) NOT NULL;
- SPATIAL INDEX(location);
- CHECK latitude between -90 and 90, longitude between -180 and 180.

Separating the row allows MySQL spatial index requirement for NOT NULL while catalog entries without coordinates remain valid by having no location row.

## Coordinate convention

Application canonical input is latitude, longitude. MySQL POINT is constructed in longitude, latitude order and explicitly assigned SRID 4326. A single tested factory/value object must perform conversion to avoid axis reversal. Latitude/longitude duplicate columns are fallback/display/export and must be written in the same Action as POINT.

## Nearest search

Conceptual query flow:

1. Validate user latitude/longitude and maximum radius.
2. Build SRID 4326 query point.
3. Use bounding-box predicates to reduce candidates where beneficial.
4. Filter catalog_entities by service_type, published status, enabled Mitra feature, category/region.
5. Calculate ST_Distance_Sphere(location, query point) in meters.
6. Restrict distance <= radius, order ascending, use stable ID tie-breaker, limit page size.

Exact MySQL axis-order option and function behavior must be integration-tested against known Indonesian coordinates before migration.

## Spatial index usage caveat

ST_Distance_Sphere alone may not use SPATIAL INDEX efficiently for all plans. Use an MBR bounding polygon or numeric latitude/longitude range prefilter, then exact spherical distance. Verify with EXPLAIN ANALYZE at realistic density. Region/category/status B-tree filters may be more selective than spatial index in small datasets.

## Fallback

If spatial capability is unavailable in a development/test driver, latitude/longitude bounding box plus Haversine calculation may support non-production behavior. Production and integration tests use MySQL 8 spatial functions. SQLite behavior is not accepted as proof.

## Precision and migration

Legacy Float coordinates are parsed, range-validated, and checked for zero/swap/outliers. Invalid coordinates go to migration quarantine; no POINT is created. Legacy PostGIS geography/geometry values, where present, are exported to WGS84 longitude/latitude and reconciled with scalar columns. No trigger is copied.

## Future regions

Region polygons/geofencing are not V1. regions remains administrative hierarchy. If polygon search becomes a product requirement, add a separately reviewed geometry table with SRID, source/license/version, validity checks, and spatial index.


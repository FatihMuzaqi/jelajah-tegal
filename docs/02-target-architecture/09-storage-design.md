# Storage Design

## Abstraction

Semua file memakai Laravel Storage/Flysystem. Business code references logical disks and media record, not R2-specific URL. Target supports:

- local disk untuk development/test;
- S3-compatible disk untuk production, termasuk Cloudflare R2;
- private and public visibility policies independent of provider.

## Media registry

media_assets menyimpan ULID, disk, object key, owner user atau mitra, purpose, original name, MIME detected, bytes, checksum, visibility, status, timestamps, and optional safe metadata. Domain models relate through FK/pivot. URL bukan source of truth dan tidak disimpan sebagai permanent public credential.

## Storage classes

| Class | Visibility | Examples | Delivery |
| --- | --- | --- | --- |
| Public catalog | Public/CDN where approved | Destination/event/banner image | CDN/storage URL generated |
| Private user | Private | Avatar original if required, renter docs V2 | Authorized temporary URL/controller |
| Compliance | Strict private | KYC, bank proof | Short signed URL after policy + audit read |
| Generated | Controlled | QR render, thumbnails, exports | Reconstructable or expiring download |

QR security depends on opaque token, not obscurity of image path. QR containing live token should be authorized/private where possible.

## Direct upload flow

1. Authenticated user requests upload intent with tenant, purpose, MIME, size.
2. Policy and feature checks run; server creates pending media row/object key.
3. Server returns short-lived presigned upload URL and required headers.
4. Browser uploads directly to R2/S3-compatible storage.
5. Browser calls finalize endpoint; server HEAD-checks object, MIME/size/checksum rules.
6. Media becomes ready and may be attached through authorized Action.
7. Abandoned pending objects are removed by scheduler.

Server-proxied upload remains option for small/private files or providers without presign.

## Security controls

- Generate object keys; ignore client paths.
- Allow-list MIME/extension by purpose and detect server-side.
- Size/dimension/page limits; virus scanning hook for compliance documents.
- Prevent SVG/HTML active content on public origin unless sanitized/forced download.
- Separate public and private bucket/prefix policy.
- No credentials or private URLs in logs/audit.
- KYC read requires permission, reason/context, short expiry, and audit.

## Ownership and attachment

Upload owner must match domain owner at attachment. Tenant A cannot attach Tenant B media. A ready asset can be attached only to allow-listed model/purpose. Attachment count and primary/sort order use database relations, not JSON arrays.

## Lifecycle

Delete aggregate detaches media but does not immediately delete shared/referenced object. Media cleanup verifies zero references, retention/legal hold, and grace period, then marks deletion intent and retries object deletion idempotently. Database record remains failed/retryable if provider deletion fails.

## Backup and migration

Database backup alone is insufficient. Object inventory/checksum and restore procedure are part of backup. Legacy external URLs are imported only after ownership/license/reachability checks; otherwise quarantine. Provider migration uses object keys/checksums without changing domain relations.


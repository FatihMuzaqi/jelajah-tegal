# Route Architecture

## Loading and conventions

Route files dimuat dari application bootstrap dengan middleware group web atau api yang tepat. Semua route bernama, memakai kebab-case URL, singular parameter names, implicit scoped binding bila aman, dan controller invokable/resource atau Livewire page component.

## Route files

| File | Prefix/name | Middleware baseline | Isi |
| --- | --- | --- | --- |
| routes/web.php | / | web | Landing, public discovery/detail, legal/error pages; memuat surface route files |
| routes/auth.php | /auth, auth. | web, guest/auth sesuai route, throttle | Register/login/logout, verification, reset, OAuth, MFA |
| routes/consumer.php | /account, consumer. | web, auth, verified, active-user | Profile, address, favorite, review, checkout, order/payment/ticket, notification |
| routes/mitra.php | /mitra/{mitra}, mitra. | web, auth, verified, active-user, resolve-mitra, active-membership | Tenant dashboard/profile/member/KYC/bank/catalog/order/claim |
| routes/gatekeeper.php | /gatekeeper/{mitra}, gatekeeper. | web, auth, verified, active-user, resolve-mitra, permission:tickets.validate | Scanner, result, validation history, assignment |
| routes/admin.php | /admin, admin. | web, auth, verified, active-user, MFA, permission per action | Operational administration |
| routes/super-admin.php | /super-admin, super-admin. | web, auth, verified, active-user, recent MFA, permission per action | RBAC, sensitive settings/security/audit |
| routes/api.php | /api/v1, api. | API rate limit, signed/provider auth or Sanctum future | Webhook, direct upload, future mobile/AI/realtime |
| routes/console.php | none | console | Scheduler definitions dan safe operational commands |

## Surface map

| Surface | Representative route names |
| --- | --- |
| Public | home, tourism.index/show, accommodations.index/show, culinary.index/show, events.index/show |
| Auth | auth.login, register, verification.notice/verify, password.request/reset, oauth.google.redirect/callback, mfa.challenge |
| Consumer | consumer.profile.edit, orders.index/show, checkout.create/store, tickets.show, favorites.*, reviews.*, notifications.index |
| Mitra | mitra.dashboard, profile.edit, members.index, gatekeepers.index, kyc.index, bank-accounts.index, catalog.*, orders.index, claims.index |
| Gatekeeper | gatekeeper.scan.create/store, validations.show/index |
| Admin | admin.dashboard, users.*, mitras.*, kyc.*, moderation.*, transactions.*, claims.*, banners.*, broadcasts.*, regions.*, settings.*, audit.index |
| Super Admin | super-admin.roles.*, permissions.*, assignments.*, role-matrix.index, security.*, audit.index |

## Web versus Livewire

- Controller dipilih untuk simple request/redirect, download/stream, OAuth callback, direct provider callback, dan command endpoint dengan clear HTTP semantics.
- Full-page Livewire dipilih untuk dashboard, filterable tables, multi-step forms, role matrix, catalog editing, and scanner UI.
- Livewire action tetap memvalidasi melalui Livewire Form/rules, memanggil authorize, lalu Action; tidak mengakses provider/ledger langsung.

## API boundary

| Endpoint class | Auth/security | Processing |
| --- | --- | --- |
| Midtrans webhook | Public network, provider signature, IP not sole control, strict throttle | Persist inbox then idempotent Action |
| Direct upload request/finalize | Session/Sanctum, permission, tenant ownership, MIME/size policy | Signed temporary URL + finalize metadata |
| Future mobile | Sanctum token abilities + same policies | Calls same Actions |
| AI integration | Service credential/user auth, quota, feature flag | Async job preferred |
| SSE/realtime | Auth session/token, tenant/user channel authorization | Read-only stream; no business command |

## Binding and isolation rules

Mitra routes include explicit {mitra} ULID. resolve-mitra middleware verifies membership and aligns session active_mitra_id. Nested resource bindings must be scoped to the Mitra or parent aggregate; a globally found ULID is never sufficient authorization.

## CSRF and rate limits

All web and Livewire requests use CSRF. Provider webhook is exempt only for its exact API route and uses signature verification. Separate named limiters cover login, reset, OAuth, MFA, upload, checkout, ticket scan, and webhook.

## Redirect and response standard

Web commands use Post/Redirect/Get with flash message. Validation returns field errors. Authorization returns 403 without revealing resource existence when tenant isolation requires 404. API returns versioned JSON error envelope and correlation ID.


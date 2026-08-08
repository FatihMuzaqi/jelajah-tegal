# Security Architecture

## Security model

Defense in depth diterapkan pada identity, session, permission, tenant, state, data protection, provider boundary, dan observability. ULID, hidden navigation, feature flag, dan obscured URL bukan security control.

## Threat and control matrix

| Threat | Primary controls |
| --- | --- |
| Credential stuffing | Rate limit, generic errors, strong password hash, lockout, monitoring |
| Session fixation/theft | Regenerate on auth/privilege, Secure HttpOnly SameSite cookies, expiry/revoke |
| CSRF | Laravel CSRF on all web/Livewire; exact webhook exemption only |
| XSS | Blade escaping, CSP, sanitize rich text, no unsafe HTML from CMS/provider |
| IDOR/cross-tenant | Explicit Mitra context, policy ownership, scoped binding, tests |
| Privilege escalation | Dynamic permissions, grant boundary, recent MFA, audit, last-super-admin guard |
| Webhook spoof/replay | Signature+amount verification, unique inbox, state transition, idempotency |
| File abuse | MIME/size detection, generated keys, private disks, malware hook, signed access |
| Ticket forgery/replay | CSPRNG opaque token, hash at rest, atomic single-use transition, assignment scope |
| Financial tampering | DECIMAL/value objects, transaction locks, immutable balanced ledger, segregation of duties |
| Secret/PII leakage | Encryption, masking, log redaction, access audit, retention |
| Queue replay/context leak | Idempotent jobs, explicit tenant/actor IDs, clear context between jobs |

## QR ticket

QR contains a high-entropy opaque token or URL carrying it; database stores only keyed/secure hash where practical. Ticket code for display is not scan credential. Scanner submits token over TLS. ValidateTicket atomically:

1. hash/lookup token;
2. check ticket active and time window;
3. check gatekeeper permission plus assignment scope;
4. conditional state update from active to used;
5. append validation log for accepted/denied result without exposing token;
6. audit suspicious repeated scans.

Concurrent accepted scans must be impossible. Offline validation is excluded until a separate cryptographic/reconciliation design exists.

## Sensitive data

| Data | Protection |
| --- | --- |
| Password, reset/recovery/QR token | One-way hash; never audit value |
| TOTP secret | Application encryption with key rotation/version |
| Bank/KYC identifier | Encryption + masked display + permission/read audit |
| KYC/renter documents | Private object storage and short authorized URL |
| Midtrans/server/OAuth/storage keys | Environment/secret manager; no database UI plaintext |
| Audit before/after | Redaction allow-list/deny-list; restricted access |

Encryption keys are distinct from app key where risk warrants, versioned, backed up securely, and rotation-tested. Laravel APP_KEY rotation is planned; encrypted data must not become unreadable unexpectedly.

## Audit logging

Audit entries include UTC time, actor/system identity, tenant, event, subject type/id, request/correlation ID, IP/user agent where appropriate, outcome, and redacted changes. High-risk reads such as KYC/export are audited as access events. Audit is append-only; corrections append new metadata, not edit old rows.

## Security headers and browser

Production enforces HTTPS/HSTS, secure cookies, CSP tailored for Vite/Livewire/Midtrans needs, frame-ancestors, nosniff, referrer policy, and restrictive permissions policy. Third-party scripts are minimized and integrity/allow-list reviewed.

## AI integration

AI is FUTURE and isolated behind Services/Ai provider interface plus feature flag/quota. Input is minimized/redacted; tenant/user consent and retention are explicit; prompts/responses are not trusted HTML or executable commands. Tool calls use allow-listed capabilities with policy re-check. External call is queued where possible, has timeout/circuit breaker, cost budget, moderation, and audit metadata. AI cannot directly post ledger, payment, role, KYC, or publication changes.

## Operational security

Dependency/security updates, secret scanning, least-privilege DB/object credentials, backups/restore tests, failed-login/webhook alerting, and incident runbooks are release requirements. Debug mode is off in production; Telescope/Horizon-style tooling, if used, is restricted and redacts payloads.


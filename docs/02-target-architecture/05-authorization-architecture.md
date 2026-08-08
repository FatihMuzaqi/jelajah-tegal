# Authorization Architecture

## Decision

Spatie Laravel Permission menyediakan dynamic role/permission. Laravel Policies dan Gates menjadi enforcement point. Authorization sensitif adalah konjungsi:

authenticated user + active status + effective permission + tenant membership + resource ownership + valid domain state + MFA/feature requirement.

Nama role tidak boleh menjadi satu-satunya condition pada controller, Livewire, Action, atau view.

## Role scopes

| Scope | Seed role | Karakter |
| --- | --- | --- |
| Global | consumer, admin, super-admin | consumer baseline; admin privileges tetap permission-driven |
| Tenant/Mitra | mitra-owner, mitra-staff, gatekeeper | Assignment terikat mitra_id/team context |

Role dapat dibuat dinamis oleh authorized super-admin. System seed role boleh dilindungi dari delete/rename berbahaya, namun permissionnya tetap dikelola melalui controlled workflow.

## Permission taxonomy

Format resource.action dengan stable English key, contoh:

- profile.update, orders.create, orders.view-own;
- mitras.create, mitras.update, mitras.suspend;
- members.invite, bank-accounts.manage, kyc.submit, kyc.review;
- tourism.manage, accommodation.manage, culinary.manage, events.manage;
- tickets.issue, tickets.validate;
- claims.create, claims.review, ledger.view;
- moderation.review, audit.view, settings.manage;
- roles.manage, permissions.manage, role-assignments.manage.

Permission catalog version-controlled melalui seed/config deployment, sedangkan assignment berada di database. Super-admin tidak membuat arbitrary permission key tanpa registry/description/risk classification; ia dapat membuat role dan memilih registered permissions.

## Policy algorithm

Policy untuk tenant-owned resource memeriksa berurutan:

1. User active dan route context resolved.
2. Active Mitra membership valid dan tidak suspended.
3. Resource.mitra_id sama dengan active Mitra.
4. User memiliki effective permission pada tenant scope.
5. Feature/module enabled untuk Mitra.
6. Resource state mengizinkan action.
7. Recent MFA bila action sensitive.

Global admin tidak otomatis bypass tenant ownership. Support override bila diperlukan harus permission khusus, reason input, explicit cross-tenant mode, dan audit.

## Enforcement locations

| Lokasi | Fungsi |
| --- | --- |
| Route middleware | Baseline auth/verified/MFA/permission/surface |
| Policy | Record ownership, tenant, domain state |
| Form Request authorize | Early request-level authorization |
| Livewire authorize | Setiap mutating action, bukan hanya mount |
| Action | Defense-in-depth invariant untuk entry dari web/API/job |
| Blade | Hanya menyembunyikan UI; bukan security boundary |

## Super-admin RBAC operations

Create/update role, grant/revoke permission, assign/revoke role, direct permission, dan role-permission matrix wajib:

- memakai recent MFA;
- membedakan global dan Mitra scope;
- mencegah privilege assignment di luar actor grant boundary;
- mencegah removal last active super-admin;
- invalidate permission cache dan affected sessions/navigation cache;
- menulis before/after audit tanpa secret;
- menunjukkan effective access preview.

## Gatekeeper

tickets.validate saja tidak cukup. Gatekeeper harus mempunyai active tenant membership dan active assignment yang mencakup ticket/event/destination/outlet. Admin dan super-admin tidak otomatis boleh scan; mereka memerlukan permission serta assignment eksplisit jika business policy mengizinkan.

## Feature flags

Feature flag/module enablement hanya menentukan availability. Ia tidak memberi izin. Request harus lolos flag dan permission. Flag global, tenant override, rollout percentage, start/end, dan emergency kill-switch mempunyai precedence terdokumentasi dan audit.

## Authorization testing matrix

Setiap policy diuji allow/deny untuk owner, tenant lain, staff tanpa permission, inactive membership, suspended user/Mitra, disabled feature, invalid state, admin tanpa override, dan super-admin tanpa recent MFA.


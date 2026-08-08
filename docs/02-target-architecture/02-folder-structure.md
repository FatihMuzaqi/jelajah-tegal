# Folder Structure

Struktur menggunakan konvensi Laravel dan subnamespace domain. Folder dibuat ketika implementasi dimulai; tahap ini hanya mendokumentasikan target.

```text
app/
├── Actions/
│   ├── Auth/
│   ├── Mitra/
│   ├── Catalog/
│   ├── Orders/
│   ├── Payments/
│   ├── Tickets/
│   ├── Finance/
│   └── Platform/
├── Enums/
├── Events/
├── Http/
│   ├── Controllers/
│   │   ├── Public/
│   │   ├── Consumer/
│   │   ├── Mitra/
│   │   ├── Gatekeeper/
│   │   ├── Admin/
│   │   ├── SuperAdmin/
│   │   └── Api/
│   ├── Middleware/
│   └── Requests/
│       ├── Auth/
│       ├── Consumer/
│       ├── Mitra/
│       ├── Gatekeeper/
│       ├── Admin/
│       └── Api/
├── Jobs/
├── Livewire/
│   ├── Public/
│   ├── Consumer/
│   ├── Mitra/
│   ├── Gatekeeper/
│   ├── Admin/
│   ├── SuperAdmin/
│   └── Forms/
├── Models/
├── Notifications/
├── Policies/
├── Services/
│   ├── Auth/
│   ├── Tenancy/
│   ├── Payments/
│   ├── Finance/
│   ├── Storage/
│   ├── Notifications/
│   └── Ai/
└── Support/
    ├── Audit/
    ├── Context/
    ├── Idempotency/
    ├── Money/
    ├── Pagination/
    └── Observability/

resources/
├── views/
│   ├── layouts/
│   ├── components/
│   ├── public/
│   ├── consumer/
│   ├── mitra/
│   ├── gatekeeper/
│   ├── admin/
│   ├── super-admin/
│   ├── auth/
│   └── errors/
├── css/
└── js/

routes/
├── web.php
├── auth.php
├── consumer.php
├── mitra.php
├── gatekeeper.php
├── admin.php
├── super-admin.php
├── api.php
└── console.php
```

## Placement rules

| Artefak                          | Lokasi                            | Contoh konseptual                         |
| -------------------------------- | --------------------------------- | ----------------------------------------- |
| Satu write use case              | Actions/domain                    | CreateOrder, ApproveClaim, ValidateTicket |
| Provider atau algorithm reusable | Services/domain                   | MidtransGateway, LedgerPostingService     |
| HTTP validation                  | Http/Requests/surface atau domain | StoreMitraRequest                         |
| Livewire form state              | Livewire/Forms                    | EditMitraProfileForm                      |
| Authorization aggregate          | Policies                          | OrderPolicy, MitraPolicy                  |
| Async work                       | Jobs                              | SendOwnerInvitation, ProcessMediaCleanup  |
| User-facing delivery             | Notifications                     | PaymentPaidNotification                   |
| Immutable domain signal          | Events                            | PaymentSettled, TicketValidated           |
| Shared value/technical helper    | Support                           | Money, RequestContext, IdempotencyResult  |

## Naming guidance

- Actions memakai imperative verb: CreateMitra, PlaceOrder, ApplyPaymentWebhook.
- Services memakai capability noun: MidtransService, LedgerService, TenantContext.
- Events memakai past tense: OrderPlaced, PaymentSettled.
- Jobs memakai imperative outcome: SendVerificationEmail, ExpirePendingOrders.
- Policies mengikuti model aggregate; permission string memakai resource.action.

## Avoided structures

- Repository interface untuk setiap Eloquent model tidak diwajibkan; tambahkan hanya untuk external boundary atau query complexity yang nyata.
- Tidak ada Controllers/Api duplikat untuk use case web. Keduanya memanggil Action yang sama.
- Tidak ada Helpers.php global berisi business logic.
- Tidak ada Models per surface; satu model domain digunakan lintas surface dengan policy berbeda.
- Tidak membuat Modules/vendor-style package internal pada V1.

## Views and visual system

Reusable Blade components mencakup application shell, sidebar, topbar, breadcrumb, status badge, alert, modal, filter bar, paginated table, form controls, empty/error/loading state, dan chart card. Components original dibangun dengan Bootstrap/Alpine/Livewire. ArchitectUI hanya inspirasi layout density dan hierarchy.

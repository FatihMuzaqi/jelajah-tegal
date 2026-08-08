<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerLine extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = ['ledger_journal_id', 'ledger_account_id', 'sequence', 'debit_amount', 'credit_amount', 'currency', 'created_at'];

    protected function casts(): array
    {
        return ['debit_amount' => 'decimal:2', 'credit_amount' => 'decimal:2', 'sequence' => 'integer', 'created_at' => 'datetime'];
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(LedgerJournal::class, 'ledger_journal_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }
}

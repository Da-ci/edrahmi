<?php

namespace App\Models;

use App\Models\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'status',
        'application_id',

        'amount',

        'order_number',
        'order_id',
        'card_holder_name',
        'deposit_amount',
        'currency',
        'auth_code',
        'action_code',
        'action_code_description',
        'error_code',
        'error_message',
        'order_number',

    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->status = 'Processing';
        });
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycReview extends Model
{
    const ACTION_APPROVED = 'approved';
    const ACTION_REJECTED = 'rejected';

    protected $fillable = ['kyc_verification_id', 'reviewed_by', 'action', 'reason'];

    public function kycVerification(): BelongsTo
    {
        return $this->belongsTo(KycVerification::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

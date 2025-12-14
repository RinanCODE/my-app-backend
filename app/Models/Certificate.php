<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'participant_id',
        'template_id',
        'certificate_id',
        'qr_code_path',
        'pdf_path',
        'issued_date',
        'is_valid',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'is_valid' => 'boolean',
    ];

    /**
     * Get the event that owns the certificate.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the participant that owns the certificate.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Get the template used for this certificate.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class);
    }
}


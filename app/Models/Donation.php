<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'donor_name',
        'donor_email',
        'donor_whatsapp',
        'amount',
        'message',
        'amin_count',
        'amin_users',
        'payment_method',
        'payment_status',
        'is_anonymous',
        'expired_at',
        'payment_proof',
        'payment_notes',
        'proof_uploaded_at',
        'salutation',
        'tripay_reference',
        'tripay_fee',
        'tripay_status',
        'tripay_payment_url',
    ];

    protected $casts = [
        'amount' => 'integer',
        'is_anonymous' => 'boolean',
        'expired_at' => 'datetime',
        'proof_uploaded_at' => 'datetime',
        'amin_users' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Method untuk menambah amin
    public function addAmin($userId = null)
    {
        $aminUsers = $this->amin_users ?? [];
        
        // Jika user sudah amin, tidak perlu ditambah lagi
        if (in_array($userId, $aminUsers)) {
            return false;
        }
        
        $aminUsers[] = $userId;
        $this->amin_users = $aminUsers;
        $this->amin_count = count($aminUsers);
        $this->save();
        
        return true;
    }

    // Method untuk menghapus amin
    public function removeAmin($userId = null)
    {
        $aminUsers = $this->amin_users ?? [];
        
        // Hapus user dari array amin_users
        $aminUsers = array_filter($aminUsers, function($id) use ($userId) {
            return $id != $userId;
        });
        
        $this->amin_users = array_values($aminUsers);
        $this->amin_count = count($aminUsers);
        $this->save();
        
        return true;
    }

    // Method untuk cek apakah user sudah amin
    public function hasUserAmin($userId = null)
    {
        $aminUsers = $this->amin_users ?? [];
        return in_array($userId, $aminUsers);
    }
} 
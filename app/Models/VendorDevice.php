<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorDevice extends Model
{
    /** Oldest sessions beyond this many are dropped, so silent re-logins can't grow the table forever. */
    public const MAX_PER_VENDOR = 5;

    protected $fillable = ['vendor_id', 'auth_token', 'fcm_token', 'last_used_at'];

    protected $hidden = ['auth_token'];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /** Records a freshly issued login token as its own device session. */
    public static function register(Vendor $vendor, string $token): void
    {
        self::create([
            'vendor_id' => $vendor->id,
            'auth_token' => $token,
            'last_used_at' => now(),
        ]);

        $keep = self::where('vendor_id', $vendor->id)
            ->orderByDesc('last_used_at')
            ->limit(self::MAX_PER_VENDOR)
            ->pluck('id');
        self::where('vendor_id', $vendor->id)->whereNotIn('id', $keep)->delete();
    }

    /** Throttled so an authenticated request doesn't cost a write every time. */
    public function markUsed(): void
    {
        if (!$this->last_used_at || $this->last_used_at->lt(now()->subHour())) {
            $this->forceFill(['last_used_at' => now()])->saveQuietly();
        }
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


/**
 * @property Organization $organization
 * @property ?string $key
 */
class Key extends Model
{
    public ?string $plaintext = null;
    
    protected $casts = [
        'last_used_at' => 'datetime',
    ];
    
    /**
     * Set a plain text and hashed value for new keys.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function (Key $key) {
            if (is_null($key->key)) {
                $key->plaintext = self::generate();
                $key->key = self::hash($key->plaintext);
            }
        });
    }
    
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
    /**
     * Generate a unique key.
     * @return string
     */
    public static function generate()
    {
        do {
            $plaintext_key = Str::random(40);
        } while (self::keyExists($plaintext_key));

        return $plaintext_key;
    }
    
    /**
     * Check if a plaintext key already exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function keyExists(string $key)
    {
        return self::where('key', self::hash($key))
            ->first() instanceof self;
    }
    
    /**
     *
     * @param string $key
     * @return string type
     */
    public static function hash(string $key)
    {
        return hash('sha256', $key);
    }
}

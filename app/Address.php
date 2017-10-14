<?php

namespace App;

use App\Util\Iota;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address',
    ];

    /**
     * Address belongs to a user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     *
     */
    public function getBalance()
    {
        return (new Iota())->getBalanceByAddress($this->address, 'I');
    }


    /**
     * Address has many payments
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'address_id');
    }

}

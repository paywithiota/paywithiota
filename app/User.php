<?php

namespace App;

use App\Util\Iota;
use Laravel\Spark\Spark;
use Ramsey\Uuid\Uuid;

use Laravel\Spark\User as SparkUser;

class User extends SparkUser
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'last_key_index',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at'        => 'datetime',
        'uses_two_factor_auth' => 'boolean',
    ];

    /**
     * User has many payments
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    /**
     * User has many addresses
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id');
    }


    /**
     * Get user token
     * @return \Illuminate\Http\JsonResponse
     */
    public function token()
    {
        $token = $this->tokens()->select('token')->first();

        if ( ! $token) {

            $token = $this->tokens()->create([
                'id'         => Uuid::uuid4(),
                'user_id'    => $this->id,
                'name'       => "Default",
                'token'      => str_random(60),
                'metadata'   => [],
                'transient'  => false,
                'expires_at' => null,
            ]);

        }

        return $token ? $token->token : null;
    }

    /**
     * Create new address
     * @return \Illuminate\Database\Eloquent\Model|null|string
     */
    public function createNewAddress($newKeyIndex = null)
    {
        if (is_null($newKeyIndex)) {
            $lastKeyIndex = $this->last_key_index;
            $newKeyIndex = intval(is_null($lastKeyIndex) ? 0 : $lastKeyIndex + 1);
        }

        $address = (new Iota())->generateAddress($this, $newKeyIndex);

        if ($address) {
            $address = $this->addresses()->firstOrCreate([
                'address'   => $address,
                'key_index' => $newKeyIndex,
            ]);

            // Update index
            $this->update([
                'last_key_index' => $newKeyIndex
            ]);

            return $address;
        }

        return null;
    }
}

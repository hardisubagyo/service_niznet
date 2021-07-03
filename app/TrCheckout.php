<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class TrCheckout extends Authenticatable
{
    protected $table = "tr_checkout";
}

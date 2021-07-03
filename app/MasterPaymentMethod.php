<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class MasterPaymentMethod extends Authenticatable
{
    protected $table = "master_payment_method";
}

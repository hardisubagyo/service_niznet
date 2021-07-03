<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class MasterKurir extends Authenticatable
{
    protected $table = "master_kurir";
}

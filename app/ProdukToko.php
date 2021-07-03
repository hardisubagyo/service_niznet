<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class ProdukToko extends Authenticatable
{
    protected $table = "master_produk_toko";
}

<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model as _MODEL;

class Model extends _MODEL {
    use Concerns\CustomAttributes;
}

class UserAuth extends Authenticatable {
    use Concerns\CustomAttributes;
}

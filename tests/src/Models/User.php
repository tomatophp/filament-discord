<?php

namespace TomatoPHP\FilamentDiscord\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use TomatoPHP\FilamentDiscord\Traits\InteractsWithDiscord;

/**
 * @property ?string $webhook
 */
class User extends Authenticatable
{
    use InteractsWithDiscord;

    protected $guarded = [];

    protected $table = 'users';
}

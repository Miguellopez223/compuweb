<?php

namespace App\Support\Auth;

if (trait_exists(\Laravel\Passport\HasApiTokens::class)) {
    trait HasApiTokensCompat
    {
        use \Laravel\Passport\HasApiTokens;
    }
} else {
    trait HasApiTokensCompat
    {
        // Passport is optional in this project; keep auth working when missing.
    }
}

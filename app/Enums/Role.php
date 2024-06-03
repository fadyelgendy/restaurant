<?php

namespace App\Enums;

enum Role: string
{
    case CUSTOMER = 'customer';
    case MERCHANT = 'merchant';
}

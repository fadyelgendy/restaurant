<?php

namespace App\Enums;

enum Status: string
{
    case AVAILABLE = 'available';
    case WARNING = 'warning';
    case OUT_OF_STOCK = 'out_of_stock';
    case PENDING = 'pending';
    case INPROGRESS = 'inprogress';
    case PROCESSED = 'proccessed';
    case COMPLETED = 'completed';
}

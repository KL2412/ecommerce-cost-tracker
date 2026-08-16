<?php

namespace App\Enums;

enum InventoryTransactionType: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
}

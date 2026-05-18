<?php

declare(strict_types=1);

namespace App\Directory\Input;

use App\Directory\Enum\Order;

final class SearchSirenSorting
{
    public ?string $field = null;
    public ?Order $order = null;
}

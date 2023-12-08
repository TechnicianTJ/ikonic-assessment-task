<?php


namespace App\Helpers;


use App\Models\Order;
use App\Repositories\OrderRepository;

class OrderHelper
{
    public $orderRepo;

    public function __construct()
    {
        $this->orderRepo = new OrderRepository();
    }

    public function getOrderWithInRange($from, $to)
    {
        return Order::with('affiliate')
            ->whereBetween('created_at', [$from, $to])
            ->get();
    }
}

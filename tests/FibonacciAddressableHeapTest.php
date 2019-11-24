<?php

namespace tests;

use BingoSoft\Heap\AddressableHeapInterface;
use BingoSoft\Heap\Tree\FibonacciHeap;

class FibonacciAddressableHeapTest extends AbstractAddressableHeapTest
{
    protected function createHeap(): AddressableHeapInterface
    {
        return new FibonacciHeap();
    }
}

<?php

namespace tests;

use heap\AddressableHeapInterface;
use heap\tree\FibonacciHeap;

class FibonacciAddressableHeapTest extends AbstractAddressableHeapTest
{
    protected function createHeap(): AddressableHeapInterface
    {
        return new FibonacciHeap();
    }
}

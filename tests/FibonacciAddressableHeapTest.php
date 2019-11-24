<?php

namespace tests;

use Heap\AddressableHeapInterface;
use Heap\Tree\FibonacciHeap;

class FibonacciAddressableHeapTest extends AbstractAddressableHeapTest
{
    protected function createHeap(): AddressableHeapInterface
    {
        return new FibonacciHeap();
    }
}

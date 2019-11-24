<?php

namespace tests;

use BingoSoft\Heap\MergeableAddressableHeapInterface;
use BingoSoft\Heap\Tree\FibonacciHeap;

class FibonacciMergeableAddressableHeapTest extends AbstractMergeableAddressableHeapTest
{
    protected function createHeap(): MergeableAddressableHeapInterface
    {
        return new FibonacciHeap();
    }
}

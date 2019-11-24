<?php

namespace tests;

use Heap\MergeableAddressableHeapInterface;
use Heap\Tree\FibonacciHeap;

class FibonacciMergeableAddressableHeapTest extends AbstractMergeableAddressableHeapTest
{
    protected function createHeap(): MergeableAddressableHeapInterface
    {
        return new FibonacciHeap();
    }
}

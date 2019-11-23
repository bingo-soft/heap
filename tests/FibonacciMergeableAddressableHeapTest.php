<?php

namespace tests;

use heap\MergeableAddressableHeapInterface;
use heap\tree\FibonacciHeap;

class FibonacciMergeableAddressableHeapTest extends AbstractMergeableAddressableHeapTest
{
    protected function createHeap(): MergeableAddressableHeapInterface
    {
        return new FibonacciHeap();
    }
}

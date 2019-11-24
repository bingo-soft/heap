<?php

namespace tests;

use Heap\MergeableAddressableHeapInterface;
use Heap\Tree\PairingHeap;

class PairingMergeableAddressableHeapTest extends AbstractMergeableAddressableHeapTest
{
    protected function createHeap(): MergeableAddressableHeapInterface
    {
        return new PairingHeap();
    }
}

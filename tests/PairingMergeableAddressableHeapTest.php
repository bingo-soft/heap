<?php

namespace tests;

use BingoSoft\Heap\MergeableAddressableHeapInterface;
use BingoSoft\Heap\Tree\PairingHeap;

class PairingMergeableAddressableHeapTest extends AbstractMergeableAddressableHeapTest
{
    protected function createHeap(): MergeableAddressableHeapInterface
    {
        return new PairingHeap();
    }
}

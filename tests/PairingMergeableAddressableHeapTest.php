<?php

namespace tests;

use heap\MergeableAddressableHeapInterface;
use heap\tree\PairingHeap;

class PairingMergeableAddressableHeapTest extends AbstractMergeableAddressableHeapTest
{
    protected function createHeap(): MergeableAddressableHeapInterface
    {
        return new PairingHeap();
    }
}

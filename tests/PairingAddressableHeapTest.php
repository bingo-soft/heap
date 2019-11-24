<?php

namespace tests;

use Heap\AddressableHeapInterface;
use Heap\Tree\PairingHeap;

class PairingAddressableHeapTest extends AbstractAddressableHeapTest
{
    protected function createHeap(): AddressableHeapInterface
    {
        return new PairingHeap();
    }
}

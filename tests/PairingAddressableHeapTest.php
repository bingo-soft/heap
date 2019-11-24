<?php

namespace tests;

use BingoSoft\Heap\AddressableHeapInterface;
use BingoSoft\Heap\Tree\PairingHeap;

class PairingAddressableHeapTest extends AbstractAddressableHeapTest
{
    protected function createHeap(): AddressableHeapInterface
    {
        return new PairingHeap();
    }
}

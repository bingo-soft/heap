<?php

namespace tests;

use heap\AddressableHeapInterface;
use heap\tree\PairingHeap;

class PairingAddressableHeapTest extends AbstractAddressableHeapTest
{
    protected function createHeap(): AddressableHeapInterface
    {
        return new PairingHeap();
    }
}

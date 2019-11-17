<?php

namespace heap;

/**
 * Interface MergeableAddressableHeapInterface
 *
 * @package heap
 */
interface MergeableAddressableHeapInterface extends AddressableHeapInterface
{
    /**
     * Meld other heap to the current heap
     *
     * @param MergeableAddressableHeapInterface $other - the heap to be melded
     */
    public function meld(MergeableAddressableHeapInterface $other): void;
}

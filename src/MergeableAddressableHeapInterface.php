<?php

namespace Heap;

/**
 * Interface MergeableAddressableHeapInterface
 *
 * @package Heap
 */
interface MergeableAddressableHeapInterface extends AddressableHeapInterface
{
    /**
     * Meld other heap to the current heap
     *
     * @param MergeableAddressableHeapInterface $other - the heap to be melded
     */
    public function meld(MergeableAddressableHeapInterface $other): void;

    /**
     * Get the other heap referenced in the current heap
     *
     * @return MergeableAddressableHeapInterface
     */
    public function getOther(): MergeableAddressableHeapInterface;

    /**
     * Reference the other heap in the current heap
     *
     * @param MergeableAddressableHeapInterface $other - the other heap
     */
    public function setOther(MergeableAddressableHeapInterface $other): void;
}

<?php

namespace Heap;

/**
 * Interface AddressableHeapHandleInterface
 *
 * @package Heap
 */
interface AddressableHeapHandleInterface
{
    /**
     * Get the node key
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Get the node value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Set the node value
     *
     * @param mixed $value - node value
     */
    public function setValue($value): void;

    /**
     * Decrease the node key
     *
     * @param mixed $newKey - new node key
     */
    public function decreaseKey($newKey): void;

    /**
     * Delete the node
     */
    public function delete(): void;
}

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
     * @return int
     */
    public function getKey(): int;

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
     * @param int $newKey - new node key
     */
    public function decreaseKey(int $newKey): void;

    /**
     * Delete the node
     */
    public function delete(): void;
}

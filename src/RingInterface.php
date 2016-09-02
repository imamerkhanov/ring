<?php
/**
 * @link https://github.com/imamerkhanov
 * @author Ilshat Amerkhanov
 */
namespace imamerkhanov\ring;


interface RingInterface
{
    /**
     * Метод генерации хеша
     * @param $item
     * @return mixed
     */
    public function Hash(int $item);

    /**
     * Поиск номера ноды по идентификатору
     * @param int $item
     * @return mixed
     */
    public function getNodeId(int $item);
}
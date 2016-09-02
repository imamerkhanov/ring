<?php
/**
 * Created by PhpStorm.
 * User: twk
 * Date: 01.09.16
 * Time: 19:00
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
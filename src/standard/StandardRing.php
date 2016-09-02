<?php
/**
 * @link https://github.com/imamerkhanov
 * @author Ilshat Amerkhanov
 */
namespace imamerkhanov\ring\standard;


use imamerkhanov\ring\Ring;

class StandardRing extends Ring
{
    /**
     * StandardRing constructor.
     * @param $params
     */
    public function __construct($params)
    {
        parent::__construct($params);
    }

    /**
     * Поиск номера ноды по хешу
     * @param $h
     * @return int
     */
    public function findNode(int $h)
    {
        return  (int)gmp_strval(gmp_mod($h, $this->nodesCount));
    }
}
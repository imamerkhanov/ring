<?php
/**
 * Created by PhpStorm.
 * User: twk
 * Date: 26.08.16
 * Time: 19:59
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
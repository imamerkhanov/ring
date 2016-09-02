<?php
/**
 * @link https://github.com/imamerkhanov
 * @author Ilshat Amerkhanov
 */
namespace imamerkhanov\ring\partition;


use imamerkhanov\ring\Ring;

class PartitionRing extends Ring
{
    /**
     * Битовая разрядность кольца (количество нод 2 в степени $bitCount)
     * @var int
     */
    public $bitCount=0;

    /**
     * Карта нод
     * @var array
     */
    public $nodeIndexMap=[];

    /**
     * PartitionRing constructor.
     * @param $params
     */
    public function __construct($params)
    {
        parent::__construct($params);
        $this->countPerNode = array_fill(0, count($this->nodes),0);
    }

    /**
     * Поиск номера ноды по хешу
     * @param $h
     * @return mixed
     */
    public function findNode(int $h)
    {
        $partition = (int)$h >> (32 - $this->bitCount);
        $index = $this->nodeIndexMap[$partition];
        $node = $this->nodes[$index];
        return $node?$node->id:null;
    }


}
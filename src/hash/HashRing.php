<?php
/**
 * @link https://github.com/imamerkhanov
 * @author Ilshat Amerkhanov
 */
namespace imamerkhanov\ring\hash;

use imamerkhanov\ring\Ring;

class HashRing extends Ring
{
    public $virtualNodesCount = 1000;
    public $hashesToNode = [];

    /**
     * HashRing constructor.
     * @param $params
     */
    public function __construct($params)
    {
        parent::__construct($params);
        $this->nodesCount = $this->validData(1,100000,$this->nodesCount);
        $this->virtualNodesCount = $this->validData(1,100000,$this->virtualNodesCount);
        $this->nodes = array_fill(0, $this->nodesCount*$this->virtualNodesCount,0);
        $this->hashesToNode = array_fill(0, $this->nodesCount*$this->virtualNodesCount,0);

        $p=0;
        for($n=0;$n<$this->nodesCount;$n++)
            for($v=0;$v<$this->virtualNodesCount;$v++)
            {
                $h=$this->Hash($n*1000000 + $v); // чтоб не пересеклись ноды * 1 000 000
                $this->nodes[$p]=$h;
                $p++;
                $this->hashesToNode[$h] = $n;
            }

        sort($this->nodes);
    }

    /**
     * @TODO очь ебанненько - время теряеться - надо найти альтернативу - проигрывает GO в 10 раз
     * Поиск номера ноды по хешу
     * @param $h
     * @return int
     */
    public function findNode(int $h)
    {
        $start = 0;
        $end = count($this->nodes)-1;
        while ($end-$start>5)
        {
            $center = $start + intval(($end-$start)/2);
            if($this->nodes[$center]<$h)
                $start = $center;
            else
                $end = $center;
        }

        for($i=$start;$i<=$end;$i++)
            if($this->nodes[$i]>=$h)
                return $i;
        return 0;
    }

    /**
     * Валидируем данные
     * @param $min
     * @param $max
     * @param $value
     * @return mixed
     */
    public function validData($min,$max,$value)
    {
        if($value<=$min)
            $value=$min;
        if($value>$max)
            $value=$max;
        return $value;
    }
}
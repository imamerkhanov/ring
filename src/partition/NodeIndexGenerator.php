<?php
/**
 * Класс реализующий смещение при построение битовой карты нод
 * Пример карты
 * [2 2 0 1 1 2 0 0 1 2 2 0 1 1 2 0]
 * Суть в том что при каждой интерации повторяеться номер следующей ноды
 */

namespace imamerkhanov\ring\partition;


class NodeIndexGenerator implements \Iterator
{
    /**
     * Номер последнего дубликата
     * @var int
     */
    protected $duplicate=1;

    /**
     * Номер ноды
     * @var int
     */
    protected $key=0;

    /**
     * Количество нод
     * @var int
     */
    protected $nodeCount=0;

    /**
     * Начальный номер
     * @var int
     */
    protected $start=0;

    /**
     * Текущее количество повтора ключа
     * @var int
     */
    protected $repeat=1;

    /**
     * Максимальное количество повторов
     * @var int
     */
    protected $maxRepeat=1;

    public function __construct(int $nodeCount,int $start=0) {
        $this->start = $start;
        $this->key = ($this->start!=0)?$nodeCount:$this->start;
        $this->nodeCount = $nodeCount;
        if($start>0)
            $this->maxRepeat = $nodeCount-$start+1;
        if ($this->nodeCount<0)
            throw new \Exception();
    }

    /**
     * Возвращаем следующий номер ноды
     * Можно было бы обойтись и просто $this->generateNext();
     * Но при больших $bitCount карта получаеться слишком разнородной по сравнению с предыдущей репликацией
     * Например при добовлении 2-х нод вместо
     * [1 ... 1 1 1 1 1 5 5 4 4 5 5 4 4 5 5 4 4 5 5 4 4 5 5 4 4 5 2 2 2 2 ... ]
     * получем
     * [1 ... 1 1 1 1 1 5 4 5 4 5 4 5 4 5 4 5 4 5 4 5 4 5 4 5 4 5 2 2 2 2 ... ]
     * @return int
     */
    public function next()
    {
        $tmp = $this->key;

        if($this->start==0)
            $this->generateNext();
        elseif($this->repeat==$this->maxRepeat)
        {
            $this->generateNext();
            $this->repeat=1;
        }else
            $this->repeat++;

        return $tmp;
    }

    /**
     * Генерация номера ноды
     * @return int
     */
    public function generateNext()
    {
        if($this->duplicate==$this->key)
        {
            $this->duplicate++;
            if($this->duplicate>$this->nodeCount)
                $this->duplicate=$this->start;
        }else
            $this->key--;

        if($this->key<$this->start)
            $this->key =$this->nodeCount;
    }

    public function current() {}

    public function key(){}

    public function rewind(){}

    public function valid(){}

    public function __destruct(){}
}
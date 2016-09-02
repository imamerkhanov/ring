<?php
/**
 * @link https://github.com/imamerkhanov
 * @author Ilshat Amerkhanov
 */
namespace imamerkhanov\ring\partition;


class Builder
{
    /**
     * Все ноды в кольце
     * @var array
     */
    public $nodes = [];

    /**
     * Битовая разрядность кольца (количество нод 2 в степени $bitCount)
     * @var int
     */
    public $bitCount=1;

    /**
     * Предыдущая битовая разрядность
     * @var int
     */
    public $prevBitCount=0;

    /**
     * Придел роста размера карты нод
     *  1 << 23 is 8388608 при 3 репликах будет тратить до 100M памяти
     * @var int
     */
    public $maxBitCount=23;

    /**
     * Карта нод
     * @var array
     */
    public $nodeIndexMap=[-1, -1];

    /**
     * Будет пытаться сохранить данные в пределах +\-.
     * По умолчанию используется значение 1 на один процент дополнительные или меньше данных.
     * Мера допустимого отклонения количества данных внутри ноды
     * @var int
     */
    public $pointsAllowed=1;

    /**
     * Предыдущее количество нод
     * @var int
     */
    public $prevNodeCount=0;

    /**
     * Добовление нод в кольцо
     * @param $active
     * @param $capacity
     * @param $meta
     * @return Node
     */
    public function AddNode($active , $capacity , $meta)
    {
        $n  = new Node(count($this->nodes));
        $n->inactive = !$active;
        $n->capacity = $capacity;
        $n->meta = $meta;
        $this->nodes[]=$n;
        return $n;
    }

    /**
     * Генерирует кольцо из полученных нод -> AddNode
     * @return PartitionRing
     */
    public function Ring()
    {
        $validNodes = false;
        if(!empty($this->nodes))
            foreach ($this->nodes as $node)
            if(!$node->inactive)
                $validNodes = true;

        if(!$validNodes)
            die("Нет действительных узлов кольца");

        $this->initBitMap();

        $params = [
            'bitCount'      =>  $this->bitCount,
            'nodes'         =>  $this->nodes,
            'nodeIndexMap'  =>  $this->nodeIndexMap
        ];
        return new PartitionRing($params);
    }

    /**
     * Вычисляем новый размер карты и инициализируем битовую карту нод
     * @return bool
     */
    public function initBitMap()
    {
        if ($this->bitCount >= $this->maxBitCount)
            return false;

        $totalCapacity = 0;
        $nodes = [];
        /** выесняем суммарную емкость всех нод в кольце */
        if(!empty($this->nodes))
            foreach ($this->nodes as $node)
                if(!$node->inactive)
                    $totalCapacity+=$node->capacity;

        /** Количество элементов карты */
        $partitionCount = count($this->nodeIndexMap);

        /** разрядность карты */
        $bitCount = $this->bitCount;

        /** Мера допустимого отклонения */
        $pointsAllowed = (float)($this->pointsAllowed) * 0.01;

        if(!empty($this->nodes))
            foreach ($this->nodes as $node)
            {
                if($node->inactive)
                    continue;

                $nodes[$node->id]=0;

                /** часть карты занимаемая одной нодой - среднее */
                $desiredPartitionCount = (float)$partitionCount *  (float)$node->capacity / (float)$totalCapacity;

                $under = ($desiredPartitionCount - (int)$desiredPartitionCount) / $desiredPartitionCount;
                $over = 0;

                if ($desiredPartitionCount > (int)$desiredPartitionCount)
                    $over = ((int)($desiredPartitionCount+1) - $desiredPartitionCount) / $desiredPartitionCount;

                /**
                 * помещается ли нода в предел отклонения,
                 * если нет то увеличиваем размерность карты
                 */
                if ($under > $pointsAllowed || $over > $pointsAllowed)
                {
                    $partitionCount <<= 1;
                    $bitCount++;
                    if ($bitCount == $this->maxBitCount)
                        break;
                }
		    }

        if ($partitionCount > count($this->nodeIndexMap))
            $this->bitCount = $bitCount;

        $this->initNewMap();

        $this->prevBitCount = $this->bitCount;
        $this->prevNodeCount = count($this->nodes);
    }

    /**
     * Инициализируем битовую карту нод
     */
    public function initNewMap()
    {
        /** Если первая генерация */
        if($this->prevBitCount==0)
        {
            $top = pow(2,$this->bitCount)-1;

            $generateNextItems = new NodeIndexGenerator(count($this->nodes)-1);
            for($i=(count($this->nodeIndexMap)-1);$i<=$top;$i++)
                $this->nodeIndexMap[$i]=-1;

            for($i=$top;$i>=0;$i--)
            {
                if ($this->nodeIndexMap[$i] >= 0)
                    continue;

                $this->nodeIndexMap[$i]=$generateNextItems->next();
            }
        }
        else
        {
            $lastNodeIndexMap=$this->nodeIndexMap;
            $top = pow(2,$this->bitCount)/pow(2,$this->prevBitCount);
            $topRepeat =pow(2,$this->prevBitCount);

            $repeatBlock = floor(pow(2,$this->bitCount)/count($this->nodes));

            $generateNextItems = new NodeIndexGenerator(count($this->nodes)-1,$this->prevNodeCount);

            $nodesRepeat = $this->alignmentMap($top,$topRepeat,$repeatBlock);

            /**
             * На основе старой карты строим новую увеличенную на новый bitCount
             * С выравниванием среднего количества нод на карте
             * Пример с 4 на 5 нод
             * [1 2 3 0 ]
             * [1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 4 4 4 4 4 4 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 4 4 4 4 4 4 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 3 4 4 4 4 4 4 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 4 4 4 4 4 4 4 ]
             */
            for($i=0;$i<$topRepeat;$i++)
                for($j=0;$j<$top;$j++)
                {
                    $key = $lastNodeIndexMap[$i];
                    if($nodesRepeat[$key]>=$repeatBlock)
                        $key=$generateNextItems->next();

                    $this->nodeIndexMap[$i*$top +$j]=$key;
                    $nodesRepeat[$key]++;
                }
        }
    }


    /**
     * функция выравнивания
     * При больших bitCount во время решарда - количество может сильно разница в хвасте карты
     * Например при 100
     *  [162,162,.....,162,162,184]
     * Данная функция это выравнивает
     *  [162,162,.....,163,163,163]
     * @param $top
     * @param $topRepeat
     * @param $repeatBlock
     * @return array
     */

    public function alignmentMap($top,$topRepeat,$repeatBlock)
    {
        $nodesRepeat=[];
        foreach ($this->nodes as $node)
            $nodesRepeat[$node->id]=0;

        krsort($nodesRepeat);
        $count = count($this->nodes);
        $delta = intval($topRepeat*$top-$count*$repeatBlock);
        if($delta>0)
        {
            foreach ($nodesRepeat as $nodeKey=>$nodeValue)
            {
                if($delta<0)
                    break;
                $nodesRepeat[$nodeKey]--;
                $delta--;
            }
        }
        return $nodesRepeat;
    }
}
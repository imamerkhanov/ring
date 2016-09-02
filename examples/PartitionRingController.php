<?php
/**
 * @link https://github.com/imamerkhanov
 * @author Ilshat Amerkhanov
 */
use imamerkhanov\ring\partition\Builder;

class PartitionRingController
{
    const ITEMS = 1000000;
    const NODES = 100;

    public function actionIndex($nc=0)
    {
        $s = microtime(1);
        $nodeIDsToNode = [];
        $b = new Builder();

        $nc= empty($nc)?self::NODES:(int)$nc;

        for($n=0;$n<$nc;$n++)
        {
            $bn=$b->AddNode(true, 1, "Node $n");
            $nodeIDsToNode[$bn->id] = $n;
        }


        $ring = $b->Ring();
        $nodeIDsToNode2 = $nodeIDsToNode;

        for($n=$nc;$n<$nc+1;$n++)
        {
            $bn=$b->AddNode(true, 1, "Node $n");
            $nodeIDsToNode2[$bn->id] = $n;
        }
        $ring2 = $b->Ring();

        /**
         * Добавили еще одну ноду
         * Посчитаем количество решардных элементов
         */
        for($i=0;$i<self::ITEMS;$i++)
        {
            $x = $ring->getNodeId($i);
            $ring->countPerNode[$nodeIDsToNode[$x]]++;
        }

        $max = max($ring->countPerNode);
        $min = min($ring->countPerNode);

        $t = self::ITEMS / $nc;

        printf("от %d до %d элементов в одной ноде, при средней = %d.\n", $min,$max,$t);
        printf("Отклонения от %.02f%% и до %.02f%%\n", (($t-$min)*100/$t),(($max-$t)*100/$t));

        $moved=0;
        for($i=0;$i<self::ITEMS;$i++)
        {
            $x = $ring->getNodeId($i);
            $x2 = $ring2->getNodeId($i);
            if($nodeIDsToNode[$x]!=$nodeIDsToNode2[$x2])
                $moved++;
        }

        printf("%d элементов перемещено %.02f%%.\n", $moved,$moved*100/self::ITEMS);

        echo 'Время выполнения ', (microtime(1) - $s), "s\n";

        $s = microtime(1);
        for($i=0;$i<self::ITEMS;$i++)
            $key = $ring->getNodeId($i);

        echo 'Среднее время координатора ', (microtime(1) - $s)/self::ITEMS, "s\n";
    }
}

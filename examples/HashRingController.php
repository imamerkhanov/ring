<?php

use imamerkhanov\ring\hash\HashRing;

class HashRingController
{
    const ITEMS = 1000000;
    const NODES = 100;
    const VIRTUAL_NODES_PER_NODE = 1000;

    public function actionIndex()
    {
        $s = microtime(1);
        $nc= empty($nc)?self::NODES:(int)$nc;

        $ring = new HashRing(
            [
                'nodesCount'=>$nc,
                'virtualNodesCount'=>self::VIRTUAL_NODES_PER_NODE
            ]
        );

        for($i=0;$i<self::ITEMS;$i++)
        {
            $x = $ring->getNodeId($i);
            $ring->countPerNode[$ring->hashesToNode[$ring->nodes[$x]]]++;
        }

        $max = max($ring->countPerNode);
        $min = min($ring->countPerNode);

        $t = self::ITEMS / $nc;

        printf("от %d до %d элементов в одной ноде, при средней = %d.\n", $min,$max,$t);
        printf("Отклонения от %.02f%% и до %.02f%%\n", (($t-$min)*100/$t),(($max-$t)*100/$t));

        /**
         * Добавили еще одну ноду
         * Посчитаем количество решардных элементов
         */
        $ring2 = new HashRing(
            [
                'nodesCount'=>$nc+1,
                'virtualNodesCount'=>self::VIRTUAL_NODES_PER_NODE
            ]
        );

        $moved=0;
        for($i=0;$i<self::ITEMS;$i++)
        {
            $x = $ring->getNodeId($i);
            $x2 = $ring2->getNodeId($i);
            if($ring->hashesToNode[$ring->nodes[$x]]!=$ring2->hashesToNode[$ring2->nodes[$x2]])
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
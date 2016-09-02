<?php
/**
 * @link https://github.com/imamerkhanov
 * @author Ilshat Amerkhanov
 */
use imamerkhanov\ring\standard\StandardRing;

class StandardRingController
{
    const ITEMS = 1000000;
    const NODES = 100;

    public function actionIndex()
    {
        $s = microtime(1);

        $nc= empty($nc)?self::NODES:(int)$nc;
        $ring = new StandardRing(['nodesCount'=>$nc]);

        for($i=0;$i<self::ITEMS;$i++)
            $ring->countPerNode[$ring->getNodeId($i)]++;

        $max = max($ring->countPerNode);
        $min = min($ring->countPerNode);

        $t = self::ITEMS / $nc;

        printf("от %d до %d элементов в одной ноде, при средней = %d.\n", $min,$max,$t);
        printf("Отклонения от %.02f%% и до %.02f%%\n", (($t-$min)*100/$t),(($max-$t)*100/$t));

        /**
         * Добавили еще одну ноду
         * Посчитаем количество решардных элементов
         */
        $moved = 0;

        $ring2 = new StandardRing(['nodesCount'=>$nc+1]);
        for($i=0;$i<self::ITEMS;$i++)
        {
            $key_old = $ring->getNodeId($i);
            $key_new = $ring2->getNodeId($i);
            if($key_old!=$key_new)
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

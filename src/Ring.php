<?php
/**
 * @link https://github.com/imamerkhanov
 * @author Ilshat Amerkhanov
 */
namespace imamerkhanov\ring;

abstract class Ring implements RingInterface
{
    public $nodesCount = 100;
    public $nodes = [];
    public $countPerNode=[];

    protected function __construct(array $params)
    {
        if(!empty($params) and is_array($params))
            foreach ($params as $attribute=>$value)
                if(isset($this->{$attribute}))
                    $this->{$attribute} = $value;

        $this->countPerNode = array_fill(0, $this->nodesCount,0);
    }

    /**
     * Метод генерации хеша
     * @param $item
     * @return mixed
     */
    public function Hash(int $item)
    {
        $hash=hash('fnv1a32',$item);
        return  gmp_strval(gmp_init($hash, 16), 10);
    }

    /**
     * Поиск номера ноды по хешу
     * @param $h
     * @return mixed
     */
    abstract public function findNode(int $h);

    /**
     * Поиск номера ноды по идентификатору
     * @param int $item
     * @return mixed
     */
    public function getNodeId(int $item)
    {
        return $this->findNode($this->Hash($item));
    }

}
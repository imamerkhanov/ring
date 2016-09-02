<?php
/**
 * Created by PhpStorm.
 * User: twk
 * Date: 25.08.16
 * Time: 19:06
 */

namespace imamerkhanov\ring\partition;


class Node
{
    /**
     * Идентификатор
     * @var
     */
    public $id;

    /**
     * Флаг неактивности
     * @var
     */
    public $inactive;

    /**
     * Вес (емкость)
     * @var
     */
    public $capacity;

    /**
     * Мета данные
     * @var
     */
    public $meta;

    /**
     * Node constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
}
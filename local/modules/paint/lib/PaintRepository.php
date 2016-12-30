<?php

namespace Paint;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\SystemException;
use Paint\Entity\PaintEntity as Entity;
use Paint\Orm\PaintTable;

class PaintRepository
{
    /**
     * @var null
     */
    private static $instance = null;

    /**
     * @var string;
     */
    private $dataProvider;

    public static function getInstance() {
        return self::$instance == null ? self::$instance = new static(PaintTable::class) : self::$instance;
    }

    private function __construct($dataProvider)
    {
        $this->setDataProvider($dataProvider);
    }

    public function setDataProvider($dataProvider) {
        $this->dataProvider = $dataProvider;
    }

    public function getDataProvider() {
        return $this->dataProvider;
    }

    /**
     * @param Entity $entity
     */
    public function add(Entity $entity)
    {
        $provider = $this->getDataProvider();

        $addResult = $provider::add(
            array(
                'path' => $entity->getPath(),
                'access' => $entity->getAccess(),
            )
        );

        if ($addResult->isSuccess()) {
            $data = $addResult->getData();

            $entity->setId($addResult->getId());
            $entity->setCreated($data['created']);
            $entity->setUpdated($data['updated']);

            return $entity;
        } else {
            throw new \RuntimeException('Failed add paint');
        }
    }

    public function save(Entity $entity) {
        $updateResult = PaintTable::update($entity->getId(), array('updated' => new \DateTime()));
        if ($updateResult->isSuccess()) {

            $data = $updateResult->getData();
            $entity->setUpdated($data['updated']);

            return $entity;
        } else {
            throw new \RuntimeException('Failed save paint');
        }
    }
    /**
     * @param Entity $entity
     * @return boolean
     */
    public function remove(Entity $entity) {

    }

    /**
     * @return Entity[]
     */
    public function findAll()
    {
        $provider = $this->getDataProvider();
        $providerData = $provider::findAll();
        $entities = [];
        foreach ($providerData as $dataItem) {
            $entities[] = Entity::createFromData($dataItem);
        }

        return $entities;
    }

    public function findById($id = 0) {
        if (!$id) {
            throw new \InvalidArgumentException();
        }

        $provider = $this->getDataProvider();
        $data = $provider::getById($id)->fetch();

        if (!$data) {
            return false;
        }

        return Entity::createFromData($data);
    }
}
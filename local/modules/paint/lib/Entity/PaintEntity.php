<?php
namespace Paint\Entity;

class PaintEntity
{
    private $id;

    private $access;

    private $path;

    private $preview;

    private $created;

    private $updated;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param mixed $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * @param mixed $preview
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public static function createFromData($data) {
        $entity = new self();

        $entity->setId($data['id']);
        $entity->setAccess($data['access']);
        $entity->setPath($data['path']);
        $entity->setPreview($data['preview']);
        $entity->setCreated($data['created']);
        $entity->setUpdated($data['updated']);

        return $entity;
    }

    public function toArray() {
        return [
            'id' => $this->getId(),
            'access' => $this->getAccess(),
            'path' => $this->getPath(),
            'preview' => $this->getPreview(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
        ];
    }
}
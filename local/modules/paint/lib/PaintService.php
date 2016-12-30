<?php
namespace Paint;

use Bitrix\Main\ObjectNotFoundException;
use Paint\Entity\PaintEntity;


class PaintService
{
    private static $instance = null;

    protected $repository;

    protected $documentRoot;

    protected $savePath = '/upload/paint';

    public static function getInstance()
    {
        return self::$instance == null ? self::$instance = new static(PaintRepository::getInstance()) : self::$instance;
    }

    private function __construct($repository)
    {
        $this->setRepository($repository);
        $this->setDocumentRoot($_SERVER['DOCUMENT_ROOT']);
    }

    private function validatePass($password = '') {

        $strError = false;
        if (strlen($password) == 0 && !$strError) {
            $strError = 'Empty password';
        }

        if (strlen($password) < 3 && !$strError) {
            $strError = 'Password is too short';
        }

        if (strlen($password) > 255 && !$strError) {
            $strError = 'Password is too long';
        }

        ///

        if ($strError) {
            throw new \InvalidArgumentException($strError);
        } else {
            return true;
        }
    }

    private function validateBase64Image($base64image = false) {

        if (!$base64image) {
            throw new \InvalidArgumentException('Empty image data');
        }

        $encoded = preg_replace('#^data:image/\w+;base64,#i', '', $base64image);

        $imgData = base64_decode($encoded);

        if (base64_encode($imgData) === $encoded) {
            return $imgData;
        } else {
            throw new \InvalidArgumentException('Invalid image data');
        }
    }

    private function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param mixed $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return mixed
     */
    public function getDocumentRoot()
    {
        return $this->documentRoot;
    }

    /**
     * @param mixed $documentRoot
     */
    public function setDocumentRoot($documentRoot)
    {
        $this->documentRoot = $documentRoot;
    }

    public function getAll() {

        $repo = $this->getRepository();
        $items = $repo::getInstance()->findAll();

        return $items;
    }

    public function addPaint($pass, $base64image)
    {
        $imageResource = $this->validateBase64Image($base64image);

        if ($imageResource && $this->validatePass($pass)) {

            $baseName = md5(microtime());
            $baseDir = sprintf('%s/%s/%s', $this->savePath, substr($baseName, 0, 2), substr($baseName, 2, 2));
            $dirPath = $this->documentRoot . $baseDir;

            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $savePath = sprintf('%s/%s.png', $dirPath, $baseName);

            $result = false;
            if (file_put_contents($savePath, $imageResource)) {
                $entity = new PaintEntity();

                $entity->setAccess($this->hashPassword($pass));
                $entity->setPath(sprintf('%s/%s.png', $baseDir, $baseName));

                $result = $this->getRepository()->add($entity);
            }

            if (!$result) {
                unlink($savePath);
            }

            return $result;
        }
    }

    public function getById($id = 0)
    {
        if (!$id) {
            throw new \InvalidArgumentException('Invalid id param');
        }

        return $this->getRepository()->findById($id);
    }

    public function updatePaint(PaintEntity $paint, $base64image, $storage) {

        $imageResource = $this->validateBase64Image($base64image);

        if ($imageResource && $paint) {

            if (!$this->checkAccess($paint, $storage)) {
                throw new \Exception('Access denied');
            }

            $savePath = $this->documentRoot . $paint->getPath();

            $result = false;
            if (file_put_contents($savePath, $imageResource)) {
                $result = $this->getRepository()->save($paint);
            }

            return $result;
        }

        return false;
    }

    public function checkAccess($paint, $storage)
    {
        if (in_array($this->generateToken($paint), $storage)) {
            return true;
        }

        return false;
    }

    public function generateToken(PaintEntity $paint)
    {
        if (!$paint) {
            throw new \InvalidArgumentException('Invalid paint');
        }

        $token = md5($paint->getId() . $paint->getCreated()->toString());
        return $token;
    }

    public function verifyByPassword($paint, $pass, $token, &$storage = false) {

        if (!$paint) {
            throw new \InvalidArgumentException('Invalid paint');
        }

        if (!$pass || !$token) {
            return false;
        }

        $granted = false;
        $paintToken = $this->generateToken($paint);
        if ($this->verifyPassword($pass, $paint->getAccess()) && $paintToken == $token) {
            $granted = true;
        }

        if ($granted && is_array($storage) && !in_array($paintToken, $storage)) {
            $storage[] = $paintToken;
        }

        return $granted;
    }
}
<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

 Bitrix\Main\Loader::registerAutoLoadClasses('paint', [
     '\Paint\Orm\PaintTable' => '/lib/Orm/PaintTable.php',
     '\Paint\Entity\PaintEntity' => '/lib/Entity/PaintEntity.php',
     '\Paint\PaintService' => '/lib/PaintService.php',
     '\Paint\PaintRepository' => '/lib/PaintRepository.php',
 ]);
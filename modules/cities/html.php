<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */
$sDbTable = 'cities';
$sDbTableOrder = 'city_id';               // Сортировка и идентификация для редактирования

$aPageTableThead = [
    'id',
    'Населённый пункт'
];

/* * * * * * * MODULE CONFIG END * * * * * * */


// Запрос данных для страницы
$oPageData = new qbSelect($sDbTable, [], [], $sDbTableOrder);
$aPageData = $oPageData->runMakeSelectPaginated($this->iPageNo);


// Главная таблица на странице
$oPageTable = new Table($aPageData);
$oPageTable->setAThead($aPageTableThead);
$oPageTable->setEdit($sDbTableOrder, true, false);


$oButtonAdd = new FormButton();
$oButtonAdd->setSCaption('Добавить населённый пункт');
$oButtonAdd->setSColor('blue');
$oButtonAdd->setSHref('/cities/add/');


$oPaginator = new Paginator($oPageData->getCount(), $this->iPageNo);
?>




<?=$oButtonAdd->makeLink()?>
<br><br>

<?=$oPaginator->makePaginator()?>

<?=$oPageTable->makeTable()?>

<?=$oPaginator->makePaginator()?>

<br>
<br>

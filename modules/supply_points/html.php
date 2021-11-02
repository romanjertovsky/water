<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */
$sDbTable = 'supply_points';
$sDbTableOrder = 'point_id';               // Сортировка и идентификация для редактирования


$aPageTableThead = ['id', 'Поставщик', 'Наименование', 'Улица', 'Дом', 'Диспетчерское наименование', 'Ориентир'];


/* * * * * * * MODULE CONFIG END * * * * * * */


// Запрос данных для страницы
$oPageData = new qbSelect($sDbTable, [], [], $sDbTableOrder);
$aPageData = $oPageData->runMakeSelectPaginated($this->iPageNo);


/* * * * * КОНВЕРТАЦИЯ ID В ЗНАЧЕНИЯ ИЗ СПРАВОЧНИКА * * * * */
$aPageData =
    $this->a_id2val(
        $aPageData,
        'provider_id',
        'providers',
        ['provider_name']);


// Главная таблица на странице
$oPageTable = new Table($aPageData);
$oPageTable->setAThead($aPageTableThead);
$oPageTable->setEdit($sDbTableOrder, true, false);


$oButtonAdd = new FormButton();
$oButtonAdd->setSCaption('Добавить ТП');
$oButtonAdd->setSColor('blue');
$oButtonAdd->setSHref('/supply_points/add/');


$oPaginator = new Paginator($oPageData->getCount(), $this->iPageNo);
?>


<?=$oButtonAdd->makeLink()?>
<br><br>

<?=$oPaginator->makePaginator()?>

<?=$oPageTable->makeTable()?>

<?=$oPaginator->makePaginator()?>

<br>
<br>



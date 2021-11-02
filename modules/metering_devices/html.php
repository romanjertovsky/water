<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */
$sDbTable = 'metering_devices';
$sDbTableOrder = 'device_id';               // Сортировка и идентификация для редактирования

$aPageTableThead = [
    'id',
    'Точка поставки',
    'Тип',
    'Заводской номер', 'Номер фидера', 'Дата ввода', 'Дата вывода', 'Начальное показание', 'Заявленный объём'];

/* * * * * * * MODULE CONFIG END * * * * * * */


// Запрос основных данных для страницы
$oPageData = new qbSelect($sDbTable, [], [], $sDbTableOrder);
$aPageData = $oPageData->runMakeSelectPaginated($this->iPageNo);


/* * * * * КОНВЕРТАЦИЯ ID В ЗНАЧЕНИЯ ИЗ СПРАВОЧНИКА * * * * */
$aPageData =
    $this->a_id2val(
            $aPageData,
            'point_id',
            'supply_points',
            ['dispatch_name', 'point_name', 'point_street'],
            ', '
    );


// Главная таблица на странице
$oPageTable = new Table($aPageData);
$oPageTable->setAThead($aPageTableThead);
$oPageTable->setEdit($sDbTableOrder, true, false);



$oButtonAdd = new FormButton();
$oButtonAdd->setSCaption('Добавить ПУ');
$oButtonAdd->setSColor('blue');
$oButtonAdd->setSHref('/metering_devices/add/');


$oPaginator = new Paginator($oPageData->getCount(), $this->iPageNo);
?>


<?=$oButtonAdd->makeLink()?>
<br><br>


<?=$oPaginator->makePaginator()?>

<?=$oPageTable->makeTable()?>

<?=$oPaginator->makePaginator()?>

<br>
<br>

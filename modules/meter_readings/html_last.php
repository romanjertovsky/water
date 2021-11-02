<?php

// TODO прочитать и удалить, нужное зафиксировать

$aFields = ['read_id',      'date',     'device_id',    'value'];
$aThead =  [ '№ в базе',    'Дата',     'Прибор учёта', 'Показания'];


$oQuery = new qbSelect('meter_readings', $aFields, ['user_id' => Core::getUser()->getUserId()]);
$oQuery->setAOrderBy('device_id', true);

/* * * * TODO Интересная идея: похоже, что ниже почти ничего не меняется * * * * */


$aData = $oQuery->runMakeSelectPaginated($this->iPageNo);


$oTable = new Table($aData);
$oTable->setAThead($aThead);
$oTable->setEdit('device_id', true, true);

$oButtonAdd = new FormButton();
$oButtonAdd->setSCaption('Добавить прибор');
$oButtonAdd->setSColor('blue');
$oButtonAdd->setSHref('/metering_devices/add/');


$oPaginator = new Paginator($oQuery->getCount(), $this->iPageNo);


?>

<?=$oTable->makeTable()?>


<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/*
$aReadings = Core::getDb()->selectAll('meter_readings', ['user_id' => Core::getUser()->getUserId()]);
arsort($aReadings);

$aDevices = Core::getDb()->selectAll('metering_devices', ['user_id' => Core::getUser()->getUserId()]);

function getDeviceName($iDeviceId) {
    return Core::getDb()->selectRow('metering_devices', ['device_id', '=', $iDeviceId])['device_name'];
}


if (isset($_POST['go_save'])) {

    $postError = false;

    if (empty($_POST['date'])
    ||
    !DateTime::createFromFormat('Y-m-d', $_POST['date'])
    ) {
        // TODO сделать проверку на то, чтобы дата была не больше сегодняшней и не была слишком далека от сегодняшнего дня
        $postError = true;
        Core::getView()->addAlert("Введите дату.", 2);
    }

    if (empty($_POST['device_id'])) {
        // TODO сделать проверку на существование и доступность прибора
        $postError = true;
        Core::getView()->addAlert("Выберите прибор учёта.",2);
    }

    if (empty($_POST['value'])) {
        // TODO сделать проверку на число и корректность показаний (не меньше предыдущих)
        $postError = true;
        Core::getView()->addAlert("Введите показания.", 2);
    }

    if(!$postError){
        Core::getDb()->addRow('meter_readings',
            [
                'date'    => $_POST['date'],
                'device_id' => $_POST['device_id'],
                'value'   => $_POST['value'],
                'user_id' => Core::getUser()->getUserId()
            ]
        );
        Core::getView()->addAlert('Показания добавлены.', 1);
        Core::getView()->redirect(URL_HOME . 'meter_readings/');
    }


} elseif (
            isset($this->aModuleParams[0])
            &&
            $this->aModuleParams[0] == 'del'
            &&
            isset($this->aModuleParams[1])
            &&
            is_numeric($this->aModuleParams[1])


) { // если запрошен /meter_readings/del/{id}
    // TODO сделать проверку, чтобы было нельзя удалять записи старше чем определённый период, этот период вынести в config_global
    // TODO сделать проверку на удаление несуществующей записи

    Core::getDb()->deleteRow('meter_readings', ['read_id' => $this->aModuleParams[1]]);
    Core::getView()->addAlert('Запись удалена.', 1);
    Core::getView()->redirect(URL_HOME . 'meter_readings/');
}


?>


<form action="" method="post">
    <div class="form-row align-items-end">

        <div class="form-group col-sm-3">
            <label for="date">Дата:</label>
            <input type="date" value="<?=@$_POST['date']?>" class="form-control" id="date" name="date">
        </div>

        <div class="form-group col-sm-3">
            <label for="device_id">Прибор учёта:</label>
            <select class="form-control" id="device_id" name="device_id">
                <option></option>
                <?php
                    foreach ($aDevices as $val) {

                        print "<option value=\"{$val['device_id']}\">{$val['device_name']}</option>";

                    }
                ?>
            </select>
        </div>

        <div class="form-group col-sm-3">
            <label for="value">Показания:</label>
            <input type="text" value="<?=@$_POST['value']?>" class="form-control" id="value" name="value">
        </div>

        <div class="form-group col-sm-3">
            <button type="submit" class="btn btn-primary" id="go_save" name="go_save">Добавить</button>
        </div>

    </div>

</form>


<br><br>
<table class="table">
    <thead>
    <tr>
        <th>№ в базе</th>
        <th>Дата</th>
        <th>Прибор учёта</th>
        <th>Показания</th>
        <th>Удалить запись</th>
    </tr>
    </thead>
    <?php
    foreach ($aReadings as $val) {
        print "
    <tr>
        <td>{$val['read_id']}</td>
        <td>". date("d.m.Y", strtotime($val['date'])) . "</td>
        <td>" . getDeviceName($val['device_id']) . "</td>
        <td>{$val['value']}</td>
        <td><a class='btn btn-dark' href='/meter_readings/del/{$val['read_id']}/'>Удалить</a></td>
    </tr>
";
    }

    ?>

</table>

*/
?>
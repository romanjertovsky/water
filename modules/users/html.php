<?php


    $aFields = ['user_id',     'user_login',   'common_name',  'email',     'position'];   // Поле "роли" будет заполнено и добавлено из другой таблицы ниже
    $aThead =  ['id',          'Login',        'ФИО',          'Почта',     'Должность',     'Роли'];

    $oQuery = new qbSelect('users', $aFields, [], 'user_id');
    $aUsers = $oQuery->runMakeSelectPaginated($this->iPageNo);


    // TODO большое количество запросов - Роли каждого пользователя
    // Поля 'roles' в таблице вообще нету - добавляется и заполняется в цикле ниже
    foreach ($aUsers as $key => $val) {
        $aUsers[$key]['roles'] = implode(
                ', ',
                Core::getUser()->loadRolesFromDb("{$aUsers[$key]['user_id']}")
        );
    }


    $oTableUsers = new Table($aUsers);
    $oTableUsers->setAThead($aThead);
    $oTableUsers->setEdit('user_id', true, false);


    $oButtonAdd = new FormButton();
    $oButtonAdd->setSCaption('Добавить пользователя');
    $oButtonAdd->setSColor('blue');
    $oButtonAdd->setSHref('/users/add/');

    $oPaginator = new Paginator($oQuery->getCount(), $this->iPageNo);

?>

<?=$oButtonAdd->makeLink()?>
<br><br>

<?=$oPaginator->makePaginator()?>

<?=$oTableUsers->makeTable()?>

<?=$oPaginator->makePaginator()?>

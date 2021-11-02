<?php


class Table extends FormCommon
{


    /* VARIABLES */
    private $aData = [];
    private $aThead = [];

    // Edit
    private $sIdName = '';                  // Имя поля c id для идентификации если есть кнопки
    private $bEdit = false;                 // ссылка-кнопка "Редактировать"?
    private $bDelete = false;               // кнопка "Удалить"?
    private $sDeleteName = 'delete_row';    // Delete button name="delete_row"


    /* SETTERS */
    public function setAData(array $aData)
    {
        $this->aData = $aData;
    }
    public function setAThead(array $aThead)
    {
        $this->aThead = $aThead;
    }

    /**
     * @param string $sIdName ID опредиляющий запись
     * @param bool $bEdit true - кнопка редактировать
     * @param bool $bDel true - кнопка удалить
     * @param string $sDeleteName <button name="go_delete"
     */
    public function setEdit(
        string $sIdName,
        bool $bEdit = false,
        bool $bDel = false,
        string $sDeleteName = ''
    )
    {
        $this->sIdName = $sIdName;
        $this->bEdit = $bEdit;
        $this->bDelete = $bDel;
        if(!empty($sDeleteName))
            $this->sDeleteName = $sDeleteName;
    }


    /* METHODS */
    public function __construct(array $aData, $aThead = [])
    {
        $this->aData = $aData;
        $this->aThead = $aThead;
    }


    public function makeTable()
    {

        $sHtml = '';

        if($this->bDelete) // Если будут кнопки удаления, то таблицу помещаем в форму
            $sHtml .= $this->tab() . "<form action=\"\" method=\"post\">\n";

        $sHtml .= $this->tab() . "<table class=\"table\">\n";

        // Если у таблицы установлен заголовок
        if(!empty($this->aThead)) {
            $sHtml .= $this->tab(1) . "<thead>\n" . $this->tab(1) . "<tr>\n";
            foreach ($this->aThead as $td) {
                $sHtml .= $this->tab(2) . "<th>$td</th>\n";
            }
            if($this->bEdit) {
                $sHtml .= $this->tab(2) . "<th>Редактировать</th>\n";
            }
            if($this->bDelete) {
                $sHtml .= $this->tab(2) . "<th>Удалить</th>\n";
            }
            $sHtml .= $this->tab(1) . "</tr>\n" . $this->tab(1) . "</thead>\n";
        }


        if($this->bEdit) {
            $oEditButton = new FormButton('', '', 'Редактировать', 'blue');
            $oEditButton->addClass('edit');
        }

        if($this->bDelete) {
            $oDelButton = new FormButton($this->sDeleteName, 'submit', 'Удалить', 'red');
            $oDelButton->setSConfirmMsg('');
        }


        foreach ($this->aData as $row) {

            $sHtml .= $this->tab(1) . "<tr>\n";

            foreach ($row as $td) {
                $sHtml .= $this->tab(2) . "<td>$td</td>\n";
            }

            $sModName = Core::getController()->getSModName();

            if($this->bEdit) {
                $oEditButton->setSHref("/$sModName/edit/{$row[$this->sIdName]}/");

                $sHtml .= $this->tab(2) . "<td>{$oEditButton->makeLink()}</td>\n";
            }

            if($this->bDelete) {
                $oDelButton->setSValue($row[$this->sIdName]);
                $oDelButton->setSConfirmMsg($this->sConfirmMsg . " (id: {$row[$this->sIdName]})");
                $sHtml .= $this->tab(2) . "<td>{$oDelButton->makeButton()}</td>\n";
            }

            $sHtml .= $this->tab(1) . "</tr>\n";
        }

        $sHtml .= $this->tab() . "</table>\n";
        if($this->bDelete) // Если будут кнопки удаления, то таблицу помещаем в форму
            $sHtml .= $this->tab() . "</form>\n";

        return $sHtml;

    }


}
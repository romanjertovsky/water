<?php


class Form extends FormCommon
{

    /* VARIABLES */
    private $sAction = '';
    private $aNames = [];           //В id пойдёт то-же самое
    private $aTypes = [];
    private $aLabels = [];
    private $bDelete = false;       // кнопка "Удалить"?
    private $sSubmitConfirm = '';
    private $sDeleteConfirm = '';

    private $aValues = [];
    private $aPlaceholders = [];
    private $aSelects = [];         // [aSelects [0 aOptions], [1 aOptions], [2 aOptions]]
    private $aSelected = [];        // [3, 0, 1] - выбранный параметр по умолчанию

    private $sSubmitName = 'go_save';       // Submit button name=""
    private $sDeleteName = 'go_delete';     // Delete button name=""
    private $sSubmitCaption = 'Сохранить';  // Submit button подпись
    private $sDeleteCaption = 'Удалить';  // Submit button подпись


    /* SETTERS */
    public function setSAction(string $sAction)
    {
        $this->sAction = $sAction;
    }
    public function setANames(array $aInputs)
    {
        $this->aNames = $aInputs;
    }
    public function setATypes(array $aTypes)
    {
        $this->aTypes = $aTypes;
    }
    public function setALabels(array $aLabels)
    {
        $this->aLabels = $aLabels;
    }
    public function addAName(string $aInputs)
    {
        $this->aNames[] = $aInputs;
    }
    public function addAType(string $aTypes)
    {
        $this->aTypes[] = $aTypes;
    }
    public function addALabel(string $aLabels)
    {
        $this->aLabels[] = $aLabels;
    }
    public function setBDelete(bool $bDelete)
    {
        $this->bDelete = $bDelete;
    }
    public function setSSubmitConfirm(string $sSubmitConfirm)
    {
        $this->sSubmitConfirm = $sSubmitConfirm;
    }
    public function setSDeleteConfirm(string $sDeleteConfirm)
    {
        $this->sDeleteConfirm = $sDeleteConfirm;
    }
    public function setAValues(array $aValues)
    {
        $this->aValues = $aValues;
    }
    public function addAValue(string $aValues)
    {
        $this->aValues[] = $aValues;
    }
    public function setAPlaceholders(array $aPlaceholders)
    {
        $this->aPlaceholders = $aPlaceholders;
    }
    public function setSSubmitName(string $sSubmitName): void
    {
        $this->sSubmitName = $sSubmitName;
    }
    public function setSDeleteName(string $sDeleteName): void
    {
        $this->sDeleteName = $sDeleteName;
    }
    public function setSSubmitCaption(string $sSubmitCaption): void
    {
        $this->sSubmitCaption = $sSubmitCaption;
    }
    public function setSDeleteCaption(string $sDeleteCaption): void
    {
        $this->sDeleteCaption = $sDeleteCaption;
    }



    /** Добавляет массив для полей &lt;option value="111"&gt;Aaa&lt;/option&gt;
     *  Метод вызывать обязательно если в форме есть &lt;select&gt;
     *  Если &lt;select&gt;-ов несколько, то метод вызывать несколько раз.
     * @param $aOptions array Двумерный массив-справочник для select-ов: [[1, 'val'], [2, 'val']]
     */
    public function addASelect($aOptions)
    {
        $this->aSelects[] = $aOptions;
    }

    /**
     * @param int $iSelected id значение уже выбранного поля
     */
    public function addSelected(int $iSelected)
    {
        $this->aSelected[] = $iSelected;
    }

    /* METHODS */
    /**
     * Устанавливает пустые значения '' в массивы $this->aLabels, $this->aValues и aPlaceholders
     * чтобы кол-во элементов везде совпадало с $this->aNames
     */
    private function setEmptyDefaults()
    {
        foreach ($this->aNames as $key => $val) {
            if (!isset($this->aLabels[$key]))
                $this->aLabels[$key] = '';
            if (!isset($this->aValues[$key]))
                $this->aValues[$key] = '';
            if (!isset($this->aPlaceholders[$key]))
                $this->aPlaceholders[$key] = '';
        }
    }


    /** Генерит <input> разных типов
     * @param int $iInputKey ключ поля в $this->aNames
     * @param int $iTab кол-во табуляций перед элементом
     * @return string
     */
    private function makeInput($iInputKey, $iTab = 0)
    {

        $sType = $this->aTypes[$iInputKey];

        // начало <input id="" name="" valaue="" placeholder="" ...
        $sInput  =
            $this->tab($iTab) .
            "<input id=\"{$this->aNames[$iInputKey]}\" " .
            "name=\"{$this->aNames[$iInputKey]}\" ".
            "value=\"{$this->aValues[$iInputKey]}\" " .
            (
                empty($this->aPlaceholders[$iInputKey])
                ?
                ''
                :
                "placeholder=\"{$this->aPlaceholders[$iInputKey]}\" "
            );

        // завершение ... type="" class="">
        switch ($sType) {

            case 'readonly':
                $sInput .= "type=\"text\" class=\"form-control-plaintext text-white\" readonly>";
                break;

            case 'text':
                $sInput .= "type=\"text\" class=\"form-control\">";
                break;

            case 'date':
                $sInput .= "type=\"date\" class=\"form-control\">";
                break;

            case 'password':
                $sInput .= 'type="password" class="form-control">';
                break;

            case 'email':
                $sInput .= 'type="email" class="form-control">';
                break;

            case 'select':

                if(empty($this->aSelects))
                    die('<strong>Form()->makeInput(), не хватает данных для заполнения полей select/options.' .
                        "Выполните \$oForm->addASelect([[1, 'val'], [2, 'val']]);</strong>");

                $aOptions = array_shift($this->aSelects);
                $iOptionSelected = array_shift($this->aSelected);

                $sInput  = $this->tab($iTab) . "<select class=\"form-control\" id=\"{$this->aNames[$iInputKey]}\" name=\"{$this->aNames[$iInputKey]}\">\n";

                $sInput .= $this->tab($iTab + 1) . "<option></option>\n";

                foreach ($aOptions as $key => $val) {
                    if($val[0]==$iOptionSelected) {
                        $sSelected = ' selected="selected"';
                    } else {
                        $sSelected = '';
                    }

                    $sInput .=
                        $this->tab($iTab + 1) .
                        "<option value=\"{$val[0]}\"$sSelected>{$val[1]}</option>\n";
                }

                $sInput .= $this->tab($iTab) . "</select>";
                break;


            default:
                $sInput = 'Ошибка генерации формы. ' . $sType . ' - несуществующий тип поля!';

        }

        return $sInput;

    }


    private function makeFormsBegin()
    {
        $this->setEmptyDefaults();
        return "<form method=\"post\" action=\"$this->sAction\"" .
            (empty($this->sConfirmMsg)?
                '':
                " onsubmit=\"return confirm(`{$this->sConfirmMsg}`);\""
            ) .
            ">\n";
    }


    private function makeButtonSubmit()
    {
        $oButtonSubmit = new FormButton($this->sSubmitName, 'submit', $this->sSubmitCaption, 'blue');
        if(!empty($this->sSubmitConfirm))
            $oButtonSubmit->setSConfirmMsg($this->sSubmitConfirm);
        return $oButtonSubmit->makeButton();
    }


    private function makeButtonDelete()
    {
        $oButtonDel = new FormButton($this->sDeleteName, 'submit', $this->sDeleteCaption, 'red');
        if (!empty($this->sDeleteConfirm))
            $oButtonDel->setSConfirmMsg($this->sDeleteConfirm);
        return $oButtonDel->makeButton();
    }


    /**
     * Генерирует вертикальную форму
     * @param int $iColLabel
     * @param int $iColInput
     * @return string
     */
    public function makeVerticalForm($iColLabel = 1, $iColInput = 1)
    {

        $sForm  = $this->tab(0) . $this->makeFormsBegin();

        foreach ($this->aNames as $key => $val) {
            $sForm .= $this->tab(1) . "<div class=\"form-group row\">\n";
            $sForm .= $this->tab(2) . "<label for=\"$val\" class=\"col-sm-$iColLabel col-form-label\">{$this->aLabels[$key]}</label>\n";
            $sForm .= $this->tab(2) . "<div class=\"col-sm-$iColInput\">\n";
            $sForm .= $this->tab(0) . $this->makeInput($key, 3) . "\n";
            $sForm .= $this->tab(2) . "</div>\n";
            $sForm .= $this->tab(1) . "</div>\n";
        }

        $sForm .= $this->tab(1) . "<div class=\"col-sm-" . ($iColLabel+$iColInput) . "\">\n";
        $sForm .= $this->tab(2) . $this->makeButtonSubmit() . "\n";
        if($this->bDelete)
            $sForm .= $this->tab(2) . $this->makeButtonDelete() . "\n";


        $sForm .= $this->tab(1) . "</div>\n";

        $sForm .= $this->tab(0) . "</form>\n";

        return $sForm;
    }

    /**
     * Генерирует горизонтальную форму
     * @param int $iCol Ширина полей, число для класса "col-sm-$iCol", от 1 до 12.
     * @return string
     */
    public function makeHorizontalForm($iCol = 1)
    {

        $sForm  = $this->tab(0) . $this->makeFormsBegin();
        $sForm .= $this->tab(1) . "<div class=\"form-row align-items-end\">\n";

        foreach ($this->aNames as $key => $val) {
            $sForm .= $this->tab(2) . "<div class=\"form-group col-sm-$iCol\">\n";
            $sForm .= $this->tab(3) . "<label for=\"$val\">{$this->aLabels[$key]}</label>\n";
            $sForm .= $this->tab(0) . $this->makeInput($key, 3) . "\n";
            $sForm .= $this->tab(2) . "</div>\n";
        }

        $sForm .= $this->tab(2) . "<div class=\"form-group col-sm-$iCol\">\n";
        $sForm .= $this->tab(2) . $this->makeButtonSubmit() . "\n";
        if($this->bDelete)
            $sForm .= $this->tab(2) . $this->makeButtonDelete() . "\n";
        $sForm .= $this->tab(2) . "</div>\n";


        $sForm .= $this->tab(1) . "</div>\n";
        $sForm .= $this->tab(0) . "</form>\n";

        return $sForm;

    }

}
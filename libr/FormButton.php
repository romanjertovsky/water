<?php


class FormButton extends FormCommon
{


    /* VARIABLES */
    private $sId = '';          // #id
    private $sName = '';        // в id пойдёт то же самое
    private $sType = '';        // button или submit, если submit не забыть про $sConfirm
    private $sCaption = '';
    private $sColor = '';       // blue, red или gray
    private $sHref = '';
    private $sValue = '';       // <button value="">


    /* SETTERS */
    public function setSId(string $sId)
    {
        $this->sId = $sId;
    }
    public function setSName(string $sName)
    {
        $this->sName = $sName;
    }
    public function setSType(string $sType)
    {
        $this->sType = $sType;
    }
    public function setSCaption(string $sCaption)
    {
        $this->sCaption = $sCaption;
    }
    public function setSColor(string $sColor)
    {
        $this->sColor = $sColor;
    }
    public function setSHref(string $sHref)
    {
        $this->sHref = $sHref;
    }
    public function setSValue(string $sValue)
    {
        $this->sValue = $sValue;
    }


    /* METHODS */
    public function __construct($sName = '', $sType = '', $sCaption = '', $sColor = '')
    {   // конструктор принимает только четыре основных, и то не обязательных, параметра!
        $this->sName = $sName;
        $this->sType = $sType;
        $this->sCaption = $sCaption;
        $this->sColor = $sColor;
    }


    private function setClasses() {

        $this->addClass('btn');

        switch ($this->sColor) {
            case 'blue':
                $this->addClass('btn-primary');
                break;
            case 'grey':
                $this->addClass('btn-dark');
                break;
            case 'red':
                $this->addClass('btn-danger');
                break;
        }

    }


    public function makeLink()
    {
        $this->setClasses();
        return $this->tab() . "<a class=\"{$this->getClasses()}\" href=\"{$this->sHref}\">{$this->sCaption}</a>";
    }


    public function makeButton($sExtParams = '')
    {
        $this->setClasses();
        return $this->tab() . "<button type=\"{$this->sType}\" class=\"{$this->getClasses()}\"" .

            (empty($this->sId)?
                '':
                " id=\"{$this->sId}\""
            ) .

            (empty($this->sName)?
                '':
                " name=\"{$this->sName}\""
            ) .

            (empty($this->sValue)?
                '':
                " value=\"{$this->sValue}\""
            ) .

            (empty($this->sConfirmMsg)?
                '':
                " onclick=\"return confirm(`{$this->sConfirmMsg}`);\""
            ) .

            (empty($sExtParams)?
                '':
                ' ' . $sExtParams
            ) .

            ">{$this->sCaption}</button>";
    }


    public function makeButtonForModal()
    {
        return $this->makeButton("data-toggle=\"modal\" data-target=\"#modal_{$this->sName}\"");
    }


    /**
     * В
     * @param string $sTitle Загголовок окна
     * @param string $sBody Текст окна
     * @param string $sOkButton код кнопки Ok/submit
     * @param int $iTab
     * @return string код модального окна
     */
    public function makeModalForButton($sTitle = '', $sBody = '', $sOkButton = '', $iTab = 0)
    {
        $oModal = new Modal();
        $oModal->setITabs($iTab);
        $oModal->setSId($this->sName);

        $oModal->setSTitle($sTitle);
        $oModal->setSBody($sBody);

        $oModal->setSOk($sOkButton);

        return $oModal->makeModal();
    }


}
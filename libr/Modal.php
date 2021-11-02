<?php


class Modal extends FormCommon
{

    /* VARIABLES */
    private $sId = '';         // #identificator
    private $sTitle = '';
    private $sBody  = '';
    private $sOkButton = '';


    /* SETTERS */
    public function setSId(string $sId)
    {
        $this->sId = $sId;
    }
    public function setSTitle(string $sTitle)
    {
        $this->sTitle = $sTitle;
    }
    public function setSBody(string $sBody)
    {
        $this->sBody = $sBody;
    }
    public function setSOk(string $sOk)
    {
        $this->sOkButton = $sOk;
    }


    /* FUNCTIONS */
    public function __construct($sId = '', $sTitle = '', $sBody = '', $sOkButton = '')
    {
        $this->sId = $sId;
        $this->sTitle = $sTitle;
        $this->sBody = $sBody;
        $this->sOkButton = $sOkButton;
    }


    public function makeModal()
    {

        $sModal  = $this->tab(0) . "<!-- Modal -->\n";
        $sModal .= $this->tab(0) . '<div class="modal fade" id="modal_' . $this->sId . '" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">' . "\n";
        $sModal .= $this->tab(1) . '<div class="modal-dialog">' . "\n";
        $sModal .= $this->tab(2) . '<div class="modal-content">' . "\n";
        $sModal .= $this->tab(3) . '<div class="modal-header">' . "\n";
        $sModal .= $this->tab(4) . "<h5 class=\"modal-title\" id=\"staticBackdropLabel\">{$this->sTitle}</h5>" . "\n";
        $sModal .= $this->tab(4) . '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' . "\n";
        $sModal .= $this->tab(5) . '<span aria-hidden="true">&times;</span>' . "\n";
        $sModal .= $this->tab(4) . '</button>' . "\n";
        $sModal .= $this->tab(3) . '</div>' . "\n";

        if(!empty($this->sBody)) {
            $sModal .= $this->tab(3) . '<div class="modal-body">' . "\n";
            $sModal .= $this->tab(4) . $this->sBody . "\n";
            $sModal .= $this->tab(3) . '</div>' . "\n";
        }

        $sModal .= $this->tab(3) . '<div class="modal-footer">' . "\n";
        $sModal .= $this->tab(4) . '<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>' . "\n";
        if($this->sOkButton)
            $sModal .= $this->tab(4) . $this->sOkButton . "\n";
        $sModal .= $this->tab(3) . '</div>' . "\n";

        $sModal .= $this->tab(2) . '</div>' . "\n";
        $sModal .= $this->tab(1) . '</div>' . "\n";
        $sModal .= $this->tab(0) . '</div>' . "\n";

        return $sModal;

    }


    public function makeJs() {





    }


}
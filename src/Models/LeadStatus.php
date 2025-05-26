<?php

namespace Models;

use DateTime;

class LeadStatus {

    public int $leadStatusId;
    public string $status;
    public DateTime $dataCadastroStatus;
    public int $leadId;

    public function __construct()
    {
        $this->leadId = 0;
        $this->leadStatusId = 0;
        $this->status = "";
        $this->dataCadastroStatus = new DateTime("now");
    }

}

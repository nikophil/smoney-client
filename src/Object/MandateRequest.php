<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class MandateRequest extends AbstractHydratable
{

    /**
     * MandateRequest ID
     *
     * @var int
     */
    public $id;

    /**
     * Url to call in order to electronically sign the mandate
     *
     * @var string
     */
    public $href;

    /**
     * BankAccount information
     *
     * @var BankAccount
     */
    public $bankAccount;

    /**
     * Request's date
     *
     * @var \DateTime
     */
    public $date;

    /**
     * MandateRequest Status
     *
     * Values :
     * 0 = Incomplete
     * 1 = Waiting
     * 2 = Refused
     * 3 = Accepted
     * 
     * @var int
     */
    public $status;

    /**
     * Mandate Single Reference
     *
     * @var string
     */
    public $UMR;
}

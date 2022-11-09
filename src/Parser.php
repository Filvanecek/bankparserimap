<?php

namespace ImapBankParser;

use ImapBankParser\Banks\Bank;

class Parser extends Bank
{
    public function __construct(
        string $hostname,
        string $username,
        string $password,
        $port = '993',
        $flags = '/imap/ssl/validate-cert',
        $parameters = []
    )
    {
        parent::__construct($hostname, $username, $password, $port, $flags, $parameters);
    }


}
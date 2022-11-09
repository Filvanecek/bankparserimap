<?php

namespace ImapBankParser\Banks;

use Ddeboer\Imap\Message;
use ImapBankParser\Transaction;

class Csob extends Parser
{
    protected function parseOne(Message $message): ?Transaction
    {
        $rows = $this->getRows($message, 3);
        $transaction = new Transaction($message);

        foreach ($rows as $row)
        {
            [$key, $value] = $row;

            switch ($key)
            {
                case "Účet":
                    $transaction->setAccount($value);
                    break;
                case "Účet protistrany":
                    $transaction->setSender($value);
                    break;
                case "Částka":
                    $price = $this->parsePrice($value);
                    $transaction->setPrice(abs($price));
                    $transaction->setType($price > 0 ? "incoming" : "outcoming");
                    break;
                case "Zpráva pro příjemce":
                    $transaction->setMessage($value);
                    break;
                case "Variabilní symbol":
                    $transaction->setVariableSymbol($value);
                    break;
                case "Konstantní symbol":
                    $transaction->setOtherSymbols("constant", $value);
                    break;
                case "Specifický symbol":
                    $transaction->setOtherSymbols("specific", $value);
                    break;
            }
        }

        return $transaction->getBank() === "0300" ?
            $transaction : null;
    }
}

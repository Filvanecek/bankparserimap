<?php

namespace ImapBankParser\Banks;

use Ddeboer\Imap\Message;
use ImapBankParser\Transaction;

class Kb extends Parser
{
    protected function parseOne(Message $message): ?Transaction
    {
        $content = $message->getBodyHtml();
        $rows = $this->getRows($message, 13, 2, 3);

        $transaction = new Transaction($message);
        $transaction->setType(stripos($content, "Příchozí platba") === false ? "outcoming" : "incoming");

        foreach ($rows as $row)
        {
            [$key, $value] = $row;

            switch ($key)
            {
                case "Částka a měna:":
                    $price = $this->parsePrice($value);
                    $transaction->setPrice(abs($price));
                    break;
                case "Číslo vašeho účtu:":
                    $transaction->setAccount($value);
                    break;
                case "Číslo protiúčtu:":
                    $transaction->setSender($value);
                    break;
                case "Variabilní symbol:":
                    $transaction->setVariableSymbol($value);
                    break;
                case "Konstantní symbol:":
                    $transaction->setOtherSymbols("constant", $value);
                    break;
                case "Specifický symbol:":
                    $transaction->setOtherSymbols("specific", $value);
                    break;
                case "Zpráva pro příjemce:":
                    $transaction->setMessage($value);
                    break;
            }
        }

        return $transaction->getBank() === "0100" ?
            $transaction : null;
    }
}

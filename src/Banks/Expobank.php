<?php

namespace ImapBankParser\Banks;

use Ddeboer\Imap\Message;
use ImapBankParser\Transaction;

class Expobank extends Parser
{
    protected function parseOne(Message $message): ?Transaction
    {
        $rows = $this->getRows($message, 0);
        $transaction = new Transaction($message);

        foreach ($rows as $row)
        {
            [$key, $value, $keyElement, $valueElement] = $row;

            if ($keyElement === $valueElement && strpos($key, " ") !== false)
                [$key, $value] = explode(" ", $key, 2);

            switch ($key)
            {
                case "Upozornění":
                    $transaction->setType(stripos($value, "Příchozí") === false ? "outcoming" : "incoming");
                    break;
                case "Účet":
                    $account = explode(", ", $value);

                    if (isset($account[1]))
                    {
                        [$account] = explode(" ", $account[1]);
                        $transaction->setAccount($account);
                    }

                    break;
                case "Částka:":
                    $price = $this->parsePrice($value);
                    $transaction->setPrice($price);
                    break;
                case "Účet protistrany:":
                    $transaction->setSender($value);
                    break;
                case "Zpráva pro příjemce:":
                    $transaction->setMessage($value);
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
            }
        }

        return $transaction->getBank() === "4000" ?
            $transaction : null;
    }
}

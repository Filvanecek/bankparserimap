<?php

namespace ImapBankParser\Banks;

use Ddeboer\Imap\Message;
use ImapBankParser\Transaction;

class Moneta extends Parser
{
    protected function parseOne(Message $message): ?Transaction
    {
        $rows = $this->getRows($message, 6);
        $transaction = new Transaction($message);

        foreach ($rows as $row)
        {
            [$key, $value] = $row;

            switch ($key)
            {
                case "Účet:":
                    $transaction->setAccount($value);
                    break;
                case "Částka:":
                    $price = $this->parsePrice($value);
                    $transaction->setPrice(abs($price));
                    $transaction->setType($price > 0 ? "incoming" : "outcoming");
                    break;
                case "Popis:":
                    $keyValues = $this->parseLine($value, ", ", ":");

                    foreach ($keyValues as $keyValue)
                    {
                        [$partKey, $partValue] = $keyValue;

                        switch ($partKey)
                        {
                            case "DOM-AVIZO":
                                $transaction->setMessage($partValue);
                                break;
                            case "VS":
                                $transaction->setVariableSymbol($partValue);
                                break;
                            case "KS":
                                $transaction->setOtherSymbols("constant", $partValue);
                                break;
                            case "SS":
                                $transaction->setOtherSymbols("specific", $partValue);
                                break;
                        }
                    }

                    break;
                case "Od:":
                    $transaction->setSender($value);
                    break;
                case "Disponibilní zůstatek:":
                    $price = $this->parsePrice($value);
                    $transaction->setBalance($price);
                    break;
            }
        }

        return $transaction->account ?
            $transaction : null;
    }
}

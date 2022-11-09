<?php

namespace ImapBankParser\Banks;

use Ddeboer\Imap\Message;
use ImapBankParser\Transaction;

class Creditas extends Parser
{
    protected function parseOne(Message $message): ?Transaction
    {
        $content = $message->getBodyText();
        $lines = explode("\n", $content);
        $lines = array_map(fn ($line) => iterator_to_array($this->parseLine($line, "- ", ": ")), $lines);
        $keyValues = array_merge(...$lines);
        $transaction = new Transaction($message);


        if (stripos($content, "(Odchozí úhrada)") !== false)
            $transaction->setType("outcoming");
        else if (stripos($content, "(Příchozí úhrada)") !== false)
            $transaction->setType("incoming");

        foreach ($keyValues as $keyValue)
        {
            [$key, $value] = $keyValue;

            switch ($key)
            {
                case "změna na účtu":
                    $transaction->setAccount($value);
                    break;
                case "účet protistrany":
                    $transaction->setSender(strpos($transaction->sender, "/") === false ? $value : $value.$transaction->sender);
                    break;
                case "částka":
                    $price = $this->parsePrice($value);
                    $transaction->setPrice(abs($price));
                    break;
                case "VS":
                    $transaction->setVariableSymbol($value);
                    break;
                case "KS":
                    $transaction->setOtherSymbols("constant", $value);
                    break;
                case "SS":
                    $transaction->setOtherSymbols("specific", $value);
                    break;
                case "zpráva pro příjemce":
                    $transaction->setMessage($value);
                    break;
                case "disponibilní zůstatek":
                    $price = $this->parsePrice($value);
                    $transaction->setBalance($price);
                    break;
            }
        }

        return $transaction->getBank() === "2250" ?
            $transaction : null;
    }
}

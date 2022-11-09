<?php

namespace ImapBankParser\Banks;

use Ddeboer\Imap\Message;
use ImapBankParser\Transaction;

class Raiffeisen extends Parser
{
    private function parseAccount(\DOMElement $element): ?string
    {
        $p = $element->getElementsByTagName("p")->item(0);

        if (!$p)
            return null;

        $account = $p->childNodes->item(0);

        if (!$account)
            return null;

        return trim($account->textContent);
    }

    protected function parseOne(Message $message): ?Transaction
    {
        $rows = $this->getRows($message, 8);
        $transaction = new Transaction($message);

        foreach ($rows as $row)
        {
            [$key, $value, $keyElement, $valueElement] = $row;

            switch ($key)
            {
                case "Na účet":
                    $account = $this->parseAccount($valueElement);
                    $transaction->setAccount($account);
                    break;
                case "Částka v měně účtu":
                    $price = $this->parsePrice($value);
                    $transaction->setPrice($price);
                    break;
                case "Typ pohybu":
                    $transaction->setType(stripos($value, "Příchozí") === false ? "outcoming" : "incoming");
                    break;
                case "Z účtu":
                    $account = $this->parseAccount($valueElement);
                    $transaction->setSender($account);
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
                case "Zpráva pro příjemce":
                    $transaction->setMessage($value);
                    break;
            }
        }

        return $transaction->getBank() === "5500" ?
            $transaction : null;
    }
}

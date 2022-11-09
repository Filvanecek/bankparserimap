<?php

namespace ImapBankParser\Banks;

use Ddeboer\Imap\Message;
use ImapBankParser\Transaction;

abstract class Parser
{
    private array $messages = [];
    private array $parsedMessages = [];

    /**
     * Set messages from email
     *
     * @param array $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    /**
     * Parse emails
     *
     * @return $this
     */
    public function parseEmails()
    {
        foreach($this->messages as $message) {
            $transaction = $this->parseOne($message);

            if ($transaction && $transaction->validate())
                $this->parsedMessages[] = $transaction;
        }

        return $this;
    }

    /**
     * Return as array
     *
     * @return array
     */
    public function getParsed(): array
    {
        return $this->parsedMessages;
    }

    /**
     * Parse email
     *
     * @param Message $message
     * @return Transaction|null
     */
    abstract protected function parseOne(Message $message): ?Transaction;

    protected function parsePrice(string $price): float
    {
        $price = str_replace(",", ".", $price);
        $price = preg_replace("/[^0-9.+-]/", "", $price);
        $price = floatval($price);

        return $price;
    }

    protected function getRows(Message $message, int $index, int $keyIndex = 0, int $valueIndex = 1): \Generator
    {
        $content = "<?xml encoding=\"utf-8\" ?>";
        $content .= str_ireplace("charset=Windows-1250", "charset=UTF-8", $message->getBodyHtml());
        $document = new \DOMDocument();

        if (!$document->loadHTML($content))
            return;

        $tables = $document->getElementsByTagName("table");
        $table = $tables->item($index);

        if (!($table instanceof \DOMElement))
            return;

        $rows = $table->getElementsByTagName("tr");

        foreach ($rows as $row)
        {
            if (!($row instanceof \DOMElement))
                continue;

            $cells = $row->getElementsByTagName("td");
            $keyElement = $cells->item($keyIndex);
            $valueElement = $cells->item($valueIndex) ?? $keyElement;

            if (!($keyElement instanceof \DOMElement) || !($valueElement instanceof \DOMElement))
                continue;

            $key = trim($keyElement->textContent);
            $value = trim($valueElement->textContent);

            yield [$key, $value, $keyElement, $valueElement];
        }
    }

    protected function parseLine(string $line, string $separator, string $keyValueSeparator): \Generator
    {
        $parts = explode($separator, $line);

        foreach ($parts as $part)
        {
            $keyValue = $this->parseKeyValue($part, $keyValueSeparator);

            if ($keyValue)
                yield $keyValue;
        }
    }

    protected function parseKeyValue(string $part, string $separator): ?array
    {
        $keyValue = explode($separator, $part);

        if (!isset($keyValue[1]))
            return null;

        [$key, $value] = $keyValue;
        $value = trim($value);

        return [$key, $value];
    }
}

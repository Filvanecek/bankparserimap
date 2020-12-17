<?php

namespace ImapBankParser\Bank;

use Ddeboer\Imap\Message;
use ImapBankParser\Banks\Rules;
use ImapBankParser\Transaction;

class Fio extends Rules
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
            $this->parsedMessages[] = $this->parseOne($message);
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
     * @return Message|bool
     */
    private function parseOne(Message $message)
    {
        $transaction = new Transaction();
        $data = explode(PHP_EOL, quoted_printable_decode($message->getContent()));
        foreach(array_filter($data) as $line) {
            $line = explode(':', $line);
            $this->identifyLine($transaction, $line);
        }

        if($transaction->validate()) {
            return $transaction;
        }

        return FALSE;
    }
}
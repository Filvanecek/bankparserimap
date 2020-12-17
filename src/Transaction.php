<?php


namespace ImapBankParser;


class Transaction
{
    public string $type = '';
    public int $account = 0;
    public float $price = 0;
    public int $variable_symbol = 0;
    public string $message = '';
    public float $balance = 0;
    public string $sender = '';
    public string $note = '';
    public array $other_symbols;

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setAccount(int $account): void
    {
        $this->account = $account;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setVariableSymbol(int $variable_symbol): void
    {
        $this->variable_symbol = $variable_symbol;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    public function setSender(string $sender): void
    {
        $this->sender = $sender;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }

    public function setOtherSymbols(string $symbol, string $value): void
    {
        $this->other_symbols[$symbol] = !empty($value) ? $value : null;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if(!floatval($this->price) || $this->price == 0) {
            return FALSE;
        }

        return TRUE;
    }
}
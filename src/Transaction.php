<?php


namespace ImapBankParser;

use Ddeboer\Imap\Message;

class Transaction
{
	public Message $email;
	public string $type = '';
	public string $account = '';
	public float $price = 0;
	public string $variable_symbol = '';
	public string $message = '';
	public float $balance = 0;
	public string $sender = '';
	public string $note = '';
	public array $other_symbols;

	public function __construct(Message $email)
	{
		$this->email = $email;
	}

	public function setType(string $type): void
	{
		$this->type = $type;
	}

	public function setAccount(string $account): void
	{
		$this->account = $account;
	}

	public function setPrice(float $price): void
	{
		$this->price = $price;
	}

	public function setVariableSymbol(string $variable_symbol): void
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

	public function getBank(): ?string
	{
		$bank = explode("/", $this->account);
		return $bank[1] ?? null;
	}

	/**
	 * @return bool
	 */
	public function validate() {
		if(!floatval($this->price) || $this->price == 0) {
			return FALSE;
		}

		return TRUE;
	}
}

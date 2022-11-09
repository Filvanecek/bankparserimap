<?php

namespace ImapBankParser\Banks;

use Ddeboer\Imap\Message;
use ImapBankParser\Transaction;

class Fio extends Parser
{
	const ACTIONS = [
		'Příjem na kontě' => 'incoming',
		'Výdaj na kontě' => 'outcoming'
	];

	const PRICES = [
		'Částka' => 'price'
	];

	const VARIABLE_SYMBOLS = [
		'VS' => 'variable_symbol'
	];

	const MESSAGES = [
		'Zpráva příjemci' => 'message'
	];

	const BALANCES = [
		'Aktuální zůstatek' => 'balance'
	];

	const SENDERS = [
		'Protiúčet' => 'sender'
	];

	const OTHER_SYMBOLS = [
		'SS' => 'specific',
		'KS' => 'constant',
	];

	const NOTES = [
		'US' => 'note'
	];

	private function identifyLine(Transaction $transaction, $line)
	{
		if(isset(self::ACTIONS[$line[0]])) {
			$transaction->setType(self::ACTIONS[$line[0]]);
			$transaction->setAccount(trim($line[1]));
		}

		if(isset(self::PRICES[$line[0]])) {
			$price = str_replace(' ', '', trim($line[1]));
			$price = str_replace(',', '.', $price);
			$transaction->setPrice((float) $price);
		}

		if(isset(self::VARIABLE_SYMBOLS[$line[0]])) {
			$transaction->setVariableSymbol(trim($line[1]));
		}

		if(isset(self::MESSAGES[$line[0]])) {
			$transaction->setMessage(trim($line[1]));
		}

		if(isset(self::BALANCES[$line[0]])) {
			$balance = str_replace(' ', '', trim($line[1]));
			$balance = str_replace(',', '.', $balance);
			$transaction->setBalance((float) $balance);
		}

		if(isset(self::SENDERS[$line[0]])) {
			$transaction->setSender(trim($line[1]));
		}

		if(isset(self::OTHER_SYMBOLS[$line[0]])) {
			$transaction->setOtherSymbols(self::OTHER_SYMBOLS[$line[0]],trim($line[1]));
		}

		if(isset(self::NOTES[$line[0]])) {
			$transaction->setNote(trim($line[1]));
		}
	}

	/**
	 * Parse email
	 *
	 * @param Message $message
	 * @return Transaction|null
	 */
	protected function parseOne(Message $message): ?Transaction
	{
		$transaction = new Transaction($message);
		$data = explode(PHP_EOL, $message->getBodyText());
		foreach(array_filter($data) as $line) {
			$line = explode(':', $line);
			$this->identifyLine($transaction, $line);
		}

		return $transaction->account ?
			$transaction : null;
	}
}

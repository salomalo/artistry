<?php

namespace mycryptocheckout\api;

/**
	@brief		A payment to be sent to the server for monitoring.
	@since		2017-12-21 23:35:59
**/
class Payment
{
	/**
		@brief		The cryptocurrency amount to watch for as a string.
		@details	"4.223432" or "12" or "5000000"
					We use strings since floats are awfully unreliable.
		@since		2017-12-21 23:36:58
	**/
	public $amount;

	/**
		@brief		How many confirmations to require to be regarded as paid.
		@details	Default is 1.
		@since		2017-12-24 12:14:06
	**/
	public $confirmations;

	/**
		@brief		Unix time when this payment was created.
		@since		2017-12-24 11:46:54
	**/
	public $created_at;

	/**
		@brief		The ID of the currency we are expecting the payment in: BTC, ETH, etc.
		@since		2017-12-21 23:36:56
	**/
	public $currency_id;

	/**
		@brief		Any extra data we want to send to the server.
		@see		data()
		@see		Payment_Data
		@since		2017-12-30 19:41:56
	**/
	public $data;

	/**
		@brief		The payment timeout in hours.
		@details	0 is the default, which takes the API default.
		@since		2018-03-16 15:57:26
	**/
	public $timeout_hours = 0;

	/**
		@brief		The wallet address to which we are expecting payment.
		@since		2017-12-21 23:36:54
	**/
	public $to;

	/**
		@brief		Convenience method to return the data handling object for this payment.
		@see		Payment_Data
		@since		2017-12-30 19:45:00
	**/
	public function data()
	{
		return new Payment_Data( $this );
	}

	/**
		@brief		Replace the instruction shortcodes in this instruction string.
		@details	Used to replace shortcodes in webshop payment instructions.
		@since		2018-01-03 12:05:12
	**/
	public function replace_shortcodes( $instructions )
	{
		$instructions = str_replace( '[AMOUNT]', $this->amount, $instructions );
		$instructions = str_replace( '[CURRENCY]', $this->currency_id, $instructions );
		$instructions = str_replace( '[TO]', $this->to, $instructions );
		return $instructions;
	}

	/**
		@brief		Return this object as an array.
		@since		2017-12-21 23:37:48
	**/
	public function to_array()
	{
		return [
			'amount' => $this->amount,
			'confirmations' => $this->confirmations,
			'created_at' => $this->created_at,
			'currency_id' => $this->currency_id,
			'data' => $this->data,
			'timeout_hours' => $this->timeout_hours,
			'to' => $this->to,
		];
	}
}

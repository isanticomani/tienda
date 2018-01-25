<?php

namespace App;

/**
*
*/
class PayPal
{
	private $_apiContext;
	private $shopping_cart;
	private $_ClientId = 'AWMDAcCv4OIA4ecuAPeune-rfBACaEd-J-wTC5-7EeoPt_wBU8-1_YfGfH7_HWQS35V9DvhjW6Nrk2ke';
	private $_ClientSecret = 'EMaiDMMxPQWmufJwuUkE0T2bBRHtFnuLK4efRBQq3vHWvvBIbtNg_pknCch6iafoZbnXFW0H6lEIvDVf';

	public function __construct($shopping_cart){
		$this->_apiContext = \PaypalPayment::ApiContext($this->_ClientId,$this->_ClientSecret);

		$config = config("paypal_payment");
		$flatConfig = array_dot($config);

		$this->_apiContext->setConfig($flatConfig);
		$this->shopping_cart = $shopping_cart;
	}

	public function generate(){
		$payment = \PaypalPayment::payment()->setIntent("sale")
																				->setPayer($this->payer())
																				->setTransactions([$this->transaction()])
																				->setRedirectUrls($this->redirectURLs());

		try {
			$payment->create($this->_apiContext);
		} catch (\Exception $e) {
			dd($e);
			exit(1);
		}

		return $payment;
	}

	public function payer(){
		// returns payment's info
		return \PaypalPayment::payer()->setPaymentMethod("paypal");
	}

	public function transaction(){
		// returns transaction's info
		return \PaypalPayment::transaction()->setAmount($this->amount())
																			 ->setItemList($this->items())
																			 ->setDescription("Tu compra en ProductosFacilito")
																			 ->setInvoiceNumber(uniqid());
	}

	public function items(){
		$items = [];
		$products = $this->shopping_cart->products()->get();
		foreach ($products as $product) {
			array_push($items, $product->paypalItem());
		}
		return \PaypalPayment::itemList()->setItems($items);
	}

	public function amount(){
		return \PaypalPayment::amount()->setCurrency("USD")->setTotal($this->shopping_cart->totalUSD());
	}

	public function redirectURLs(){
		// returns transaction's info
		$baseURL = url("/");
		return \PaypalPayment::redirectUrls()->setReturnUrl("$baseURL/payments/store")
																				->setCancelUrl("$baseURL/carrito");
	}

	public function execute($paymentId,$payerId){
		$payment = \PaypalPayment::getById($paymentId,$this->_apiContext);

		$execution = \PaypalPayment::PaymentExecution()->setPayerId($payerId);

		return $payment->execute($execution,$this->_apiContext);
	}
}








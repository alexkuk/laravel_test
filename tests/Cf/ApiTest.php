<?php

class ApiTest extends TestCase {

    public function testMain()
    {
        $this->_testAvailability();
        $this->_testHttpAuthProtection();
        $this->_testEmptyPost();
        $this->_testInvalidJson();
        $this->_testTransactionCreateSomeParametersMissing();
        $this->_testTransactionCreateUserIdWrongFormat();
        $this->_testTransactionCreateWrongCurrencyCode();
        $this->_testTransactionCreateWrongAmount();
        $this->_testTransactionCreateWrongRate();
        $this->_testTransactionCreateWrongDate();
        $this->_testTransactionCreateWrongOriginCountry();
        $this->_testTransactionCreateAllCorrect();
    }

    private function _sendTransactionStoreRequest($json)
    {
        return $this->route(
            'POST',
            'tradeapi.v1.transaction.store',
            [],
            [],
            [],
            [],
            ['PHP_AUTH_USER' => 'cf', 'PHP_AUTH_PW' => 'cfap1user'],
            $json
        );
    }

	/**
	 * Test API availability
	 *
	 * @return void
	 */
	private function _testAvailability()
	{
		$response = $this->_sendTransactionStoreRequest(
            '{"userId": "", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );

		$this->assertEquals(200, $response->getStatusCode());

        return $this;
	}

    /**
     * Test empty HTTP auth protection
     *
     * @return void
     */
    private function _testHttpAuthProtection()
    {
        $response = $this->route(
            'POST',
            'tradeapi.v1.transaction.store',
            [],
            [],
            [],
            [],
            [],
            '{"userId": "", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['status' => '0', 'message' => 'Invalid credentials.'], json_decode($response->getContent(), true));

        return $this;
    }

    /**
     * @return void
     */
    private function _testEmptyPost()
    {
        $response = $this->_sendTransactionStoreRequest('');
        $this->assertEquals(['status' => '0', 'message' => 'Empty input'], json_decode($response->getContent(), true));

        return $this;
    }

    /**
     * @return void
     */
    private function _testInvalidJson()
    {
        $response = $this->_sendTransactionStoreRequest('abc');
        $this->assertEquals(['status' => '0', 'message' => 'Invalid JSON'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest('{}');
        $this->assertEquals(['status' => '0', 'message' => 'Invalid JSON'], json_decode($response->getContent(), true));

        return $this;
    }

    /**
     * @return void
     */
    private function _testTransactionCreateAllCorrect()
    {
        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '1', 'message' => ''], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": "1000", "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '1', 'message' => ''], json_decode($response->getContent(), true));

        return $this;
    }

    /**
     * @return void
     */
    private function _testTransactionCreateSomeParametersMissing()
    {
        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "originatingCountry" : "FR"}'
        );

        $this->assertEquals(
            ['status' => '0', 'message' => 'Wrong parameters'],
            json_decode($response->getContent(), true)
        );

        return $this;
    }

    /**
     * @return void
     */
    private function _testTransactionCreateUserIdWrongFormat()
    {
        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(
            ['status' => '0', 'message' => 'Wrong user ID format'],
            json_decode($response->getContent(), true)
        );

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "-1", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(
            ['status' => '0', 'message' => 'Wrong user ID format'],
            json_decode($response->getContent(), true)
        );

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "abc", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(
            ['status' => '0', 'message' => 'Wrong user ID format'],
            json_decode($response->getContent(), true)
        );

        return $this;
    }

    /**
     * @return void
     */
    private function _testTransactionCreateWrongCurrencyCode()
    {
        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "123", "currencyFrom": "", "currencyTo": "", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(
            ['status' => '0', 'message' => 'Wrong currency code'],
            json_decode($response->getContent(), true)
        );

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "123", "currencyFrom": 12, "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(
            ['status' => '0', 'message' => 'Wrong currency code'],
            json_decode($response->getContent(), true)
        );

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "123", "currencyFrom": "QWE", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(
            ['status' => '0', 'message' => 'Wrong currency code'],
            json_decode($response->getContent(), true)
        );

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "123", "currencyFrom": "EUR", "currencyTo": "PQP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(
            ['status' => '0', 'message' => 'Wrong currency code'],
            json_decode($response->getContent(), true)
        );

        return $this;
    }

    private function _testTransactionCreateWrongAmount()
    {
        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": "abc", "amountBuy": "", "rate": "", "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong amount format'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 0, "amountBuy": 1, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong amount format'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": "abc", "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong amount format'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": "1000,01", "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong amount format'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": -747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong amount format'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": "-747.10", "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong amount format'], json_decode($response->getContent(), true));

        return $this;
    }

    private function _testTransactionCreateWrongRate()
    {
        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": "", "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong currency rate'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.747, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong currency rate'], json_decode($response->getContent(), true));
    }

    private function _testTransactionCreateWrongDate()
    {
        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong date/time format'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "29-FEB-15 10:27:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong date/time format'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "28-FEB-15 22:65:44", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong date/time format'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "28-FEB-15 22:20:67", "originatingCountry" : "FR"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong date/time format'], json_decode($response->getContent(), true));
    }

    private function _testTransactionCreateWrongOriginCountry()
    {
        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : ""}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong country ISO2 code'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "ZWE"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong country ISO2 code'], json_decode($response->getContent(), true));

        $response = $this->_sendTransactionStoreRequest(
            '{"userId": "134256", "currencyFrom": "EUR", "currencyTo": "GBP", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "QW"}'
        );
        $this->assertEquals(['status' => '0', 'message' => 'Wrong country ISO2 code'], json_decode($response->getContent(), true));
    }
}

<?php

include "../../database.php";

class Wallet {
	private $sql = "SELECT * FROM wallet WHERE UserID = ";

	private $balance = 0;
	private $id = null;

	/**
	 * Creates new wallet object
	 *
	 * @author Chris Heppell
	 * @param id
	 */
	public function __construct($id) {
		// Get the balance
		$sql = "SELECT * FROM wallet WHERE UserID = '$id'";
		$result = mysqli_query(LINK, $sql);
		$row = mysqli_fetch_array($result);

		$this->id = $id;
		$this->balance = 10;
		//$row['balance'];
  }

	/**
	 * @author Chris Heppell
	 * @param amount
	 * @param description
	 */
	public function creditWallet($amount, $description) {
		$this->balance = $this->balance + $amount;

		// Get the balance
		$sql = "UPDATE wallet SET balance = '$this->balance' WHERE UserID = '$this->id'";
		mysqli_query(LINK, $sql);
		$sql = "INSERT INTO walletHistory (Description, SignBit, Amount) values ($description, 0, $amount)";
		mysqli_query(LINK, $sql);
	}

	/**
	 * @author Chris Heppell
	 * @param amount
	 * @param description
	 */
	public function debitWallet($amount, $description) {
		$this->balance = $this->balance - $amount;

		// Get the balance
		$sql = "UPDATE wallet SET balance = '$this->balance' WHERE UserID = '$this->id'";
		mysqli_query(LINK, $sql);
		$sql = "INSERT INTO walletHistory (Description, SignBit, Amount) values ($description, 1, $amount)";
		mysqli_query(LINK, $sql);
	}

	/**
	 * @author Chris Heppell
	 * @return balance
	 */
	public function getBalance() {
		return $this->balance;
	}
}

$testObj = new Wallet;
echo $testObj->getBalance();

?>

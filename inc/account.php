<?php
/*
	Bestand: account.php
	Gemaakt: 12-12-2014
	Door: Vincent Boon
	Omschrijving:
		Beheert alle accounts, logins, registraties en wijzigingen.

	Laatst gewijzigd: -
*/
include("database.php");
include("passwordencryptor.php");

session_start();

class Accounts {

	private $database;
	private $encryptor;

	function __construct() {
		$this->database = new Database();
		$this->encryptor = new PasswordEncryptor();
	}

	/*
		Registreert een gebruiker.

		@arg voornaam - vooraam van de gebruiker
		@arg achternaam - achternaam van de gebruiker
		@arg gebruikerid - gebruikerid van de gebruiker
		@arg wachtwoord - wachtwoord van de gebruiker
		@arg email - email van de gebruiker

		TODO: check of gebruiker al bestaat

	*/
	function registreer($voornaam, $achternaam, $wachtwoord, $email) {

		$encrypted = $this->encryptor->encrypt($wachtwoord);
		$date = date("Y-m-d");

		$query = "INSERT INTO gebruikers(voornaam, achternaam, wachtwoord, email, registratiedatum) " .
							 "VALUES ('$voornaam', '$achternaam', '$encrypted', '$email', '$date');";

	    $this->database->query($query);

	}

	/*
		Log een gebruiker in, als de inloggegevens correct zijn dan word de session 'loggedin' op true gezet en 'gebruikersnaam' word gelijk gezet aan de gebruikersnaam van de gebruiker.

		@arg email - email van de gebruiker
		@arg wachtwoord - wachtwoord van de gebruiker

		@return bool - successvol
	*/
	function login($email, $wachtwoord) {

		$result = $this->database->query("SELECT * FROM gebruikers WHERE email='$email'");

		if (mysqli_num_rows($result) == 1) {

			$rij = $result->fetch_assoc();

			if ($this->encryptor->check($wachtwoord, $rij["wachtwoord"] )) {

				$_SESSION["loggedin"] = true;
				$_SESSION["id"] = $rij["id"];

				return true;

			}

		}

		return false;
	}

	/*
		Log een gebruiker uit.

		@arg id - id van de gebruiker

		@return bool - successvol
	*/
	function loguit($id) {

		if ( !isset($_SESSION["loggedin"])) {

			if ($_SESSION["loggedin"] == false) {
				return false;
			}

		}


		$_SESSION["loggedin"] = false;
		unset( $_SESSION["id"] );

		return true;
	}

}

?>

<?php
    class Account {

        private $errorArray;
        private $con;

        public function __construct($con) {
            $this->errorArray = array();
            $this->con = $con;
        }

        public function login($un, $pw) {
            //hash the password
            $pw = md5($pw);

            $query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$un' AND password='$pw'");

            //if returns one result, good
            //if not add error to error Array
            if(mysqli_num_rows($query) == 1) {
                return true;
            } 
            else {
                array_push($this->errorArray, Constants::$loginFailed);
                return false;
            }
        }

        public function register($un, $fn, $ln, $e, $pw1, $pw2) {
            $this->validateUsername($un);
            $this->validateFirstName($fn);
            $this->validateLastName($ln);
            $this->validateEmail($e);
            $this->validatePasswords($pw1, $pw2);

            if(empty($this->errorArray)) {
                //Insert into DB
                return $this->insertUserDetails($un, $fn, $ln, $e, $pw1);
            }
            else {
                return false;
            }
        }

        public function getError($error) {
            //if nothing in errorArray, print nothing
            if(!in_array($error, $this->errorArray)) {
                $error = "";
            }
            return "<span class='errorMessage'>$error</span>";
        }

        private function insertUserDetails($un, $fn, $ln, $em, $pw) {
            $encryptedPw = md5($pw);
            $profilePic = "assets/images/profile-pics/head_emerald.png";
            $date = date("Y-m-d");

            $result = mysqli_query($this->con, "INSERT INTO users VALUES ('', '$un' ,'$fn', '$ln', '$em', '$encryptedPw', '$date', '$profilePic')");

            return $result;
        }

        private function validateUsername($un) {
            if(strlen($un) > 25 || strlen($un) < 5) {
                array_push($this->errorArray, Constants::$usernameCharacters);
                return;
            }

            //check if username is in the database
            $checkUsernameQuery = mysqli_query($this->con, "SELECT username FROM users WHERE username='$un'");
            if(mysqli_num_rows($checkUsernameQuery) != 0) {
                array_push($this->errorArray, Constants::$usernameTaken);
                return;
            }
        }

        private function validateFirstName($fn) {
            if(strlen($fn) > 25 || strlen($fn) < 2) {
                array_push($this->errorArray, Constants::$firstNameCharacters);
                return;
            } 
        }

        private function validateLastName($ln) {
            if(strlen($ln) > 25 || strlen($ln) < 2) {
                array_push($this->errorArray, Constants::$lastNameCharacters);
                return;
            } 
        }

        private function validateEmail($e) {
            if(!filter_var($e, FILTER_VALIDATE_EMAIL)) {
                array_push($this->errorArray, Constants::$emailInvalid);
                return;
            }
            //check if email is in the database
            $checkEmailQuery = mysqli_query($this->con, "SELECT email FROM users WHERE email='$e'");
            if(mysqli_num_rows($checkEmailQuery) != 0) {
                array_push($this->errorArray, Constants::$emailTaken);
                return;
            }
        }

        private function validatePasswords($pw1, $pw2) {
            if($pw1 != $pw2) {
                array_push($this->errorArray, Constants::$passwordsDoNotMatch);
                return;
            }

            //make sure password is alphanumeric
            if(preg_match('/[^A-Za-z0-9]/', $pw1)) {
                array_push($this->errorArray, Constants::$passwordsNotAlphanumeric);
                return;
            }

            if(strlen($pw1) > 30 || strlen($pw1) < 5) {
                array_push($this->errorArray, Constants::$passwordCharacters);
                return;
            }
        }

    }
?>
<?php
/**
 * EEmailValidator class file.
 *
 * @author Rodolfo González González
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008 Rodolfo González González.
 * @license The 3-Clause BSD License
 *
 * Copyright © 2008, Rodolfo González González.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its
 * contributors may be used to endorse or promote products derived from
 * this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Include the email_validation class.
 */
require_once(Yii::getPathOfAlias('application.extensions.emailvalidator').DIRECTORY_SEPARATOR.'emailvalidation'.DIRECTORY_SEPARATOR.'email_validation.php');

/**
 * EEmailValidator validates that the attribute value is a valid e-mail address.
 *
 *
 * @author Rodolfo González González
 * @package application.extensions.emailvalidator
 * @since 1.0
 * @uses email_validation_class.php
 */
class EEmailValidator extends CValidator
{
   /**
    * Whether to allow empty addresses.
    *
    * @var boolean
    */
   public $allowEmpty = false;

   /**
    * How many seconds to wait before each attempt to connect to the
    * destination e-mail server
    *
    * @var integer
    */
   public $timeOut = 5;
   /**
    * How many seconds to wait for data exchanged with the server.
    * Set to a non zero value if the data timeout will be different
    * than the connection timeout.
    *
    * @var integer
    */
   public $dataTimeOut = 0;
   /**
    * User part of the e-mail address of the sending user
    *
    * @var string
    */
   public $localUser = 'postmaster';
   /**
    * Domain part of the e-mail address of the sending user
    *
    * @var string
    */
   public $localHost = 'localhost.localdomain';
   /**
    * When it is not possible to resolve the e-mail address of
    * destination server (MX record) eventually because the domain is
    * invalid, this class tries to resolve the domain address (A
    * record). If it fails, usually the resolver library assumes that
    * could be because the specified domain is just the subdomain
    * part. So, it appends the local default domain and tries to
    * resolve the resulting domain. It may happen that the local DNS
    * has an * for the A record, so any sub-domain is resolved to some
    * local IP address. This  prevents the class from figuring if the
    * specified e-mail address domain is valid. To avoid this problem,
    * just specify in this variable the local address that the
    * resolver library would return with gethostbyname() function for
    * invalid global domains that would be confused with valid local
    * domains. Here it can be either the domain name or its IP address.
    *
    * @var string
    */
   public $excludeAddress = '';
   /**
    * If possible specify in this array the address of at least on local
    * DNS that may be queried from your network.
    *
    * @var array
    */
   public $nameServers = array();

   /**
    * If it is not possible to verify if the e-mail address is valid,
    * and this flag is set to true, then the validation will fail.
    *
    * @var boolean
    */
   public $strictValidation = false;

	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel the object being validated
	 * @param string the attribute being validated
	 */
	protected function validateAttribute($object,$attribute)
	{
	   $valid = true;

		$validator = new email_validation($this->nameServers);
      $validator->timeout = $this->timeOut;
      $validator->data_timeout = $this->dataTimeOut;
      $validator->localuser = $this->localUser;
      $validator->localhost = $this->localHost;
      $validator->debug = 0;
      $validator->html_debug = 0;
      $validator->exclude_address = $this->excludeAddress;

      if (is_object($object) && isset($object->$attribute)) $email = $object->$attribute;

      if (isset($email) && strcmp($email, '')) {
         $result = $validator->ValidateEmailBox($email);
         if ($result<0 && $this->strictValidation) {
            $valid = false;
         }
         elseif (!$result && $this->strictValidation) {
            $valid = false;
         }
      }
		else {
		   $valid = ($this->allowEmpty) ? true : false;
		}

		if (!$valid) {
		   $message = $this->message !== null ? $this->message : Yii::t('yii', 'The e-mail address is invalid.');
			$this->addError($object, $attribute, $message);
		}
	}
}

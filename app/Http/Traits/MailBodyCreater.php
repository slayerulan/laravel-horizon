<?php

namespace App\Http\Traits;

use App\MailTemplate;

/**
 * This will help to render dynamic mail body
 *
 *  @author Anirban Saha
 */
trait MailBodyCreater
{
	/**
	 *  contain mail subject
	 *
	 *  @var  string
	 */
	public $subject;

	/**
	 *  contain mail body
	 *
	 *  @var  string
	 */
	public $content;

	/**
	 * set mail body with given data
	 * 
	 * @param  integer $id   mail_template table id
	 * @param  array  $data dynamic value
	 * @return bool       true/false
	 */
	public function setMailBody($id,array $data)
	{
		$mail_body = MailTemplate::select('subject','content')->find($id);
		if(isset($mail_body->subject)){
			$this->subject = $mail_body->subject;
			$body = $mail_body->content;
			foreach ($data as $key => $value) {
				$body = str_ireplace('{{ '.$key.' }}', $value, $body);
			}
			$this->content = $body;
			return true;
		}
		return false;
	}
}

 ?>

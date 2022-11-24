<?php

namespace Webi\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RegisterMail extends Mailable
{
	use Queueable, SerializesModels;

	public $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	public function build()
	{
		return $this->subject(trans(config('webi.email.subject.register')))
			->view('webi::emails.register');
	}
}

<?php

namespace Webi\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class DefaultMail extends Mailable
{
	use Queueable, SerializesModels;

	public $user = null;
	public $subject = '';
	public $content = '';

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $content = 'Email message content', $subject = 'Welcome email')
	{
		$this->user = $user;
		$this->subject = $subject;
		$this->content = $content;
	}

	/**
	 * Get the message envelope.
	 *
	 * @return \Illuminate\Mail\Mailables\Envelope
	 */
	public function envelope()
	{
		return new Envelope(
			// from: new Address('hello@email.sample', 'Demo mail'),
			subject: $this->subject,
		);
	}

	/**
	 * Get the message content definition.
	 *
	 * @return \Illuminate\Mail\Mailables\Content
	 */
	public function content()
	{
		return new Content(
			view: 'emails.default',
		);
	}

	/**
	 * Get the attachments for the message.
	 *
	 * @return array
	 */
	public function attachments()
	{
		return [];
	}
}

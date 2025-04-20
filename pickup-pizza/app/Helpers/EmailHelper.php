<?php

namespace App\Helpers;

class EmailHelper
{
    /**
     * Get the admin email addresses for notifications
     * 
     * @return array
     */
    public static function getAdminEmails()
    {
        return config('mail.admin_emails', [
            'visheshvaibhav10@gmail.com',
            'jivgan2210@gmail.com'
        ]);
    }
    
    /**
     * Send an email to all admin recipients
     * 
     * @param \Illuminate\Mail\Mailable $mailable
     * @return void
     */
    public static function sendToAdmins($mailable)
    {
        \Mail::to(self::getAdminEmails())->send($mailable);
    }
} 
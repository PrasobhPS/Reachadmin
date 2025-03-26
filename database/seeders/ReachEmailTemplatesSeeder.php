<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReachEmailTemplate;

class ReachEmailTemplatesSeeder extends Seeder
{
    public function run()
    {
        // Define email templates
        $templates = [
            [
                'template_type' => 'user_registration',
                'template_subject' => 'Welcome To Reach',
                'template_tags' => '[first_name],[user_name],[user_password]',
                'template_message' => 'Welcome [first_name],<br><br>Thank you for registering your details with Reach.com, we are very excited to have you as part of our community.<br><br>You have successfully registered. Your login details are below:<br><br><b>Username:</b> &nbsp;[user_name]<br><b>Password:</b> &nbsp;[user_password]<br><br>If you would need any help or support, please don’t hesitate to give us a shout at membership@reachclub.com<br><br>Best regards,<br>Team Reach',
                'template_to_status' => 'U',
                'template_to_address' => null,
                'template_cc_address' => null,
                'template_bcc_address' => 'info@reach.boats',
            ],
            [
                'template_type' => 'booking_confirmation',
                'template_subject' => 'Booking Confirmation',
                'template_tags' => '[first_name],[result]',
                'template_message' => 'Dear [first_name],<br><br>We are delighted to confirm your booking with us. Below are the details: [result] Please review the details above to ensure everything is correct. If there are any discrepancies or if you have any questions, feel free to contact us.<br><br>Best regards,<br>The Reach Team',
                'template_to_status' => 'U',
                'template_to_address' => null,
                'template_cc_address' => null,
                'template_bcc_address' => 'info@reach.boats',
            ],
            [
                'template_type' => 'booking_cancelled',
                'template_subject' => 'Booking Cancellation - Booking ID ',
                'template_tags' => '[first_name],[booking_id]',
                'template_message' => 'Dear [first_name],<br><br>We regret to inform you that your booking with ID [booking_id] has been cancelled. We appreciate your choosing our service.<br><br>If you have any questions, please do not hesitate to contact us.<br><br>Best regards,<br>The Reach Team',
                'template_to_status' => 'U',
                'template_to_address' => null,
                'template_cc_address' => null,
                'template_bcc_address' => 'info@reach.boats',
            ],
            [
                'template_type' => 'booking_updates',
                'template_subject' => 'Updated Booking Information for Booking ID ',
                'template_tags' => '[first_name],[result]',
                'template_message' => 'Dear [first_name],<br><br>We hope this message finds you well. We wanted to inform you that there has been a change to your booking details: [result] If you have any questions or need further assistance, please feel free to contact us. We apologize for any inconvenience this change may cause and appreciate your understanding.<br><br>Best regards,<br>The Reach Team',
                'template_to_status' => 'U',
                'template_to_address' => null,
                'template_cc_address' => null,
                'template_bcc_address' => 'info@reach.boats',
            ],
            [
                'template_type' => 'booking_call',
                'template_subject' => 'Booking Submitted - Booking ID ',
                'template_tags' => '[first_name],[result]',
                'template_message' => 'Dear [first_name],<br><br>Thank you for submitting your booking request with us. We have received the following details: [result] Our team will review your booking request shortly. Once confirmed, you will receive another email with the booking details.<br><br>Thank you for choosing our service. We look forward to serving you soon.<br><br>Best regards,<br>The Reach Team',
                'template_to_status' => 'U',
                'template_to_address' => null,
                'template_cc_address' => null,
                'template_bcc_address' => 'info@reach.boats',
            ],
            // Add more templates as needed
        ];

        // Insert templates if they do not already exist
        foreach ($templates as $template) {
            $existingTemplate = ReachEmailTemplate::where('template_type', $template['template_type'])->first();

            if (!$existingTemplate) {
                ReachEmailTemplate::create($template);
            }
        }
    }
}
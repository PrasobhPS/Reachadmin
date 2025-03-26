<?php

namespace App\Libraries;

use MailchimpMarketing\ApiClient;
use Illuminate\Support\Facades\View;

use MailchimpTransactional\ApiClient as TransactionalClient;
use Exception;


class MailchimpService
{
    protected $mailchimp;
    protected $mailchimpTransactional;
    protected $defaultEmail;
    protected $defaultName;
    protected $templateName;

    public function __construct()
    {
        $this->mailchimp = new ApiClient();
        $this->mailchimp->setConfig([
            'apiKey' => env('MAILCHIMP_API_KEY'),
            'server' => substr(env('MAILCHIMP_API_KEY'), strpos(env('MAILCHIMP_API_KEY'), '-') + 1)
        ]);

        $this->mailchimpTransactional = new TransactionalClient();
        $this->mailchimpTransactional->setApiKey(env('MAILCHIMP_TRANSACTIONAL_API_KEY'));

        $this->defaultEmail = 'info@reach.boats';
        $this->defaultName = 'Reach Boats';
        $this->templateName = 'email-template';
    }

    public function sendTemplateEmail($to, $templateContent, $subject, $fromEmail = null, $cc = [], $bcc = [])
    {
        try {
            $fromEmail = $fromEmail ?: $this->defaultEmail;
            $fromName = $this->defaultName;

            $recipients = [['email' => $to, 'type' => 'to']];

            // Add CC recipients
            foreach ($cc as $ccEmail) {
                $recipients[] = ['email' => $ccEmail, 'type' => 'cc'];
            }

            // Add BCC recipients
            foreach ($bcc as $bccEmail) {
                $recipients[] = ['email' => $bccEmail, 'type' => 'bcc'];
            }

            $response = $this->mailchimpTransactional->messages->sendTemplate([
                'template_name' => $this->templateName,
                'template_content' => [],
                'message' => [
                    'html' => 'content',
                    'subject' => $subject,
                    'from_email' => $fromEmail,
                    'from_name' => $fromName,
                    'to' => $recipients,
                    'merge' => true,
                    'merge_language' => 'mailchimp',
                    'global_merge_vars' => [
                        ['name' => 'MESSAGE', 'content' => $templateContent]
                    ],
                ],
            ]);
            return $response;
        } catch (Exception $e) {
            throw new Exception('Error from MailChimp: ' . $e->getMessage());
        }
    }

    public function addTemplate($templateTitle, $templateMessage, $folderName)
    {
        try {
            $folderId = $this->getFolderId($folderName);

            $renderedMessage = View::make('emails.email_body', ['body' => $templateMessage])->render();

            $response = $this->mailchimp->templates->create([
                "name" => $templateTitle,
                "html" => $renderedMessage,
                "folder_id" => $folderId,
            ]);

            return $response;

        } catch (\MailchimpMarketing\ApiException $e) {
            throw new \Exception('Error from MailChimp: ' . $e->getMessage());
        }
    }

    public function updateTemplate($mailchimpId, $templateTitle, $templateMessage)
    {
        try {

            $renderedMessage = View::make('emails.email_body', ['body' => $templateMessage])->render();

            $response = $this->mailchimp->templates->updateTemplate($mailchimpId, [
                "name" => $templateTitle,
                "html" => $renderedMessage,
            ]);

            return $response;

        } catch (\MailchimpMarketing\ApiException $e) {
            throw new \Exception('Error from MailChimp: ' . $e->getMessage());
        }
    }

    public function getFolderId($folderName)
    {
        try {
            $response = $this->mailchimp->templateFolders->list();
            foreach ($response['folders'] as $folder) {
                if ($folder['name'] === $folderName) {
                    return $folder['id'];
                }
            }
            throw new \Exception('Folder not found');
        } catch (\MailchimpMarketing\ApiException $e) {
            throw new \Exception('Error from MailChimp: ' . $e->getMessage());
        }
    }

}
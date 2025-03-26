<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MailchimpMarketing\ApiClient;

class UserAddToMailChimp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:mailchimp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add already created users to Mailchimp.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Fetch all users from reach_member table
        $members = DB::table('reach_members')->get();

        if ($members->isEmpty()) {
            $this->info('No users found in reach_member table.');
            return;
        }

        $this->info('Processing ' . $members->count() . ' users...');

        foreach ($members as $member) {
            if ($member->members_type === 'F') {
                $result = $this->subscribeToMailchimp($member, ['Reach-Member', 'Free-Member']);

            } else if ($member->members_type === 'M') {
                $result = $this->subscribeToMailchimp($member, ['Reach-Member', 'Full-Member']);
            } else {
                $result = [];
            }

            if ($result['success']) {
                $this->info("User {$member->members_email} added/updated successfully.");
            } else {
                $this->error("Failed to add user {$member->members_email}: " . $result['message']);
            }
        }

        $this->info('Mailchimp sync completed.');
    }

    /**
     * Subscribe a user to Mailchimp.
     *
     * @param object $member
     * @param array $tags
     * @return array
     */
    private function subscribeToMailchimp($member, $tags = [])
    {
        try {
            $mailchimp = new ApiClient();
            $mailchimp->setConfig([
                'apiKey' => env('MAILCHIMP_API_KEY'),
                'server' => substr(env('MAILCHIMP_API_KEY'), strpos(env('MAILCHIMP_API_KEY'), '-') + 1)
            ]);

            $listId = env('MAILCHIMP_LIST_ID');
            $subscriberHash = md5(strtolower($member->members_email));

            try {
                // Try to get existing member
                $user = $mailchimp->lists->getListMember($listId, $subscriberHash);

                // If user exists, update their tags
                if ($user->status === 'subscribed') {
                    $mailchimp->lists->updateListMemberTags($listId, $subscriberHash, [
                        'tags' => array_map(fn($tag) => ['name' => $tag, 'status' => 'active'], $tags)
                    ]);

                    return ['success' => true, 'message' => 'Member tags updated successfully'];
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // If user does not exist, create a new one
                if ($e->getResponse()->getStatusCode() === 404) {
                    $mailchimp->lists->addListMember($listId, [
                        'email_address' => $member->members_email,
                        'status' => 'subscribed',
                        'merge_fields' => [
                            'FNAME' => $member->members_fname,
                            'LNAME' => $member->members_lname,
                            'ADDRESS' => $member->members_address ?? '',
                            'PHONE' => isset($member->members_phone)
                                ? "+" . $member->members_phone_code . $member->members_phone
                                : '',
                        ],
                        'tags' => $tags
                    ]);

                    return ['success' => true, 'message' => 'New member subscribed successfully'];
                }

                // Re-throw other errors
                throw $e;
            }
        } catch (\MailchimpMarketing\ApiException $e) {
            \Log::error('Mailchimp API Error: ' . $e->getMessage(), [
                'email' => $member->members_email,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return ['success' => false, 'message' => 'Error subscribing to mailing list'];
        } catch (\Exception $e) {
            \Log::error('Unexpected error in Mailchimp subscription: ' . $e->getMessage(), [
                'email' => $member->members_email,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return ['success' => false, 'message' => 'Unexpected error in mailing list subscription' . $e->getMessage()];
        }
    }
}

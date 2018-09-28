<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\SmsMessage;
use AbuSalam\SmsGateway;

class SendSmsMessages extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'sms:send';

   /**
    * The console command description.
    *
    * @var string
    */
    protected $description = 'Send all Unsent SMS Messages';

   /**
    * Create a new command instance.
    *
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
    }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
    public function handle()
    {
        SmsMessage::all()->each(function ($smsMessage) {
            $smsGateway = new SmsGateway;
            $smsMessage->response = $smsGateway
                ->toRecipient($smsMessage->mobile_no)
                ->withSms($smsMessage->message)
                ->asUnicodeSms()
                ->sendSms();
            if ($smsMessage->save()) {
                $smsMessage->delete();
            }
        });
    }
}

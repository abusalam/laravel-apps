<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use AbuSalam\SmsGateway;
use Illuminate\Support\Carbon;
use App\SmsMessage;

class SmsCollector extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect SMS Messages to be sent from Various Sources.';

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
        $lastSmsMessage = SmsMessage::withTrashed()->latest();
        $lastSmsTime = time();
        if (! is_null($lastSmsMessage)) {
            $lastSmsTime = $lastSmsMessage->first()->created_at;
        }
        $smsMessages = collect(DB::table('mame_sms')->select('mobile_no', 'message', 'ason_date')->where('ason_date', '>', $lastSmsTime)->get());
        //dump($smsMessages);
        if ($smsMessages->count()) {
            $smsMessages->each(function ($smsMessage) {
                $newSms = new SmsMessage;
                $newSms->mobile_no = $smsMessage->mobile_no;
                $newSms->message = $smsMessage->message;
                $newSms->save();
                //$this->line('Saving SMS:' . $smsMessage->ason_date);
            });
            Log::info('Collected: ' . $smsMessages->count() . ' Messages');
            $sendSms = new SmsGateway;
            $sendSms->toRecipient(env('ADMIN_MOBILE'))
            ->withSms('Collected: ' . $smsMessages->count() . ' Messages')
            ->sendSms();
            $this->info('Collected: ' . $smsMessages->count() . ' Messages');
        }
    }
}

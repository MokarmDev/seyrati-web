<?php

namespace Botble\Ecommerce\Commands;

use Botble\Ecommerce\Services\WhatsAppService;
use Illuminate\Console\Command;

class TestWhatsAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'whatsapp:test {phone?}';

    /**
     * The console command description.
     */
    protected $description = 'Test WhatsApp API connection and send a test message';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsappService): int
    {
        $this->info('🔄 Testing WhatsApp API Connection...');
        $this->newLine();

        // اختبار الاتصال بالـ API
        $this->info('1️⃣ Testing API Connection...');
        $connectionTest = $whatsappService->testConnection();
        
        if ($connectionTest['success']) {
            $this->info('✅ API Connection: SUCCESS');
            $this->comment('Status Code: ' . $connectionTest['status']);
            if (!empty($connectionTest['response'])) {
                $this->comment('Response: ' . json_encode($connectionTest['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        } else {
            $this->error('❌ API Connection: FAILED');
            $this->error('Error: ' . $connectionTest['message']);
            return Command::FAILURE;
        }

        $this->newLine();

        // إرسال رسالة تجريبية
        $phone = $this->argument('phone') ?: $this->ask('Enter phone number to test (with country code, e.g., +967xxxxxxxxx)');
        
        if (!$phone) {
            $this->error('❌ Phone number is required');
            return Command::FAILURE;
        }

        $this->info('2️⃣ Sending test OTP message...');
        $testOtp = rand(100000, 999999);
        
        $sendResult = $whatsappService->sendOtp($phone, $testOtp);
        
        if ($sendResult) {
            $this->info('✅ Message sent successfully!');
            $this->comment('Test OTP Code: ' . $testOtp);
            $this->comment('Sent to: ' . $phone);
        } else {
            $this->error('❌ Failed to send message');
            $this->error('Check the logs for more details: storage/logs/laravel.log');
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('3️⃣ Configuration Details:');
        $this->table(
            ['Config', 'Value'],
            [
                ['API URL', config('services.whatsapp.api_url')],
                ['Sender Phone', config('services.whatsapp.sender_phone')],
                ['Token', substr(config('services.whatsapp.token'), 0, 4) . '****'],
            ]
        );

        $this->newLine();
        $this->info('✅ All tests completed successfully!');
        
        return Command::SUCCESS;
    }
}

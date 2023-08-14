<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Aws\MarketplaceMetering\MarketplaceMeteringClient;

class updateAWSEveryHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:updateaws';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update AWS every hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $meteringClient = new MarketplaceMeteringClient([
            'version' => '2016-01-14',
            'region' => 'us-east-1',
            'credentials' => [
                'key' => 'YOUR_AWS_ACCESS_KEY_ID',
                'secret' => 'YOUR_AWS_SECRET_ACCESS_KEY',
            ],
        ]);
        // Product ID
        // prod-tr3kxgsjrubra
        // Product code
        // 7ltpmjexhtohrhno3ewb8p30k
        $productCode = 'YOUR_PRODUCT_CODE';
        $usageDimension = 'YOUR_USAGE_DIMENSION';
        $timestamp = time();
        $quantity = 1;

        $result = $meteringClient->meterUsage([
            'ProductCode' => $productCode,
            'Timestamp' => $timestamp,
            'UsageDimension' => $usageDimension,
            'UsageQuantity' => $quantity,
        ]);

        Log::info("AWS AMI Marketplace Metering API Hourly: " . $result['MeteringRecordId']);
    }
}

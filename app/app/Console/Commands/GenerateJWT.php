<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateJWT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate jwt private and public keys. keys are stored in storage/app dir';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $config = array(
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $privateKey = openssl_pkey_new($config);
        if ($privateKey === false) {
            $this->error('could not generate private key');
            return;
        }
        openssl_pkey_export($privateKey, $privateKeyPem);
        file_put_contents('storage/app/private_key.pem', $privateKeyPem);

        $publicKeyDetails = openssl_pkey_get_details($privateKey);
        if ($publicKeyDetails === false) {
            $this->error('could not generate public key');
            return;
        }
        $publicKeyPem = $publicKeyDetails['key'];
        file_put_contents('storage/app/public_key.pem', $publicKeyPem);

        $this->info('JWT private:public key pair generated successfully.');
    }
}

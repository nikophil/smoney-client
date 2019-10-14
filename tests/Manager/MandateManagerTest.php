<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Manager;

use AssoConnect\SMoney\Exception\MissingAppUserIdException;
use AssoConnect\SMoney\Exception\MissingIdException;
use AssoConnect\SMoney\Exception\UserAgeException;
use AssoConnect\SMoney\Exception\UserAlreadyExistsException;
use AssoConnect\SMoney\Exception\UserCountryException;
use AssoConnect\SMoney\Manager\BankAccountManager;
use AssoConnect\SMoney\Manager\MandateManager;
use AssoConnect\SMoney\Manager\UserManager;
use AssoConnect\SMoney\Object\Address;
use AssoConnect\SMoney\Object\BankAccount;
use AssoConnect\SMoney\Object\MandateRequest;
use AssoConnect\SMoney\Object\SubAccount;
use AssoConnect\SMoney\Object\User;
use AssoConnect\SMoney\Object\UserProfile;
use AssoConnect\SMoney\Test\SMoneyTestCase;
use GuzzleHttp\Psr7\UploadedFile;

class MandateManagerTest extends SMoneyTestCase
{

    public function testCreateMandateRequestSuccess()
    {
        $userPro = $this->helperCreateUser(true);
        $params = [
            'displayName' => 'bank account',
            'bic' => 'SOGEFRPPXXX',
            'iban' => 'FR7630003031100005055686128',
        ];
        $bankAccountTest = new BankAccount($params);
        $client = $this->getClient();
        $bankAccountManager = new BankAccountManager($client);
        $bankAccount = $bankAccountManager->createBankAccount($userPro, $bankAccountTest);

        $mandateManager = new MandateManager($client);
        $mandateRequest = $mandateManager->createMandateRequest(
            $userPro->appUserId,
            $bankAccount->id,
            'http://test.com/returnurl/',
            'http://services.assoconnect-dev.com/services/smoney/callback'
        );
        $mandateRequest = (array) $mandateRequest;
        // Href not returned by API on getMandate()
        unset($mandateRequest['href']) ;
        $getMandate = $mandateManager->getMandate($userPro->appUserId, $mandateRequest['id'])[0];
        $this->assertSame($mandateRequest, $getMandate);
    }

    public function testSendPaperMandateSuccess()
    {
        $userPro = $this->helperCreateUser(true);
        $params = [
            'displayName' => 'bank account',
            'bic' => 'SOGEFRPPXXX',
            'iban' => 'FR7630003031100005055686128',
        ];
        $bankAccountTest = new BankAccount($params);
        $client = $this->getClient();
        $bankAccountManager = new BankAccountManager($client);
        $bankAccount = $bankAccountManager->createBankAccount($userPro, $bankAccountTest);

        $mandateManager = new MandateManager($client);

        $mandateRequest = $mandateManager->createMandateRequest(
            $userPro->appUserId,
            $bankAccount->id,
            'http://test.com/returnurl/',
            'http://services.assoconnect-dev.com/services/smoney/callback'
        );

        $file1 = __DIR__ . '/../data/sample.pdf';
        $stream1 = fopen($file1, 'r+');
        $filesize1 = filesize($file1);
        $file1 = new UploadedFile($stream1, $filesize1, UPLOAD_ERR_OK, 'document.png', 'image/png');


        $mandate = $mandateManager->getMandate(
            $userPro->appUserId,
            $mandateRequest->id
        );

        $paperMandateRequest = $mandateManager->sendPaperMandate(
            $userPro->appUserId,
            $mandate[0]['id'],
            $file1
        );

        $this->assertTrue($paperMandateRequest);
    }
}

<?php

namespace Sds\IdentityModule\Test\Controller;

use Sds\Common\Crypt\Hash;
use Sds\IdentityModule\DataModel\Identity;
use Sds\ModuleUnitTester\AbstractControllerTest;
use Zend\Http\Request;
use Zend\Http\Header\GenericHeader;

class ForgotCredentialTokenControllerTest extends AbstractControllerTest{

    protected $serviceMapArray;

    protected $documentManager;

    protected static $staticDcumentManager;

    protected static $dbIdentityCreated = false;

    public static function tearDownAfterClass(){
        //Cleanup db after all tests have run
        $collections = static::$staticDcumentManager->getConnection()->selectDatabase('identityModuleTest')->listCollections();
        foreach ($collections as $collection) {
            $collection->remove(array(), array('safe' => true));
        }
    }

    public function setUp(){

        $this->controllerName = 'Sds\IdentityModule\Controller\ForgotCredentialTokenController';

        parent::setUp();

        $this->documentManager = $this->serviceManager->get('doctrine.odm.documentmanager.default');
        static::$staticDcumentManager = $this->documentManager;

        if ( ! static::$dbIdentityCreated){
            //create the mock identity
            $documentManager = $this->documentManager;
            $identity = new Identity;
            $identity->setIdentityName('toby');
            $identity->setFirstName('Toby');
            $identity->setLastName('Awesome');
            $identity->setCredential('password1');
            $identity->setEmail('toby@awesome.com');
            $documentManager->persist($identity);
            $documentManager->flush();
            $documentManager->clear();

            static::$dbIdentityCreated = true;
        }
    }

    public function testCreateTokenWithEmail(){

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getHeaders()->addHeader(GenericHeader::fromString('Content-type: application/json'));
        $this->request->setContent('{
            "email": "toby@awesome.com"
        }');
        $result = $this->getController()->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();

        $this->assertCount(0, $returnArray);

        //check the email
        $this->assertTrue(file_exists(__DIR__ . '/../../../../email/test_mail.tmp'));
    }

    public function testChangeCredentialWithToken(){

        //complete the password recovery
        $text = file_get_contents(__DIR__ . '/../../../../email/test_mail.tmp');
        preg_match('/forgotCredentialToken\/[a-zA-Z0-9]+/', $text, $match);
        $code = str_replace('forgotCredentialToken/', '', $match[0]);

        $this->routeMatch->setParam('id', $code);
        $this->request->setMethod(Request::METHOD_PUT);
        $this->request->getHeaders()->addHeader(GenericHeader::fromString('Content-type: application/json'));
        $this->request->setContent('{
            "credential": "newPassword1"
        }');

        $result = $this->getController()->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();

        $this->assertCount(0, $returnArray);

        $identity = $this->documentManager
            ->getRepository($this->controller->getOptions()->getIdentityClass())
            ->findOneBy(['identityName' => 'toby']);

        $this->assertTrue(Hash::hashCredential($identity, 'newPassword1') == $identity->getCredential());
    }
}


<?php

namespace G4\CleanCore\UseCase\Gateway;

use G4\CleanCore\UseCase\UseCaseAbstract;
use G4\Constants\Format;
use G4\Gateway\GatewayInterface;
use G4\CleanCore\UseCase\Gateway\UseCaseGatewayInterface;

abstract class UseCaseGatewayAbstract extends UseCaseAbstract implements UseCaseGatewayInterface
{
    /**
     * @var GatewayInterface
     */
    private $gateway;


    /**
     * @return GatewayInterface
     */
    public function getGateway()
    {
        if (!$this->gateway instanceof GatewayInterface) {
            $this->gateway = $this->getGatewayInstance();
        }
        return $this->gateway;
    }

    public function getGatewayResource()
    {
        return $this->getGateway()->getResource();
    }

    public function getParams()
    {
        return $this->getRequest()->getParams();
    }

    public function run()
    {
        $this->getGateway()
            ->setParams($this->getParams())
            ->execute();
        $this->addToResponse();
    }

    private function addToResponse()
    {
        !$this->getGateway()->isOk()
            ? $this->throwError()
            : $this->getResponse()->addPartToResponseObject(
                'data', $this->getGatewayResource()
            )->setHttpResponseCode($this->getResponseCodeFromGatway());
    }

    private function throwError()
    {
        $this->getResponse()->addPartToResponseObject(Format::FORMAT, Format::JSON);
        throw new \Exception(null, $this->getGateway()->getResponseCode());
    }

    private function getResponseCodeFromGatway()
    {
        $responseBody = $this->getGateway()->getResponseBody();
        return isset($responseBody['code'])
            ? $responseBody['code']
            : $this->getGateway()->getResponseCode();
    }

}
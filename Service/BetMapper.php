<?php

namespace Qwer\LottoDocumentsBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Qwer\LottoDocumentsBundle\Entity\Bet;
use Qwer\LottoDocumentsBundle\Entity\Request\Body;
use Itc\DocumentsBundle\Listener\ContainerAware;
use Qwer\LottoDocumentsBundle\Entity\Request\RawBet;
use Qwer\LottoBundle\Entity\BetType;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;


class BetMapper extends ContainerAware
{

    /**
     *
     * @var DrawFinder 
     */
    private $drawFinder;
   

    /**
     *
     * @var type 
     */
    private $documentType = null;
    
    /**
     * 
     * @param \Qwer\LottoDocumentsBundle\Entity\Request\Body $body
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getBets(Body $body)
    {
        $token = $body->getToken();
        $currency = $token->getCurrency();
        $externalId = $token->getExternalId();
        $client = $token->getClient();
        $withBonus = $body->getWithBonus();
        $drawNum = $body->getDrawNum();

        $betsPrototypes = new ArrayCollection();

        $documentType = $this->getDocumentType();

        foreach ($body->getRawBets() as $rawBet) {
            $bet = $this->getBet($rawBet);
            $bet->setCurrency($currency);
            $bet->setExternalUserId($externalId);
            $bet->setLotoClient($client);
            $bet->setWithBonus($withBonus);
            $bet->setDocumentType($documentType);
            $betsPrototypes->add($bet);
        }

        $lottoType = $body->getLottoType(); 
        
        

        foreach ($betsPrototypes as $betPrototype) {
            $betLines = $betPrototype->getDocumentLines();
            foreach ($betLines as $line) {
                $balls = $line->getBalls();
                $odd = $lottoType->getRate($balls, $withBonus);
                $line->setOdd($odd);
            }
        }

        $draws = $this->drawFinder->getDraws($lottoType, $drawNum);

        $bets = new ArrayCollection();

        foreach ($draws as $draw) {
            foreach ($betsPrototypes as $betPrototype) {
                $newbet = clone($betPrototype);
                $newbet->resetLines();
                $newbet->setLottoDraw($draw);
                $bets->add($newbet);
            }
        }

        return $bets;
    }

    /**
     * 
     * @param \Qwer\LottoDocumentsBundle\Entity\Request\RawBet $rawBet
     * @return \Qwer\LottoDocumentsBundle\Entity\Bet
     */
    private function getBet(RawBet $rawBet)
    {
        $balls = $rawBet->getBalls();
        $summa = $rawBet->getSumma();

        $bet = new Bet();
        $bet->setStatus(1);
        $bet->setBetStatus(0);
        $bet->setBalls($balls);

        $betType = $rawBet->getBetType();
        $bet->setBetType($betType);
        $generator = $this->getBetLineGenerator($betType);

        $betLines = $generator->getBetLines($balls);

        foreach ($betLines as $betLine) {
            $betLine->setSumma($summa);
            $bet->addBetLine($betLine);
        }

        return $bet;
    }

    /**
     * 
     * @param \Qwer\LottoBundle\Entity\BetType $betType
     * @return BetLineGenerator
     * @throws \ResourceNotFoundException
     */
    private function getBetLineGenerator(BetType $betType)
    {
        $serviceId = sprintf("bet_line.generator.%s", $betType->getCode());

        if (!$this->container->has($serviceId)) {
            $message = sprintf("Service \"%s\" was not found.", $serviceId);
            throw new ResourceNotFoundException($message);
        }

        $service = $this->container->get($serviceId);
        return $service;
    }

    public function setDrawFinder($drawFinder)
    {
        $this->drawFinder = $drawFinder;
    }

    

    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;
    }

    private function getDocumentType()
    {
        if(!is_null($this->documentType)){
            return $this->documentType;
        }
        $repo = $this->em->getRepository("QwerLottoDocumentsBundle:DocumentType");

        $document = $repo->find(1);
        return $document;
    }

}
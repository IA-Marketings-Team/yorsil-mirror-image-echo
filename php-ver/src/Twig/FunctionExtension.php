<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Repository\FlixbusRepository;
use App\Repository\RechargeRepository;
use App\Repository\RechargeflexiRepository;
use App\Repository\TransfertRepository;
use App\Repository\FichierRepository;
use App\Repository\ProduitPhysiqueRepository;
use App\Service\Metier\ServiceMetierBoutique;
use App\Service\Metier\ServiceMetierTransfert;
use App\Entity\User;
use App\Service\Utils\Util;

class FunctionExtension extends AbstractExtension
{

    public function __construct(ServiceMetierBoutique $_boutique_manager,
                                ServiceMetierTransfert $_transfert_manager,
                                ProduitPhysiqueRepository $_produitPhysiqueRepository,
                                RechargeRepository $rechargeRepository,
                                RechargeflexiRepository $rechargeflexiRepository,
                                FlixbusRepository $flixbusRepository,
                                FichierRepository $fichierRepository,
                                TransfertRepository $transfertRepository){
        $this->_boutique_manager   = $_boutique_manager;
        $this->_transfert_manager  = $_transfert_manager;
        $this->_produitPhysiqueRepository = $_produitPhysiqueRepository;
        $this->rechargeRepository  = $rechargeRepository;
        $this->transfertRepository = $transfertRepository;
        $this->flixbusRepository   = $flixbusRepository;
        $this->fichierRepository   = $fichierRepository;
        $this->transfertDiaspoRepository = $rechargeflexiRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('creditBoutique', [$this, 'getCreditBoutique']),
            new TwigFunction('debitBoutique', [$this, 'getDebitBoutique']),
            new TwigFunction('gesteBoutique', [$this, 'getGesteBoutique']),
            new TwigFunction('seuilBoutique', [$this, 'getSeuilBoutique']),
            new TwigFunction('devise', [$this, 'getDevise']),
            new TwigFunction('jsonDecode', [$this, 'jsonDecode']),
            new TwigFunction('encodeBase64', [$this, 'encodeBase64'], ['is_safe' => ['html']]),
            new TwigFunction('toSlug', [$this, 'toSlug']),
            new TwigFunction('soldeParProduitPhysique', [$this, 'soldeParProduitPhysique']),
            new TwigFunction('carteVenduParMois', [$this, 'carteVenduParMois']),
            new TwigFunction('carteVenduParAnnee', [$this, 'carteVenduParAnnee']),
            new TwigFunction('carteVenduParJour', [$this, 'carteVenduParJour']),
            new TwigFunction('carteVenduMoisDernier', [$this, 'carteVenduMoisDernier']),
            new TwigFunction('transfertParMois', [$this, 'transfertParMois']),
            new TwigFunction('transfertParAnnee', [$this, 'transfertParAnnee']),
            new TwigFunction('transfertParDays', [$this, 'transfertParDays']),
            new TwigFunction('transfertDiaspoParMois', [$this, 'transfertDiaspoParMois']),
            new TwigFunction('transfertDiaspoParAnnee', [$this, 'transfertDiaspoParAnnee']),
            new TwigFunction('transfertDiaspoParDays', [$this, 'transfertDiaspoParDays']),
            new TwigFunction('transfertDiaspoMoisDernier', [$this, 'transfertDiaspoMoisDernier']),
            new TwigFunction('transfertMoisDernier', [$this, 'transfertMoisDernier']),
            new TwigFunction('reservationParMois', [$this, 'reservationParMois']),
            new TwigFunction('reservationParAnnee', [$this, 'reservationParAnnee']),
            new TwigFunction('reservationParJour', [$this, 'reservationParJour']),
            new TwigFunction('reservationMoisDernier', [$this, 'reservationMoisDernier']),
            new TwigFunction('checkHash', [$this, 'checkHash']),
            new TwigFunction('creditBoutiqueDater', [$this, 'getCreditBoutiqueDater']),
            new TwigFunction('debitBoutiqueDater', [$this, 'getDebitBoutiqueDater']),
            new TwigFunction('gesteBoutiqueDater', [$this, 'getGesteBoutiqueDater']),
            new TwigFunction('getOneFile', [$this, 'getOneFile']),
            new TwigFunction('verifTypeFile', [$this, 'verifTypeFile'])
        ];
    }

    public function getCreditBoutique(User $user){
        $resultat = $this->_boutique_manager->creditBoutique($user);
        return $resultat;
    }

    public function getDebitBoutique(User $user){
        $resultat = $this->_boutique_manager->debitBoutique($user);
        return $resultat;
    }

    public function getGesteBoutique(User $user){
        $resultat = $this->_boutique_manager->gesteBoutique($user);
        return $resultat;
    } 

    public function getSeuilBoutique(User $user){
        $resultat = $this->_boutique_manager->seuilBoutique($user);
        return $resultat;
    } 

    public function getDevise($base_price,$code){
        $resultat = $this->_transfert_manager->getDevisePays($base_price,$code);
        return $resultat;
    }

    public function jsonDecode($json){
        $decode = json_decode($json, true);
        return $decode;
    }

    public function encodeBase64($base64){
        return sprintf('<iframe src="data:application/pdf;base64,%s" width="100%%" height="600px"></iframe>', $base64);
    }

    public function toSlug($string){
        $_slug = Util::slug($string);
        return $_slug;
    }

    public function soldeParProduitPhysique($id){
        $_result = $this->_produitPhysiqueRepository->soldeProduitPhysiqueById($id);
        return $_result;
    }

    public function carteVenduParMois(){
        return $this->rechargeRepository->countSoldRechargeByMonth();
    }
    public function carteVenduParAnnee(){
        return $this->rechargeRepository->countSoldRechargeByYear();
    }
    public function carteVenduParJour(){
        return $this->rechargeRepository->countSoldRechargeDays();
    }
    public function carteVenduMoisDernier(){
        return $this->rechargeRepository->countSoldRechargeByMonthBefore();
    }

    public function transfertParMois(){
        return $this->transfertRepository->countTransfertByMonth();
    }

    public function transfertParAnnee(){
        return $this->transfertRepository->countTransfertByYear();
    }

    public function transfertParDays(){
        return $this->transfertRepository->countTransfertDays();
    }

    public function transfertMoisDernier(){
        return $this->transfertRepository->countTransfertByMonthBefore();
    }

    public function reservationParMois(){
        return $this->flixbusRepository->countReservationByMonth();
    }
    public function reservationParAnnee(){
        return $this->flixbusRepository->countReservationByYear();
    }
    public function reservationParJour(){
        return $this->flixbusRepository->countReservationDays();
    }
    public function reservationMoisDernier(){
        return $this->flixbusRepository->countReservationByMonthBefore();
    }

    public function transfertDiaspoParMois(){
        return $this->transfertDiaspoRepository->countTransfertByMonth();
    }

    public function transfertDiaspoParAnnee(){
        return $this->transfertDiaspoRepository->countTransfertByYear();
    }

    public function transfertDiaspoParDays(){
        return $this->transfertDiaspoRepository->countTransfertDays();
    }

    public function transfertDiaspoMoisDernier(){
        return $this->transfertDiaspoRepository->countTransfertByMonthBefore();
    }
    

    public function checkHash($data, $key)
    {
        $supported_sign_algos = array('sha256_hmac');
        if (!in_array($data['kr-hash-algorithm'], $supported_sign_algos)) {
            return false;
        }
        $kr_answer = str_replace('\/', '/', $data['kr-answer']);
        $hash = hash_hmac('sha256', $kr_answer, $key);
        return ($hash == $data['kr-hash']);
    }

    public function getCreditBoutiqueDater(User $user,$_date){
        $resultat = $this->_boutique_manager->creditBoutiqueDater($user,$_date);
        return $resultat;
    }

    public function getDebitBoutiqueDater(User $user,$_date){
        $resultat = $this->_boutique_manager->debitBoutiqueDater($user,$_date);
        return $resultat;
    }

    public function getGesteBoutiqueDater(User $user,$_date){
        $resultat = $this->_boutique_manager->gesteBoutiqueDater($user,$_date);
        return $resultat;
    }

    public function getOneFile($value){
        $resultat = $this->fichierRepository->findOneFileById($value);
        return $resultat;
    }

    public function verifTypeFile($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            return 'pdf';
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return 'img';
        } else {
            return false;
        }
    }
    
}
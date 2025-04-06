<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Service\Metier\ServiceMetierBoutique;
use App\Service\Metier\ServiceMetierTransfert;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\NotificationrechargementRepository;
use App\Repository\TransfertRepository;
use App\Repository\BoutRepository;
use App\Repository\NotificationTrxRepository;

class NotificatioExtension extends AbstractExtension
{
    private $notifRepo;
    private $boutRepository;
    private $notifrechargeRepo;
    private $notifTrxRepo;

    public function __construct(
        NotificationRepository $notifRepo,
        BoutRepository $boutRepository,
        NotificationrechargementRepository $notifrechargeRepo,
        NotificationTrxRepository $notifTrxRepo)
    {
        $this->notifRepo         = $notifRepo;
        $this->boutRepository    = $boutRepository;
        $this->notifrechargeRepo = $notifrechargeRepo;
        $this->notifTrxRepo      = $notifTrxRepo;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('notifiListe', [$this, 'notifiListe']),
            new TwigFunction('notifiCount', [$this, 'notifiCount']),
            new TwigFunction('notifiListeBoutique', [$this, 'notifiListeBoutique']),
            new TwigFunction('notifiCountBoutique', [$this, 'notifiCountBoutique']),
            new TwigFunction('notifiListeRechargement', [$this, 'notifiListeRechargement']),
            new TwigFunction('notifiCountRechargement', [$this, 'notifiCountRechargement']),
            new TwigFunction('notifiCountTrxAdmin', [$this, 'notifiCountTrxAdmin']),
            new TwigFunction('notifTrxAdminNotRead', [$this, 'notifTrxAdminNotRead']),
            new TwigFunction('notifiCountTrxBout', [$this, 'notifiCountTrxBout']),
            new TwigFunction('notifTrxBoutNotRead', [$this, 'notifTrxBoutNotRead']),
        ];
    }

    public function notifiListe(){
        $notifListe = $this->notifRepo->findUnreadAdminNotificationsForToday();
        return $notifListe;
    }

    public function notifiListeBoutique($_user){
        $_boutique  = $this->boutRepository->findOneBy(['user' => $_user]);
        $notifListe = $this->notifRepo->findBy([ 'boutique' => $_boutique,'isRead' => null, 'is_admin' => false],['id' => 'DESC'], 5);

        return $notifListe;
    }

    public function notifiCount(){
        $notifiCount = $this->notifRepo->countUnreadAdminNotificationsForToday();
        return $notifiCount;
    }

    public function notifiCountBoutique($_user){
        $_boutique   = $this->boutRepository->findOneBy(['user' => $_user]);
        $notifiCount = $this->notifRepo->findBy(['boutique' => $_boutique,'isRead' => null,'is_admin' => false]);
        $nbr = COUNT($notifiCount);
        return $nbr;
    }

    public function notifiListeRechargement(){
        $notifListe = $this->notifrechargeRepo->findUnreadAdminNotificationsRechargementForToday();
        return $notifListe;
    }
    
    public function notifiCountRechargement(){
        $notifiCount = $this->notifrechargeRepo->countUnreadAdminNotificationsRechargementForToday();
        return $notifiCount;
    }
    
    public function notifiCountTrxAdmin(){
        $notifiCount = $this->notifTrxRepo->findBy(['is_read' => false, 'is_admin' => true]);
        $nbr = COUNT($notifiCount);
        return $nbr;
    }
    
    public function notifTrxAdminNotRead(){
        $notif = $this->notifTrxRepo->findAdminNotifTrxNotRead();
        return $notif;
    }
    
    public function notifiCountTrxBout($user){
        $_boutique   = $this->boutRepository->findOneBy(['user' => $user]);
        $notifiCount = $this->notifTrxRepo->findBy(['boutique' => $_boutique, 'is_read' => false, 'is_admin' => false]);
        $nbr = COUNT($notifiCount);
        return $nbr;
    }
    
    public function notifTrxBoutNotRead($user){
        $_boutique = $this->boutRepository->findOneBy(['user' => $user]);
        $notif     = $this->notifTrxRepo->findBy(['boutique' => $_boutique, 'is_read' => false, 'is_admin' => false]);
        return $notif;
    }
}
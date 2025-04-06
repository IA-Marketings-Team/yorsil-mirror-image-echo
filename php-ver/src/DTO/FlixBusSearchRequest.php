<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FlixBusSearchRequest
{
    #[Assert\NotBlank]
    #[Assert\Choice(['stations', 'cities'])]
    public string $type;

    #[Assert\NotBlank]
    public string $departId;

    #[Assert\NotBlank] 
    public string $arriveId;

    #[Assert\NotBlank]
    #[Assert\DateTime(format: 'd.m.Y')]
    public string $departDate;

    #[Assert\DateTime(format: 'd.m.Y')]
    public ?string $arriveDate = null;

    #[Assert\PositiveOrZero]
    public int $adults = 1;

    #[Assert\PositiveOrZero] 
    public int $children = 0;

    #[Assert\PositiveOrZero]
    public int $bikes = 0;

    public bool $passport = false;
    
    public bool $isRoundTrip = false;
    
    #[Assert\Expression(
        "!this.isRoundTrip || this.arriveDate !== null",
        message: "La date de retour est obligatoire pour un aller-retour"
    )]
    public ?string $returnDate = null;
}

<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @Groups({"transaction"})
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="string", length=255)
     */
    private $destination_number;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $account_id;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $transaction_code;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="integer")
     */
    private $api_key;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="integer")
     */
    private $seller;

    /**
     * @ORM\Column(type="integer")
     */
    private $service_items_id;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="float", nullable=true)
     */
    private $benefits;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="integer")
     */
    private $fees;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $wallet_id;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sms_validation_status;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="integer")
     */
    private $validation_status_code;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $external_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $error_type_id;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="float")
     */
    private $new_wallet_amount;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="integer")
     */
    private $new_account_amount;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $custom_data;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $json_data;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="string", length=255)
     */
    private $meta_data;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="boolean")
     */
    private $checked;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="boolean")
     */
    private $is_from_auto_recharge;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $debit_fees_from;

    /**
     * @Groups({"transaction"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $trx_status;

    public function __construct()
    {
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDestinationNumber(): ?string
    {
        return $this->destination_number;
    }

    public function setDestinationNumber(string $destination_number): self
    {
        $this->destination_number = $destination_number;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAccountId(): ?int
    {
        return $this->account_id;
    }

    public function setAccountId(int $account_id): self
    {
        $this->account_id = $account_id;

        return $this;
    }

    public function getTransactionCode(): ?string
    {
        return $this->transaction_code;
    }

    public function setTransactionCode(string $transaction_code): self
    {
        $this->transaction_code = $transaction_code;

        return $this;
    }

    public function getApiKey(): ?int
    {
        return $this->api_key;
    }

    public function setApiKey(int $api_key): self
    {
        $this->api_key = $api_key;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSeller(): ?int
    {
        return $this->seller;
    }

    public function setSeller(int $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getServiceItemsId(): ?int
    {
        return $this->service_items_id;
    }

    public function setServiceItemsId(int $service_items_id): self
    {
        $this->service_items_id = $service_items_id;

        return $this;
    }

    public function getBenefits(): ?float
    {
        return $this->benefits;
    }

    public function setBenefits(float $benefits): self
    {
        $this->benefits = $benefits;

        return $this;
    }

    public function getFees(): ?int
    {
        return $this->fees;
    }

    public function setFees(int $fees): self
    {
        $this->fees = $fees;

        return $this;
    }

    public function getWalletId(): ?int
    {
        return $this->wallet_id;
    }

    public function setWalletId(int $wallet_id): self
    {
        $this->wallet_id = $wallet_id;

        return $this;
    }

    public function getSmsValidationStatus(): ?string
    {
        return $this->sms_validation_status;
    }

    public function setSmsValidationStatus(string $sms_validation_status): self
    {
        $this->sms_validation_status = $sms_validation_status;

        return $this;
    }

    public function getValidationStatusCode(): ?int
    {
        return $this->validation_status_code;
    }

    public function setValidationStatusCode(int $validation_status_code): self
    {
        $this->validation_status_code = $validation_status_code;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->external_id;
    }

    public function setExternalId(string $external_id): self
    {
        $this->external_id = $external_id;

        return $this;
    }

    public function getErrorTypeId(): ?int
    {
        return $this->error_type_id;
    }

    public function setErrorTypeId(int $error_type_id): self
    {
        $this->error_type_id = $error_type_id;

        return $this;
    }

    public function getNewWalletAmount(): ?float
    {
        return $this->new_wallet_amount;
    }

    public function setNewWalletAmount(float $new_wallet_amount): self
    {
        $this->new_wallet_amount = $new_wallet_amount;

        return $this;
    }

    public function getNewAccountAmount(): ?int
    {
        return $this->new_account_amount;
    }

    public function setNewAccountAmount(int $new_account_amount): self
    {
        $this->new_account_amount = $new_account_amount;

        return $this;
    }

    public function getCustomData(): ?string
    {
        return $this->custom_data;
    }

    public function setCustomData(?string $custom_data): self
    {
        $this->custom_data = $custom_data;

        return $this;
    }

    public function getJsonData(): ?string
    {
        return $this->json_data;
    }

    public function setJsonData(?string $json_data): self
    {
        $this->json_data = $json_data;

        return $this;
    }

    public function getMetaData(): ?string
    {
        return $this->meta_data;
    }

    public function setMetaData(string $meta_data): self
    {
        $this->meta_data = $meta_data;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): self
    {
        $this->checked = $checked;

        return $this;
    }

    public function getIsFromAutoRecharge(): ?bool
    {
        return $this->is_from_auto_recharge;
    }

    public function setIsFromAutoRecharge(bool $is_from_auto_recharge): self
    {
        $this->is_from_auto_recharge = $is_from_auto_recharge;

        return $this;
    }

    public function getDebitFeesFrom(): ?int
    {
        return $this->debit_fees_from;
    }

    public function setDebitFeesFrom(?int $debit_fees_from): self
    {
        $this->debit_fees_from = $debit_fees_from;

        return $this;
    }

    public function getTrxStatus(): ?string
    {
        return $this->trx_status;
    }

    public function setTrxStatus(?string $trx_status): self
    {
        $this->trx_status = $trx_status;

        return $this;
    }
}
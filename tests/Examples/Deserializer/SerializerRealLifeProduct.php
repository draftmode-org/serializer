<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerRealLifeProduct {
    private SerializerRealLifeProductUUID $id;
    private SerializerRealLifeProductPrice $price;
    private SerializerRealLifeUserUUID $user;
    private ?string $description=null;
    /**
     * @var SerializerRealLifeProductLabel[]
     */
    private array $vLabels=[];
    /**
     * @param SerializerRealLifeProductUUID $id
     */
    public function __construct(SerializerRealLifeProductUUID $id)
    {
        $this->id       = $id;
        $this->price    = new SerializerRealLifeProductPrice;
        $this->user     = new SerializerRealLifeUserUUID;
    }

    /**
     * @param SerializerRealLifeProductPrice|null $price
     */
    public function setPrice(?SerializerRealLifeProductPrice $price): void
    {
        $this->price = $price ?: new SerializerRealLifeProductPrice;
    }

    /**
     * @return SerializerRealLifeProductUUID
     */
    public function getId(): SerializerRealLifeProductUUID
    {
        return $this->id;
    }

    /**
     * @return SerializerRealLifeProductPrice
     */
    public function getPrice(): SerializerRealLifeProductPrice
    {
        return $this->price;
    }

    /**
     * @param SerializerRealLifeUserUUID|null $user
     */
    public function setUser(?SerializerRealLifeUserUUID $user) : void {
        $this->user = $user ?: new SerializerRealLifeUserUUID();
    }

    /**
     * @return SerializerRealLifeUserUUID
     */
    public function getUser(): SerializerRealLifeUserUUID
    {
        return $this->user;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param SerializerRealLifeProductLabel[]|null $vLabels
     */
    public function setVLabels(?SerializerRealLifeProductLabel ...$vLabels): void
    {
        $this->vLabels = $vLabels ?? [];
    }

    /**
     * @return SerializerRealLifeProductLabel[]
     */
    public function getVLabels(): array
    {
        return $this->vLabels;
    }
}
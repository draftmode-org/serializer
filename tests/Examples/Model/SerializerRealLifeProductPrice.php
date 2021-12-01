<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Model;

class SerializerRealLifeProductPrice {
    private SerializerRealLifeProductAmount $regular;
    private SerializerRealLifeProductAmount $offer;

    public function __construct() {
        $this->regular  = new SerializerRealLifeProductAmount;
        $this->offer    = new SerializerRealLifeProductAmount;
    }

    /**
     * @return SerializerRealLifeProductAmount
     */
    public function getRegular(): SerializerRealLifeProductAmount
    {
        return $this->regular;
    }

    /**
     * @param SerializerRealLifeProductAmount|null $regular
     */
    public function setRegular(?SerializerRealLifeProductAmount $regular): void {
        $this->regular = $regular ?? new SerializerRealLifeProductAmount;
    }

    /**
     * @return SerializerRealLifeProductAmount
     */
    public function getOffer(): SerializerRealLifeProductAmount
    {
        return $this->offer;
    }

    /**
     * @param SerializerRealLifeProductAmount|null $offer
     */
    public function setOffer(?SerializerRealLifeProductAmount $offer): void
    {
        $this->offer = $offer ?? new SerializerRealLifeProductAmount;
    }
}
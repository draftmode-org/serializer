<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Model;

class SerializerRealLifePerson {
    private string $name;
    private ?SerializerRealLifePersonAddress $address=null;

    /**
     * @param string $name
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return SerializerRealLifePersonAddress|null
     */
    public function getAddress(): ?SerializerRealLifePersonAddress
    {
        return $this->address;
    }

    /**
     * @param SerializerRealLifePersonAddress|null $address
     */
    public function setAddress(?SerializerRealLifePersonAddress $address): void
    {
        $this->address = $address;
    }


}
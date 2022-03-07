<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Model;

class SerializerRealLifePersonAddress {
    private string $street;
    private string $city;
    private ?int $zip=null;

    /**
     * @param string $street
     * @param string $city
     */
    public function __construct(string $street, string $city) {
        $this->street = $street;
        $this->city = $city;
        $this->zip = null;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @param int|null $zip
     */
    public function setZip(?int $zip): void {
        $this->zip = $zip;
    }

    /**
     * @return int|null
     */
    public function getZip(): ?int
    {
        return $this->zip;
    }

}
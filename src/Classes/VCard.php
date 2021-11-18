<?php

declare(strict_types=1);


namespace lindesbs\vcard\Classes;

use lindesbs\vcard\Classes\VCardKind;
use OutOfRangeException;
use Symfony\Component\Uid\Uuid;

class VCard
{

    public const TYPE_HOME = 0x02;
    public const TYPE_WORK = 0x04;


    private $ValidTypes = [self::TYPE_HOME => "HOME", self::TYPE_WORK => "WORK"];

    /**
     * @var string
     */
    private $_fullname;
    /**
     * @var bool
     */
    private $_fullnameManualSet;

    /**
     * @var string
     */
    private $_firstname;
    /**
     * @var string
     */
    private $_lastname;

    private $_additionalName;

    /**
     * @var string
     */
    private $_prefix;
    /**
     * @var string
     */
    private $_suffix;


    /**
     * @var
     */
    private $_gender;
    /**
     * @var
     */
    private $_anniversary;
    /**
     * @var
     */
    private $_birthday;
    /**
     * @var
     */
    private $_category;


    /**
     * @var
     */
    private $_geo;
    /**
     * @var
     */
    private $_key;
    /**
     * @var
     */
    private $_kind;


    /**
     * @var
     */
    private $_lang;
    /**
     * @var
     */
    private $_logo;
    /**
     * @var
     */
    private $_member;
    /**
     * @var
     */
    private $_n;
    /**
     * @var
     */
    private $_nickname;
    /**
     * @var
     */
    private $_note;
    /**
     * @var
     */
    private $_org;
    /**
     * @var
     */
    private $_photo;
    /**
     * @var
     */
    private $_prodid;
    /**
     * @var
     */
    private $_profile;
    /**
     * @var
     */
    private $_related;
    /**
     * @var
     */
    private $_rev;
    /**
     * @var
     */
    private $_role;
    /**
     * @var
     */
    private $_sound;
    /**
     * @var
     */
    private $_source;


    /**
     * @var
     */
    private $_title;
    /**
     * @var
     */
    private $_tz;
    /**
     * @var
     */
    private $_uid;


    /**
     * @var
     */
    private $_typedData;


    protected $arrOutput;

    /**
     *
     */
    public function __construct($bPrefill = true)
    {
        $this->setUid(Uuid::v4());

        $this->arrOutput = [];
    }


    private function add($eigenschaft, $parameter, ...$attribut): void
    {
        $strData = $eigenschaft;
        if ($parameter)
        {
            $strData .= ";" . $parameter;
        }

        if ((!$attribut) || ((is_array($attribut)) && ($attribut[0] == null)))
        {
            return;
        }


        $strData .= ":" . implode(";", $attribut);

        $this->arrOutput[] = $strData;
    }

    public function dump()
    {
        dump($this->_typedData);
    }
    
    
    /**
     * @return string
     */
    public function export($type = self::TYPE_HOME): string
    {
        $arrType = [];
        foreach ($this->ValidTypes as $typeKey => $typeValue)
        {
            if (($type & $typeKey) == $typeKey)
            {
                $arrType[] = $typeValue;
            }
        }


        $strType = "";
        if (count($arrType) > 0)
        {
            $strType = "TYPE=" . implode(",", $arrType);
        }

        $this->arrOutput = [];
        $this->arrOutput[] = "BEGIN:VCARD";
        $this->arrOutput[] = "VERSION:4";
        $this->arrOutput[] = "UID:" . $this->getUid();

        $this->add("KIND", "", $this->getKind());
        $this->add("GENDER", "", $this->getGender());

        $this->add("N", "", $this->getLastname($type), $this->getFirstname($type), $this->getAdditionalName($type), $this->getPrefix($type), $this->getSuffix($type));
        $this->add("FN", "", $this->getFullname($type));

        $this->add("ADR", $strType, "LABEL=" . $this->getLabel($type),
            trim($this->getStreet($type) . ' ' . $this->getStreetNumber($type)),
            $this->getPlz($type),
            $this->getCity($type),
            $this->getState($type),
            $this->getCountry($type));

        $this->add("EMAIL", $strType, $this->getEmail($type));
        $this->add("URL", $strType, $this->getUrl($type));

        $this->arrOutput[] = "END:VCARD";
        return implode(PHP_EOL, $this->arrOutput);
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        if (!$this->_fullnameManualSet)
        {
            $this->_fullname = trim($this->getAdditionalName() . " " . $this->getFirstname() . ' ' . $this->getLastname());
        }

        return $this->_fullname;
    }

    /**
     * @param mixed $fullname
     */
    public function setFullname($fullname, $type = self::TYPE_HOME): void
    {
        $this->_fullnameManualSet = true;
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $fullname);
    }

    /**
     * @return mixed
     */
    public function getFirstname($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $firstname);
    }

    /**
     * @return mixed
     */
    public function getLastname($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $lastname);
    }

    /**
     * @return mixed
     */
    public function getPrefix($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $prefix);
    }

    /**
     * @return mixed
     */
    public function getSuffix($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $suffix
     */
    public function setSuffix($suffix, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $suffix);
    }

    /**
     * @return mixed
     */
    public function getStreet($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $street);
    }

    /**
     * @return mixed
     */
    public function getStreetNumber($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $streetNumber
     */
    public function setStreetNumber($streetNumber, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $streetNumber);
    }

    /**
     * @return mixed
     */
    public function getPlz($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $PLZ
     */
    public function setPlz($Plz, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $Plz);
    }

    /**
     * @return mixed
     */
    public function getCity($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $city
     */
    public function setCity($city, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $city);
    }

    /**
     * @return mixed
     */
    public function getState($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $state
     */
    public function setState($state, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $state);
    }

    /**
     * @return mixed
     */
    public function getCountry($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $country);
    }


    /**
     * @return mixed
     */
    public function getTel($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $tel);
    }

    /**
     * @return mixed
     */
    public function getTitle($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $title);
    }


    /**
     * @return mixed
     */
    public function getUrl($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $url);
    }

    /**
     * @return mixed
     */
    public function getAdditionalName($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $additionalName
     */
    public function setAdditionalName($additionalName, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $additionalName);
    }


    /**
     * @return mixed
     */
    public function getLabel($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $label);
    }


    /**
     * @return mixed
     */
    public function getEmail($type = self::TYPE_HOME)
    {
        return $this->getTypedData($type, str_replace("get", "", __FUNCTION__));
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email, $type = self::TYPE_HOME): void
    {
        $this->setTypedData($type, str_replace("set", "", __FUNCTION__), $email);
    }




    // ----------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->_gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender): void
    {
        $this->_gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getAnniversary()
    {
        return $this->_anniversary;
    }

    /**
     * @param mixed $anniversary
     */
    public function setAnniversary($anniversary): void
    {
        $this->_anniversary = $anniversary;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->_birthday;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday): void
    {
        $this->_birthday = $birthday;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->_category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->_category = $category;
    }


    /**
     * @return mixed
     */
    public function getGeo()
    {
        return $this->_geo;
    }

    /**
     * @param mixed $geo
     */
    public function setGeo($geo): void
    {
        $this->_geo = $geo;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key): void
    {
        $this->_key = $key;
    }

    /**
     * @return null|string
     */
    public function getKind(): ?string
    {
        return $this->_kind;
    }

    /**
     * @param string $kind
     */
    public function setKind(string $kind): void
    {
        $this->_kind = $kind;
    }


    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->_lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang): void
    {
        $this->_lang = $lang;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->_logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo): void
    {
        $this->_logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getMember()
    {
        return $this->_member;
    }

    /**
     * @param mixed $member
     */
    public function setMember($member): void
    {
        $this->_member = $member;
    }

    /**
     * @return mixed
     */
    public function getN($type = self::TYPE_HOME)
    {
        return $this->_n;
    }

    /**
     * @param mixed $n
     */
    public function setN($n): void
    {
        $this->_n = $n;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->_nickname;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname): void
    {
        $this->_nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getNote($type = self::TYPE_HOME)
    {
        return $this->_note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note): void
    {
        $this->_note = $note;
    }

    /**
     * @return mixed
     */
    public function getOrg()
    {
        return $this->_org;
    }

    /**
     * @param mixed $org
     */
    public function setOrg($org): void
    {
        $this->_org = $org;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->_photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo): void
    {
        $this->_photo = $photo;
    }

    /**
     * @return mixed
     */
    public function getProdid()
    {
        return $this->_prodid;
    }

    /**
     * @param mixed $prodid
     */
    public function setProdid($prodid): void
    {
        $this->_prodid = $prodid;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->_profile;
    }

    /**
     * @param mixed $profile
     */
    public function setProfile($profile): void
    {
        $this->_profile = $profile;
    }

    /**
     * @return mixed
     */
    public function getRelated()
    {
        return $this->_related;
    }

    /**
     * @param mixed $related
     */
    public function setRelated($related): void
    {
        $this->_related = $related;
    }

    /**
     * @return mixed
     */
    public function getRev()
    {
        return $this->_rev;
    }

    /**
     * @param mixed $rev
     */
    public function setRev($rev): void
    {
        $this->_rev = $rev;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role): void
    {
        $this->_role = $role;
    }

    /**
     * @return mixed
     */
    public function getSound()
    {
        return $this->_sound;
    }

    /**
     * @param mixed $sound
     */
    public function setSound($sound): void
    {
        $this->_sound = $sound;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source): void
    {
        $this->_source = $source;
    }

    /**
     * @return mixed
     */
    public function getTz()
    {
        return $this->_tz;
    }

    /**
     * @param mixed $tz
     */
    public function setTz($tz): void
    {
        $this->_tz = $tz;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->_uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid): void
    {
        $this->_uid = $uid;
    }


    /**
     * @param mixed $typedData
     */
    public function getTypedData($typedData, $org)
    {
        if (array_key_exists($typedData, $this->_typedData))
        {
            if (array_key_exists($org, $this->_typedData[$typedData]))
            {
                return $this->_typedData[$typedData][$org];
            }
        }

        return null;
    }


    /**
     * @param mixed $typedData
     */
    public function setTypedData($typedData, $org, $value): void
    {
        foreach ($this->ValidTypes as $typeKey => $typeValue)
        {
            if (($typedData & $typeKey) == $typeKey)
            {
                $this->_typedData[$typeKey][$org] = $value;
            }
        }
    }


}
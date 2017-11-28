<?php
/**
 * Auto generated from NetMessage.proto at 2017-11-14 02:15:26
 *
 * net_message package
 */

/**
 * Packet_Header message
 */
class Net_message_PacketHeader extends \ProtobufMessage
{
    /* Field index constants */
    const NFROM = 1;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::NFROM => array(
            'default' => 0, 
            'name' => 'nFrom',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::NFROM] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'nFrom' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNFrom($value)
    {
        return $this->set(self::NFROM, $value);
    }

    /**
     * Returns value of 'nFrom' property
     *
     * @return int
     */
    public function getNFrom()
    {
        return $this->get(self::NFROM);
    }
}

/**
 * W2P_Update_Module message
 */
class Net_message_W2PUpdateModule extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const NAME = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::NAME => array(
            'default' => '\"\"', 
            'name' => 'name',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::NAME] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setName($value)
    {
        return $this->set(self::NAME, $value);
    }

    /**
     * Returns value of 'name' property
     *
     * @return string
     */
    public function getName()
    {
        return $this->get(self::NAME);
    }
}

/**
 * P2W_Update_Module_Ack message
 */
class Net_message_P2WUpdateModuleAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Remove_Module message
 */
class Net_message_W2PRemoveModule extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const NAME = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::NAME => array(
            'default' => '\"\"', 
            'name' => 'name',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::NAME] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setName($value)
    {
        return $this->set(self::NAME, $value);
    }

    /**
     * Returns value of 'name' property
     *
     * @return string
     */
    public function getName()
    {
        return $this->get(self::NAME);
    }
}

/**
 * P2W_Remove_Module_Ack message
 */
class Net_message_P2WRemoveModuleAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Start_Module message
 */
class Net_message_W2PStartModule extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const NAME = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::NAME => array(
            'default' => '\"\"', 
            'name' => 'name',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::NAME] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setName($value)
    {
        return $this->set(self::NAME, $value);
    }

    /**
     * Returns value of 'name' property
     *
     * @return string
     */
    public function getName()
    {
        return $this->get(self::NAME);
    }
}

/**
 * P2W_Start_Module_Ack message
 */
class Net_message_P2WStartModuleAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Stop_Module message
 */
class Net_message_W2PStopModule extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const NAME = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::NAME => array(
            'default' => '\"\"', 
            'name' => 'name',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::NAME] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setName($value)
    {
        return $this->set(self::NAME, $value);
    }

    /**
     * Returns value of 'name' property
     *
     * @return string
     */
    public function getName()
    {
        return $this->get(self::NAME);
    }
}

/**
 * P2W_Stop_Module_Ack message
 */
class Net_message_P2WStopModuleAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Restart_Module message
 */
class Net_message_W2PRestartModule extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const NAME = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::NAME => array(
            'default' => '\"\"', 
            'name' => 'name',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::NAME] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setName($value)
    {
        return $this->set(self::NAME, $value);
    }

    /**
     * Returns value of 'name' property
     *
     * @return string
     */
    public function getName()
    {
        return $this->get(self::NAME);
    }
}

/**
 * P2W_Restart_Module_Ack message
 */
class Net_message_P2WRestartModuleAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Add_Cdp message
 */
class Net_message_W2PAddCdp extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const CDPTASKID = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::CDPTASKID => array(
            'default' => 0, 
            'name' => 'cdpTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::CDPTASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'cdpTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCdpTaskId($value)
    {
        return $this->set(self::CDPTASKID, $value);
    }

    /**
     * Returns value of 'cdpTaskId' property
     *
     * @return int
     */
    public function getCdpTaskId()
    {
        return $this->get(self::CDPTASKID);
    }
}

/**
 * P2W_Add_Cdp_Ack message
 */
class Net_message_P2WAddCdpAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Force_Add_Cdp message
 */
class Net_message_W2PForceAddCdp extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const CDPTASKID = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::CDPTASKID => array(
            'default' => 0, 
            'name' => 'cdpTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::CDPTASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'cdpTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCdpTaskId($value)
    {
        return $this->set(self::CDPTASKID, $value);
    }

    /**
     * Returns value of 'cdpTaskId' property
     *
     * @return int
     */
    public function getCdpTaskId()
    {
        return $this->get(self::CDPTASKID);
    }
}

/**
 * P2W_Force_Add_Cdp_Ack message
 */
class Net_message_P2WForceAddCdpAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Modify_Cdp message
 */
class Net_message_W2PModifyCdp extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const CDPTASKID = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::CDPTASKID => array(
            'default' => 0, 
            'name' => 'cdpTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::CDPTASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'cdpTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCdpTaskId($value)
    {
        return $this->set(self::CDPTASKID, $value);
    }

    /**
     * Returns value of 'cdpTaskId' property
     *
     * @return int
     */
    public function getCdpTaskId()
    {
        return $this->get(self::CDPTASKID);
    }
}

/**
 * P2W_Modify_Cdp_Ack message
 */
class Net_message_P2WModifyCdpAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Delete_Cdp message
 */
class Net_message_W2PDeleteCdp extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const CDPTASKID = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::CDPTASKID => array(
            'default' => 0, 
            'name' => 'cdpTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::CDPTASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'cdpTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCdpTaskId($value)
    {
        return $this->set(self::CDPTASKID, $value);
    }

    /**
     * Returns value of 'cdpTaskId' property
     *
     * @return int
     */
    public function getCdpTaskId()
    {
        return $this->get(self::CDPTASKID);
    }
}

/**
 * P2W_Delete_Cdp_Ack message
 */
class Net_message_P2WDeleteCdpAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Start_Cdp message
 */
class Net_message_W2PStartCdp extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const CDPTASKID = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::CDPTASKID => array(
            'default' => 0, 
            'name' => 'cdpTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::CDPTASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'cdpTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCdpTaskId($value)
    {
        return $this->set(self::CDPTASKID, $value);
    }

    /**
     * Returns value of 'cdpTaskId' property
     *
     * @return int
     */
    public function getCdpTaskId()
    {
        return $this->get(self::CDPTASKID);
    }
}

/**
 * P2W_Start_Cdp_Ack message
 */
class Net_message_P2WStartCdpAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Pause_Cdp message
 */
class Net_message_W2PPauseCdp extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const CDPTASKID = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::CDPTASKID => array(
            'default' => 0, 
            'name' => 'cdpTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::CDPTASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'cdpTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCdpTaskId($value)
    {
        return $this->set(self::CDPTASKID, $value);
    }

    /**
     * Returns value of 'cdpTaskId' property
     *
     * @return int
     */
    public function getCdpTaskId()
    {
        return $this->get(self::CDPTASKID);
    }
}

/**
 * P2W_Pause_Cdp_Ack message
 */
class Net_message_P2WPauseCdpAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Stop_Cdp message
 */
class Net_message_W2PStopCdp extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;
    const CDPTASKID = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::CDPTASKID => array(
            'default' => 0, 
            'name' => 'cdpTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
        $this->values[self::CDPTASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }

    /**
     * Sets value of 'cdpTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCdpTaskId($value)
    {
        return $this->set(self::CDPTASKID, $value);
    }

    /**
     * Returns value of 'cdpTaskId' property
     *
     * @return int
     */
    public function getCdpTaskId()
    {
        return $this->get(self::CDPTASKID);
    }
}

/**
 * P2W_Stop_Cdp_Ack message
 */
class Net_message_P2WStopCdpAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Create_Snap message
 */
class Net_message_W2PCreateSnap extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }
}

/**
 * P2W_Create_Snap_Ack message
 */
class Net_message_P2WCreateSnapAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Merge_Snap message
 */
class Net_message_W2PMergeSnap extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }
}

/**
 * P2W_Merge_Snap_Ack message
 */
class Net_message_P2WMergeSnapAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Update_Cloning_Info message
 */
class Net_message_W2PUpdateCloningInfo extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }
}

/**
 * P2W_Update_Cloning_Info_Ack message
 */
class Net_message_P2WUpdateCloningInfoAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;
    const PROGRESS = 3;
    const SPEED = 4;
    const NTIME = 5;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
        self::PROGRESS => array(
            'default' => 0, 
            'name' => 'progress',
            'required' => true,
            'type' => 5,
        ),
        self::SPEED => array(
            'default' => 0, 
            'name' => 'speed',
            'required' => true,
            'type' => 5,
        ),
        self::NTIME => array(
            'default' => 0, 
            'name' => 'ntime',
            'required' => false,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
        $this->values[self::PROGRESS] = null;
        $this->values[self::SPEED] = null;
        $this->values[self::NTIME] = 0;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }

    /**
     * Sets value of 'progress' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setProgress($value)
    {
        return $this->set(self::PROGRESS, $value);
    }

    /**
     * Returns value of 'progress' property
     *
     * @return int
     */
    public function getProgress()
    {
        return $this->get(self::PROGRESS);
    }

    /**
     * Sets value of 'speed' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setSpeed($value)
    {
        return $this->set(self::SPEED, $value);
    }

    /**
     * Returns value of 'speed' property
     *
     * @return int
     */
    public function getSpeed()
    {
        return $this->get(self::SPEED);
    }

    /**
     * Sets value of 'ntime' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNtime($value)
    {
        return $this->set(self::NTIME, $value);
    }

    /**
     * Returns value of 'ntime' property
     *
     * @return int
     */
    public function getNtime()
    {
        return $this->get(self::NTIME);
    }
}

/**
 * Recovering_Info message
 */
class Net_message_RecoveringInfo extends \ProtobufMessage
{
    /* Field index constants */
    const PROGRESS = 1;
    const SPEED = 2;
    const ELAPSEDTIME = 3;
    const NISTEMP = 4;
    const NHARDDISK = 5;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::PROGRESS => array(
            'default' => 0, 
            'name' => 'progress',
            'required' => true,
            'type' => 5,
        ),
        self::SPEED => array(
            'default' => 0, 
            'name' => 'speed',
            'required' => true,
            'type' => 5,
        ),
        self::ELAPSEDTIME => array(
            'default' => 0, 
            'name' => 'elapsedTime',
            'required' => true,
            'type' => 5,
        ),
        self::NISTEMP => array(
            'default' => 0, 
            'name' => 'nistemp',
            'required' => true,
            'type' => 5,
        ),
        self::NHARDDISK => array(
            'default' => 0, 
            'name' => 'nharddisk',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::PROGRESS] = null;
        $this->values[self::SPEED] = null;
        $this->values[self::ELAPSEDTIME] = null;
        $this->values[self::NISTEMP] = null;
        $this->values[self::NHARDDISK] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'progress' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setProgress($value)
    {
        return $this->set(self::PROGRESS, $value);
    }

    /**
     * Returns value of 'progress' property
     *
     * @return int
     */
    public function getProgress()
    {
        return $this->get(self::PROGRESS);
    }

    /**
     * Sets value of 'speed' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setSpeed($value)
    {
        return $this->set(self::SPEED, $value);
    }

    /**
     * Returns value of 'speed' property
     *
     * @return int
     */
    public function getSpeed()
    {
        return $this->get(self::SPEED);
    }

    /**
     * Sets value of 'elapsedTime' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setElapsedTime($value)
    {
        return $this->set(self::ELAPSEDTIME, $value);
    }

    /**
     * Returns value of 'elapsedTime' property
     *
     * @return int
     */
    public function getElapsedTime()
    {
        return $this->get(self::ELAPSEDTIME);
    }

    /**
     * Sets value of 'nistemp' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNistemp($value)
    {
        return $this->set(self::NISTEMP, $value);
    }

    /**
     * Returns value of 'nistemp' property
     *
     * @return int
     */
    public function getNistemp()
    {
        return $this->get(self::NISTEMP);
    }

    /**
     * Sets value of 'nharddisk' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNharddisk($value)
    {
        return $this->set(self::NHARDDISK, $value);
    }

    /**
     * Returns value of 'nharddisk' property
     *
     * @return int
     */
    public function getNharddisk()
    {
        return $this->get(self::NHARDDISK);
    }
}

/**
 * Migrating_Info message
 */
class Net_message_MigratingInfo extends \ProtobufMessage
{
    /* Field index constants */
    const PROGRESS = 1;
    const SPEED = 2;
    const ELAPSEDTIME = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::PROGRESS => array(
            'default' => 0, 
            'name' => 'progress',
            'required' => true,
            'type' => 5,
        ),
        self::SPEED => array(
            'default' => 0, 
            'name' => 'speed',
            'required' => true,
            'type' => 5,
        ),
        self::ELAPSEDTIME => array(
            'default' => 0, 
            'name' => 'elapsedTime',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::PROGRESS] = null;
        $this->values[self::SPEED] = null;
        $this->values[self::ELAPSEDTIME] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'progress' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setProgress($value)
    {
        return $this->set(self::PROGRESS, $value);
    }

    /**
     * Returns value of 'progress' property
     *
     * @return int
     */
    public function getProgress()
    {
        return $this->get(self::PROGRESS);
    }

    /**
     * Sets value of 'speed' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setSpeed($value)
    {
        return $this->set(self::SPEED, $value);
    }

    /**
     * Returns value of 'speed' property
     *
     * @return int
     */
    public function getSpeed()
    {
        return $this->get(self::SPEED);
    }

    /**
     * Sets value of 'elapsedTime' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setElapsedTime($value)
    {
        return $this->set(self::ELAPSEDTIME, $value);
    }

    /**
     * Returns value of 'elapsedTime' property
     *
     * @return int
     */
    public function getElapsedTime()
    {
        return $this->get(self::ELAPSEDTIME);
    }
}

/**
 * W2P_Start_Recover message
 */
class Net_message_W2PStartRecover extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const RECOVERTASKID = 2;
    const NISTEMP = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::RECOVERTASKID => array(
            'default' => 0, 
            'name' => 'recoverTaskId',
            'required' => true,
            'type' => 5,
        ),
        self::NISTEMP => array(
            'default' => 0, 
            'name' => 'nIsTemp',
            'required' => false,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::RECOVERTASKID] = null;
        $this->values[self::NISTEMP] = 0;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'recoverTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setRecoverTaskId($value)
    {
        return $this->set(self::RECOVERTASKID, $value);
    }

    /**
     * Returns value of 'recoverTaskId' property
     *
     * @return int
     */
    public function getRecoverTaskId()
    {
        return $this->get(self::RECOVERTASKID);
    }

    /**
     * Sets value of 'nIsTemp' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNIsTemp($value)
    {
        return $this->set(self::NISTEMP, $value);
    }

    /**
     * Returns value of 'nIsTemp' property
     *
     * @return int
     */
    public function getNIsTemp()
    {
        return $this->get(self::NISTEMP);
    }
}

/**
 * P2W_Start_Recover_Ack message
 */
class Net_message_P2WStartRecoverAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Get_Recovering_Info message
 */
class Net_message_W2PGetRecoveringInfo extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const RECOVERTASKID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::RECOVERTASKID => array(
            'default' => 0, 
            'name' => 'recoverTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::RECOVERTASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'recoverTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setRecoverTaskId($value)
    {
        return $this->set(self::RECOVERTASKID, $value);
    }

    /**
     * Returns value of 'recoverTaskId' property
     *
     * @return int
     */
    public function getRecoverTaskId()
    {
        return $this->get(self::RECOVERTASKID);
    }
}

/**
 * P2W_Get_Recovering_Info_Ack message
 */
class Net_message_P2WGetRecoveringInfoAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;
    const RECOVERINGINFO = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
        self::RECOVERINGINFO => array(
            'name' => 'recoveringInfo',
            'required' => true,
            'type' => 'Net_message_RecoveringInfo'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
        $this->values[self::RECOVERINGINFO] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }

    /**
     * Sets value of 'recoveringInfo' property
     *
     * @param Net_message_RecoveringInfo $value Property value
     *
     * @return null
     */
    public function setRecoveringInfo(Net_message_RecoveringInfo $value)
    {
        return $this->set(self::RECOVERINGINFO, $value);
    }

    /**
     * Returns value of 'recoveringInfo' property
     *
     * @return Net_message_RecoveringInfo
     */
    public function getRecoveringInfo()
    {
        return $this->get(self::RECOVERINGINFO);
    }
}

/**
 * W2P_Start_Migrate message
 */
class Net_message_W2PStartMigrate extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const TASKID = 2;
    const MOVETASKID = 3;
    const SOURCEUNIQUEID = 4;
    const TARGETUNIQUEID = 5;
    const SELECTEDPARTITIONS = 6;
    const HARDDISKVERSUS = 7;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::TASKID => array(
            'default' => 0, 
            'name' => 'taskId',
            'required' => true,
            'type' => 5,
        ),
        self::MOVETASKID => array(
            'default' => 0, 
            'name' => 'moveTaskId',
            'required' => true,
            'type' => 5,
        ),
        self::SOURCEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'sourceUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::TARGETUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'targetUniqueId',
            'required' => true,
            'type' => 7,
        ),
        self::SELECTEDPARTITIONS => array(
            'default' => '\"\"', 
            'name' => 'selectedPartitions',
            'required' => true,
            'type' => 7,
        ),
        self::HARDDISKVERSUS => array(
            'default' => '\"\"', 
            'name' => 'harddiskVersus',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::TASKID] = null;
        $this->values[self::MOVETASKID] = null;
        $this->values[self::SOURCEUNIQUEID] = null;
        $this->values[self::TARGETUNIQUEID] = null;
        $this->values[self::SELECTEDPARTITIONS] = null;
        $this->values[self::HARDDISKVERSUS] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'taskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setTaskId($value)
    {
        return $this->set(self::TASKID, $value);
    }

    /**
     * Returns value of 'taskId' property
     *
     * @return int
     */
    public function getTaskId()
    {
        return $this->get(self::TASKID);
    }

    /**
     * Sets value of 'moveTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setMoveTaskId($value)
    {
        return $this->set(self::MOVETASKID, $value);
    }

    /**
     * Returns value of 'moveTaskId' property
     *
     * @return int
     */
    public function getMoveTaskId()
    {
        return $this->get(self::MOVETASKID);
    }

    /**
     * Sets value of 'sourceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSourceUniqueId($value)
    {
        return $this->set(self::SOURCEUNIQUEID, $value);
    }

    /**
     * Returns value of 'sourceUniqueId' property
     *
     * @return string
     */
    public function getSourceUniqueId()
    {
        return $this->get(self::SOURCEUNIQUEID);
    }

    /**
     * Sets value of 'targetUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setTargetUniqueId($value)
    {
        return $this->set(self::TARGETUNIQUEID, $value);
    }

    /**
     * Returns value of 'targetUniqueId' property
     *
     * @return string
     */
    public function getTargetUniqueId()
    {
        return $this->get(self::TARGETUNIQUEID);
    }

    /**
     * Sets value of 'selectedPartitions' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSelectedPartitions($value)
    {
        return $this->set(self::SELECTEDPARTITIONS, $value);
    }

    /**
     * Returns value of 'selectedPartitions' property
     *
     * @return string
     */
    public function getSelectedPartitions()
    {
        return $this->get(self::SELECTEDPARTITIONS);
    }

    /**
     * Sets value of 'harddiskVersus' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setHarddiskVersus($value)
    {
        return $this->set(self::HARDDISKVERSUS, $value);
    }

    /**
     * Returns value of 'harddiskVersus' property
     *
     * @return string
     */
    public function getHarddiskVersus()
    {
        return $this->get(self::HARDDISKVERSUS);
    }
}

/**
 * P2W_Start_Migrate_Ack message
 */
class Net_message_P2WStartMigrateAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Get_Migrating_Info message
 */
class Net_message_W2PGetMigratingInfo extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const MOVETASKID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::MOVETASKID => array(
            'default' => 0, 
            'name' => 'moveTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::MOVETASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'moveTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setMoveTaskId($value)
    {
        return $this->set(self::MOVETASKID, $value);
    }

    /**
     * Returns value of 'moveTaskId' property
     *
     * @return int
     */
    public function getMoveTaskId()
    {
        return $this->get(self::MOVETASKID);
    }
}

/**
 * P2W_Get_Migrating_Info_Ack message
 */
class Net_message_P2WGetMigratingInfoAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;
    const MIGRATINGINFO = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
        self::MIGRATINGINFO => array(
            'name' => 'migratingInfo',
            'required' => true,
            'type' => 'Net_message_MigratingInfo'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
        $this->values[self::MIGRATINGINFO] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }

    /**
     * Sets value of 'migratingInfo' property
     *
     * @param Net_message_MigratingInfo $value Property value
     *
     * @return null
     */
    public function setMigratingInfo(Net_message_MigratingInfo $value)
    {
        return $this->set(self::MIGRATINGINFO, $value);
    }

    /**
     * Returns value of 'migratingInfo' property
     *
     * @return Net_message_MigratingInfo
     */
    public function getMigratingInfo()
    {
        return $this->get(self::MIGRATINGINFO);
    }
}

/**
 * W2P_Create_Small_Snap message
 */
class Net_message_W2PCreateSmallSnap extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const NID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::NID => array(
            'name' => 'nId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::NID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'nId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNId($value)
    {
        return $this->set(self::NID, $value);
    }

    /**
     * Returns value of 'nId' property
     *
     * @return int
     */
    public function getNId()
    {
        return $this->get(self::NID);
    }
}

/**
 * P2W_Create_Small_Snap_Ack message
 */
class Net_message_P2WCreateSmallSnapAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Create_Ahdr_File message
 */
class Net_message_W2PCreateAhdrFile extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const SSTORAGEID = 2;
    const SPATH = 3;
    const NSYSTYPE = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::SSTORAGEID => array(
            'name' => 'sStorageId',
            'required' => true,
            'type' => 7,
        ),
        self::SPATH => array(
            'name' => 'spath',
            'required' => true,
            'type' => 7,
        ),
        self::NSYSTYPE => array(
            'name' => 'nSysType',
            'required' => false,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::SSTORAGEID] = null;
        $this->values[self::SPATH] = null;
        $this->values[self::NSYSTYPE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'sStorageId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSStorageId($value)
    {
        return $this->set(self::SSTORAGEID, $value);
    }

    /**
     * Returns value of 'sStorageId' property
     *
     * @return string
     */
    public function getSStorageId()
    {
        return $this->get(self::SSTORAGEID);
    }

    /**
     * Sets value of 'spath' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSpath($value)
    {
        return $this->set(self::SPATH, $value);
    }

    /**
     * Returns value of 'spath' property
     *
     * @return string
     */
    public function getSpath()
    {
        return $this->get(self::SPATH);
    }

    /**
     * Sets value of 'nSysType' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNSysType($value)
    {
        return $this->set(self::NSYSTYPE, $value);
    }

    /**
     * Returns value of 'nSysType' property
     *
     * @return int
     */
    public function getNSysType()
    {
        return $this->get(self::NSYSTYPE);
    }
}

/**
 * P2W_Create_Ahdr_File_Ack message
 */
class Net_message_P2WCreateAhdrFileAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Delete_Ahdr_File message
 */
class Net_message_W2PDeleteAhdrFile extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const SSTORAGEID = 2;
    const SPATH = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::SSTORAGEID => array(
            'name' => 'sStorageId',
            'required' => true,
            'type' => 7,
        ),
        self::SPATH => array(
            'name' => 'spath',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::SSTORAGEID] = null;
        $this->values[self::SPATH] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'sStorageId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSStorageId($value)
    {
        return $this->set(self::SSTORAGEID, $value);
    }

    /**
     * Returns value of 'sStorageId' property
     *
     * @return string
     */
    public function getSStorageId()
    {
        return $this->get(self::SSTORAGEID);
    }

    /**
     * Sets value of 'spath' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSpath($value)
    {
        return $this->set(self::SPATH, $value);
    }

    /**
     * Returns value of 'spath' property
     *
     * @return string
     */
    public function getSpath()
    {
        return $this->get(self::SPATH);
    }
}

/**
 * P2W_Delete_Ahdr_File_Ack message
 */
class Net_message_P2WDeleteAhdrFileAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Stop_Migrate message
 */
class Net_message_W2PStopMigrate extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const MIGRATETASKID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::MIGRATETASKID => array(
            'default' => 0, 
            'name' => 'migrateTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::MIGRATETASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'migrateTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setMigrateTaskId($value)
    {
        return $this->set(self::MIGRATETASKID, $value);
    }

    /**
     * Returns value of 'migrateTaskId' property
     *
     * @return int
     */
    public function getMigrateTaskId()
    {
        return $this->get(self::MIGRATETASKID);
    }
}

/**
 * P2W_Stop_Migrate_Ack message
 */
class Net_message_P2WStopMigrateAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Stop_Recover message
 */
class Net_message_W2PStopRecover extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const RECOVERTASKID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::RECOVERTASKID => array(
            'default' => 0, 
            'name' => 'recoverTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::RECOVERTASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'recoverTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setRecoverTaskId($value)
    {
        return $this->set(self::RECOVERTASKID, $value);
    }

    /**
     * Returns value of 'recoverTaskId' property
     *
     * @return int
     */
    public function getRecoverTaskId()
    {
        return $this->get(self::RECOVERTASKID);
    }
}

/**
 * P2W_Stop_Recover_Ack message
 */
class Net_message_P2WStopRecoverAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Add_Libvirt_Computer message
 */
class Net_message_W2PAddLibvirtComputer extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const COMPUTERID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::COMPUTERID => array(
            'default' => 0, 
            'name' => 'computerId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::COMPUTERID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'computerId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setComputerId($value)
    {
        return $this->set(self::COMPUTERID, $value);
    }

    /**
     * Returns value of 'computerId' property
     *
     * @return int
     */
    public function getComputerId()
    {
        return $this->get(self::COMPUTERID);
    }
}

/**
 * P2W_Add_Libvirt_Computer_Ack message
 */
class Net_message_P2WAddLibvirtComputerAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Update_Libvirt_Computer_Info message
 */
class Net_message_W2PUpdateLibvirtComputerInfo extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const COMPUTERID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::COMPUTERID => array(
            'default' => 0, 
            'name' => 'computerId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::COMPUTERID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'computerId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setComputerId($value)
    {
        return $this->set(self::COMPUTERID, $value);
    }

    /**
     * Returns value of 'computerId' property
     *
     * @return int
     */
    public function getComputerId()
    {
        return $this->get(self::COMPUTERID);
    }
}

/**
 * P2W_Update_Libvirt_Computer_Info_Ack message
 */
class Net_message_P2WUpdateLibvirtComputerInfoAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Remote_Host message
 */
class Net_message_W2PRemoteHost extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const NTASKID = 2;
    const SIPS = 3;
    const NTYPE = 4;
    const SUNID = 5;
    const STRSERVERIP = 6;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::NTASKID => array(
            'name' => 'nTaskId',
            'required' => true,
            'type' => 5,
        ),
        self::SIPS => array(
            'name' => 'sips',
            'required' => true,
            'type' => 7,
        ),
        self::NTYPE => array(
            'name' => 'ntype',
            'required' => true,
            'type' => 5,
        ),
        self::SUNID => array(
            'name' => 'sUnId',
            'required' => true,
            'type' => 7,
        ),
        self::STRSERVERIP => array(
            'name' => 'strserverip',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::NTASKID] = null;
        $this->values[self::SIPS] = null;
        $this->values[self::NTYPE] = null;
        $this->values[self::SUNID] = null;
        $this->values[self::STRSERVERIP] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'nTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNTaskId($value)
    {
        return $this->set(self::NTASKID, $value);
    }

    /**
     * Returns value of 'nTaskId' property
     *
     * @return int
     */
    public function getNTaskId()
    {
        return $this->get(self::NTASKID);
    }

    /**
     * Sets value of 'sips' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSips($value)
    {
        return $this->set(self::SIPS, $value);
    }

    /**
     * Returns value of 'sips' property
     *
     * @return string
     */
    public function getSips()
    {
        return $this->get(self::SIPS);
    }

    /**
     * Sets value of 'ntype' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNtype($value)
    {
        return $this->set(self::NTYPE, $value);
    }

    /**
     * Returns value of 'ntype' property
     *
     * @return int
     */
    public function getNtype()
    {
        return $this->get(self::NTYPE);
    }

    /**
     * Sets value of 'sUnId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSUnId($value)
    {
        return $this->set(self::SUNID, $value);
    }

    /**
     * Returns value of 'sUnId' property
     *
     * @return string
     */
    public function getSUnId()
    {
        return $this->get(self::SUNID);
    }

    /**
     * Sets value of 'strserverip' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setStrserverip($value)
    {
        return $this->set(self::STRSERVERIP, $value);
    }

    /**
     * Returns value of 'strserverip' property
     *
     * @return string
     */
    public function getStrserverip()
    {
        return $this->get(self::STRSERVERIP);
    }
}

/**
 * P2W_Remote_Host_Ack message
 */
class Net_message_P2WRemoteHostAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Delete_Dir message
 */
class Net_message_W2PDeleteDir extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const NTASKID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::NTASKID => array(
            'name' => 'nTaskId',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::NTASKID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'nTaskId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setNTaskId($value)
    {
        return $this->set(self::NTASKID, $value);
    }

    /**
     * Returns value of 'nTaskId' property
     *
     * @return int
     */
    public function getNTaskId()
    {
        return $this->get(self::NTASKID);
    }
}

/**
 * P2W_Delete_Dir_Ack message
 */
class Net_message_P2WDeleteDirAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Config_Auto_Exercise_Strategy message
 */
class Net_message_W2PConfigAutoExerciseStrategy extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const ISSTRATEGYUSED = 2;
    const ISNEWSTRATEGY = 3;
    const VHOSTSOURCEIP = 4;
    const WEEKDAY = 5;
    const TIME = 6;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::ISSTRATEGYUSED => array(
            'default' => 0, 
            'name' => 'isStrategyUsed',
            'required' => true,
            'type' => 5,
        ),
        self::ISNEWSTRATEGY => array(
            'default' => 0, 
            'name' => 'isNewStrategy',
            'required' => true,
            'type' => 5,
        ),
        self::VHOSTSOURCEIP => array(
            'default' => '\"\"', 
            'name' => 'vHostSourceIP',
            'required' => true,
            'type' => 7,
        ),
        self::WEEKDAY => array(
            'default' => '\"\"', 
            'name' => 'weekday',
            'required' => true,
            'type' => 7,
        ),
        self::TIME => array(
            'default' => 0, 
            'name' => 'time',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::ISSTRATEGYUSED] = null;
        $this->values[self::ISNEWSTRATEGY] = null;
        $this->values[self::VHOSTSOURCEIP] = null;
        $this->values[self::WEEKDAY] = null;
        $this->values[self::TIME] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'isStrategyUsed' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setIsStrategyUsed($value)
    {
        return $this->set(self::ISSTRATEGYUSED, $value);
    }

    /**
     * Returns value of 'isStrategyUsed' property
     *
     * @return int
     */
    public function getIsStrategyUsed()
    {
        return $this->get(self::ISSTRATEGYUSED);
    }

    /**
     * Sets value of 'isNewStrategy' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setIsNewStrategy($value)
    {
        return $this->set(self::ISNEWSTRATEGY, $value);
    }

    /**
     * Returns value of 'isNewStrategy' property
     *
     * @return int
     */
    public function getIsNewStrategy()
    {
        return $this->get(self::ISNEWSTRATEGY);
    }

    /**
     * Sets value of 'vHostSourceIP' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setVHostSourceIP($value)
    {
        return $this->set(self::VHOSTSOURCEIP, $value);
    }

    /**
     * Returns value of 'vHostSourceIP' property
     *
     * @return string
     */
    public function getVHostSourceIP()
    {
        return $this->get(self::VHOSTSOURCEIP);
    }

    /**
     * Sets value of 'weekday' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setWeekday($value)
    {
        return $this->set(self::WEEKDAY, $value);
    }

    /**
     * Returns value of 'weekday' property
     *
     * @return string
     */
    public function getWeekday()
    {
        return $this->get(self::WEEKDAY);
    }

    /**
     * Sets value of 'time' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setTime($value)
    {
        return $this->set(self::TIME, $value);
    }

    /**
     * Returns value of 'time' property
     *
     * @return int
     */
    public function getTime()
    {
        return $this->get(self::TIME);
    }
}

/**
 * P2W_Config_Auto_Exercise_Strategy_Ack message
 */
class Net_message_P2WConfigAutoExerciseStrategyAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Create_Vhost_Finished message
 */
class Net_message_W2PCreateVhostFinished extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const VHOSTID = 2;
    const VHOSTNAME = 3;
    const VHOSTSOURCEIP = 4;
    const VHOSTSNAPID = 5;
    const VHOSTSTATUS = 6;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::VHOSTID => array(
            'default' => 0, 
            'name' => 'vHostId',
            'required' => true,
            'type' => 5,
        ),
        self::VHOSTNAME => array(
            'default' => '\"\"', 
            'name' => 'vHostName',
            'required' => true,
            'type' => 7,
        ),
        self::VHOSTSOURCEIP => array(
            'default' => '\"\"', 
            'name' => 'vHostSourceIP',
            'required' => true,
            'type' => 7,
        ),
        self::VHOSTSNAPID => array(
            'default' => 0, 
            'name' => 'vHostSnapId',
            'required' => true,
            'type' => 5,
        ),
        self::VHOSTSTATUS => array(
            'default' => 0, 
            'name' => 'vHostStatus',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::VHOSTID] = null;
        $this->values[self::VHOSTNAME] = null;
        $this->values[self::VHOSTSOURCEIP] = null;
        $this->values[self::VHOSTSNAPID] = null;
        $this->values[self::VHOSTSTATUS] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'vHostId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setVHostId($value)
    {
        return $this->set(self::VHOSTID, $value);
    }

    /**
     * Returns value of 'vHostId' property
     *
     * @return int
     */
    public function getVHostId()
    {
        return $this->get(self::VHOSTID);
    }

    /**
     * Sets value of 'vHostName' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setVHostName($value)
    {
        return $this->set(self::VHOSTNAME, $value);
    }

    /**
     * Returns value of 'vHostName' property
     *
     * @return string
     */
    public function getVHostName()
    {
        return $this->get(self::VHOSTNAME);
    }

    /**
     * Sets value of 'vHostSourceIP' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setVHostSourceIP($value)
    {
        return $this->set(self::VHOSTSOURCEIP, $value);
    }

    /**
     * Returns value of 'vHostSourceIP' property
     *
     * @return string
     */
    public function getVHostSourceIP()
    {
        return $this->get(self::VHOSTSOURCEIP);
    }

    /**
     * Sets value of 'vHostSnapId' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setVHostSnapId($value)
    {
        return $this->set(self::VHOSTSNAPID, $value);
    }

    /**
     * Returns value of 'vHostSnapId' property
     *
     * @return int
     */
    public function getVHostSnapId()
    {
        return $this->get(self::VHOSTSNAPID);
    }

    /**
     * Sets value of 'vHostStatus' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setVHostStatus($value)
    {
        return $this->set(self::VHOSTSTATUS, $value);
    }

    /**
     * Returns value of 'vHostStatus' property
     *
     * @return int
     */
    public function getVHostStatus()
    {
        return $this->get(self::VHOSTSTATUS);
    }
}

/**
 * P2W_Create_Vhost_Finished_Ack message
 */
class Net_message_P2WCreateVhostFinishedAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Destroy_Vhost_Finished message
 */
class Net_message_W2PDestroyVhostFinished extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const VHOSTNAME = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::VHOSTNAME => array(
            'default' => '\"\"', 
            'name' => 'vHostName',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::VHOSTNAME] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'vHostName' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setVHostName($value)
    {
        return $this->set(self::VHOSTNAME, $value);
    }

    /**
     * Returns value of 'vHostName' property
     *
     * @return string
     */
    public function getVHostName()
    {
        return $this->get(self::VHOSTNAME);
    }
}

/**
 * P2W_Destroy_Vhost_Finished_Ack message
 */
class Net_message_P2WDestroyVhostFinishedAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Update_Local_Machine_Hardware message
 */
class Net_message_W2PUpdateLocalMachineHardware extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }
}

/**
 * P2W_Update_Local_Machine_Hardware_Ack message
 */
class Net_message_P2WUpdateLocalMachineHardwareAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Clone_Syn message
 */
class Net_message_W2PCloneSyn extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const DEVICEUNIQUEID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::DEVICEUNIQUEID => array(
            'default' => '\"\"', 
            'name' => 'deviceUniqueId',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::DEVICEUNIQUEID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'deviceUniqueId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceUniqueId($value)
    {
        return $this->set(self::DEVICEUNIQUEID, $value);
    }

    /**
     * Returns value of 'deviceUniqueId' property
     *
     * @return string
     */
    public function getDeviceUniqueId()
    {
        return $this->get(self::DEVICEUNIQUEID);
    }
}

/**
 * P2W_Clone_Syn_Ack message
 */
class Net_message_P2WCloneSynAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Delete_File message
 */
class Net_message_W2PDeleteFile extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const SPATH = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::SPATH => array(
            'name' => 'spath',
            'required' => true,
            'type' => 7,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::SPATH] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'spath' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSpath($value)
    {
        return $this->set(self::SPATH, $value);
    }

    /**
     * Returns value of 'spath' property
     *
     * @return string
     */
    public function getSpath()
    {
        return $this->get(self::SPATH);
    }
}

/**
 * P2W_Delete_File_Ack message
 */
class Net_message_P2WDeleteFileAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Mount_Snap message
 */
class Net_message_W2PMountSnap extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const TASK_ID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::TASK_ID => array(
            'default' => 0, 
            'name' => 'task_id',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::TASK_ID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'task_id' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setTaskId($value)
    {
        return $this->set(self::TASK_ID, $value);
    }

    /**
     * Returns value of 'task_id' property
     *
     * @return int
     */
    public function getTaskId()
    {
        return $this->get(self::TASK_ID);
    }
}

/**
 * P2W_Mount_Snap_Ack message
 */
class Net_message_P2WMountSnapAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

/**
 * W2P_Umount_Snap message
 */
class Net_message_W2PUmountSnap extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const TASK_ID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::TASK_ID => array(
            'default' => 0, 
            'name' => 'task_id',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::TASK_ID] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'task_id' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setTaskId($value)
    {
        return $this->set(self::TASK_ID, $value);
    }

    /**
     * Returns value of 'task_id' property
     *
     * @return int
     */
    public function getTaskId()
    {
        return $this->get(self::TASK_ID);
    }
}

/**
 * P2W_Umount_Snap_Ack message
 */
class Net_message_P2WUmountSnapAck extends \ProtobufMessage
{
    /* Field index constants */
    const HEADER = 1;
    const CODE = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::HEADER => array(
            'name' => 'header',
            'required' => true,
            'type' => 'Net_message_PacketHeader'
        ),
        self::CODE => array(
            'default' => 0, 
            'name' => 'code',
            'required' => true,
            'type' => 5,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     *
     * @return null
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::HEADER] = null;
        $this->values[self::CODE] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'header' property
     *
     * @param Net_message_PacketHeader $value Property value
     *
     * @return null
     */
    public function setHeader(Net_message_PacketHeader $value)
    {
        return $this->set(self::HEADER, $value);
    }

    /**
     * Returns value of 'header' property
     *
     * @return Net_message_PacketHeader
     */
    public function getHeader()
    {
        return $this->get(self::HEADER);
    }

    /**
     * Sets value of 'code' property
     *
     * @param int $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return int
     */
    public function getCode()
    {
        return $this->get(self::CODE);
    }
}

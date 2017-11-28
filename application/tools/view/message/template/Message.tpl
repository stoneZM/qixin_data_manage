<?php

namespace Qmessage;

class [NAME] extends \DrSlump\Protobuf\Message
{

    public $double = null;
    public $float = null;
    public $int64 = null;
    public $uint64 = null;
    public $int32 = null;
    public $fixed64 = null;
    public $fixed32 = null;
    public $bool = null;
    public $string = null;
    public $bytes = null;
    public $uint32 = null;
    public $sfixed32 = null;
    public $sfixed64 = null;
    public $sint32 = null;
    public $sint64 = null;
    public $sName;
    public $nFrom;
    
[FIELDS]

	protected static $__extensions = array(); 
    
    public static function descriptor()
    {
    $descriptor = new \DrSlump\Protobuf\Descriptor(__CLASS__, 'tests.Simple');
    
[CLASS]

	foreach (self::$__extensions as $cb) { 
        $descriptor->addField($cb(), true);
    }
    return $descriptor;
    }

    public function hasDouble(){
      return $this->_has(1);
    }
    
    public function clearDouble(){
      return $this->_clear(1);
    }

    public function getDouble(){
      return $this->_get(1);
    }

    public function setDouble( $value){
      return $this->_set(1, $value);
    }

    public function hasFloat(){
      return $this->_has(2);
    }

    public function clearFloat(){
      return $this->_clear(2);
    }

    public function getFloat(){
      return $this->_get(2);
    }
    
    public function setFloat( $value){
      return $this->_set(2, $value);
    }

    public function hasInt64(){
      return $this->_has(3);
    }

    public function clearInt64(){
      return $this->_clear(3);
    }

    public function getInt64(){
      return $this->_get(3);
    }

    public function setInt64( $value){
      return $this->_set(3, $value);
    }
    
    public function hasUint64(){
      return $this->_has(4);
    }

    public function clearUint64(){
      return $this->_clear(4);
    }

    public function getUint64(){
      return $this->_get(4);
    }

    public function setUint64( $value){
      return $this->_set(4, $value);
    }
    
    public function hasInt32(){
      return $this->_has(5);
    }
    
    public function clearInt32(){
      return $this->_clear(5);
    }
    
    public function getInt32(){
      return $this->_get(5);
    }
    
    public function setInt32( $value){
      return $this->_set(5, $value);
    }
    
    public function hasFixed64(){
      return $this->_has(6);
    }
    
    public function clearFixed64(){
      return $this->_clear(6);
    }
    
    public function getFixed64(){
      return $this->_get(6);
    }
    
    public function setFixed64( $value){
      return $this->_set(6, $value);
    }
    
    public function hasFixed32(){
      return $this->_has(7);
    }
   
    public function clearFixed32(){
      return $this->_clear(7);
    }
    
    public function getFixed32(){
      return $this->_get(7);
    }

    public function setFixed32( $value){
      return $this->_set(7, $value);
    }

    public function hasBool(){
      return $this->_has(8);
    }

    public function clearBool(){
      return $this->_clear(8);
    }

    public function getBool(){
      return $this->_get(8);
    }

    public function setBool( $value){
      return $this->_set(8, $value);
    }

    public function hasString(){
      return $this->_has(9);
    }

    public function clearString(){
      return $this->_clear(9);
    }

    public function getString(){
      return $this->_get(9);
    }

    public function setString( $value){
      return $this->_set(9, $value);
    }

    public function hasBytes(){
      return $this->_has(12);
    }

    public function clearBytes(){
      return $this->_clear(12);
    }

    public function getBytes(){
      return $this->_get(12);
    }

    public function setBytes( $value){
      return $this->_set(12, $value);
    }

    public function hasUint32(){
      return $this->_has(13);
    }

    public function clearUint32(){
      return $this->_clear(13);
    }

    public function getUint32(){
      return $this->_get(13);
    }

    public function setUint32( $value){
      return $this->_set(13, $value);
    }

    public function hasSfixed32(){
      return $this->_has(15);
    }

    public function clearSfixed32(){
      return $this->_clear(15);
    }

    public function getSfixed32(){
      return $this->_get(15);
    }

    public function setSfixed32( $value){
      return $this->_set(15, $value);

    }

    public function hasSfixed64(){
      return $this->_has(16);
    }

    public function clearSfixed64(){
      return $this->_clear(16);
    }

    public function getSfixed64(){
      return $this->_get(16);
    }

    public function setSfixed64( $value){
      return $this->_set(16, $value);
    }

    public function hasSint32(){
      return $this->_has(17);
    }

    public function clearSint32(){
      return $this->_clear(17);
    }

    public function getSint32(){
      return $this->_get(17);
    }

    public function setSint32( $value){
      return $this->_set(17, $value);
    }

    public function hasSint64(){
      return $this->_has(18);
    }

    public function clearSint64(){
      return $this->_clear(18);
    }

    public function getSint64(){
      return $this->_get(18);
    }

    public function setSint64( $value){
      return $this->_set(18, $value);
    }
    
    public function getfields()
    {
		$Fields = $this->descriptor()->getFields();
		$Fields = objectToArray($Fields);
    	return $Fields;
    } 
    
    public function getdata()
    {
		$Fields = $this->descriptor()->getFields();
		$Fields = objectToArray($Fields);
		foreach($Fields as $dv){
			$re_data[$dv['name']] = $this->_get($dv['number']);
		}
    	return $re_data;
    }
    
[GETFIELD]
}
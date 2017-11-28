<?php

namespace app\device\model;

Class XmlMannager{

	private $dom_link;
	private $error;
    private $drive_node;
	public $newXML;

 	/**
 	 * 创建dom对象
 	 * @param string $xml 虚拟机的xml文件
 	 */
	public function __construct($xml){

		if (!$xml) {			
			$this->error = '读取xml文件失败';
			return flase;
		}
	$this->dom_link = new \DOMDocument('1.0', 'UTF-8');
    $this->dom_link->loadXML($xml);
 
     $this->drive_node = $this->dom_link->getElementsByTagName('devices');
     $this->drive_node = $this->drive_node->item(0);
	}
  

    /**
     * 增加磁盘操作
     * @param  [string] $filePath vmdk路径
     * @param  string $bus      [description]
     */
  public function addDisk($filePath,$bus='virtio'){

    	$nodeNames = array('disk','driver','source','target');
    	$nodeAttrs = array(
    			array(
    					'type'=>'file',
    					'device'=>'disk'
    				),
    			array(
    					'name'=>'qemu',
    					'type'=>'qcow2'
    				),
    			array(
    					'file'=>$filePath
    				),
    			array(
    					'dev'=>'sd'.$this->getDevChar(),
    					'bus'=>'virtio'
    				)
    		);

      $this->insertBefore($this->createNode($nodeNames,$nodeAttrs),'disk');
//      return  $this->dom_link->save('./data.xml');
//  		return $this->dom_link->saveXML();
	  $this->newXML = $this->dom_link->saveXML();
	  return $this->newXML;
  }


    private function getDevChar(){
    	$items = $this->drive_node -> getElementsByTagName('disk');
    	$devs = array();
  		foreach($items as $a){
          $tarEle = $a->getElementsByTagName('target');
          $tarEle = $tarEle->item(0);
        foreach($tarEle->attributes as $b){
          if ($b->nodeName == 'dev') {
            $devs[] = substr($b->nodeValue,-1,1);
            continue;
          }
        }
      }
      sort($devs);
      $lastChar = end($devs);
      return ++$lastChar;
}

    
   	 /**
   	  * 增加网卡
   	  * @param [type] $macAddr [description]
   	  * @param [type] $srcName [description]
   	  * @param string $model   [description]
   	  */
    public function addNetwork($macAddr,$srcName='default',$model='e1000'){

      $nodeNames = array('interface','mac','source','model');
      $nodeAttrs = array(
          array(
              'type'=>'network',
            ),
          array(
              'address'=>$macAddr,
            ),
          array(
              'network'=>$srcName,
            ),
          array(
              'type' => $model,
            ),
        );
      $this->insertBefore($this->createNode($nodeNames,$nodeAttrs),'interface');
//      return $this->dom_link->save('./data.xml');
	  $this->newXML = $this->dom_link->saveXML();
    }

    /**
     * 生成mac地址
     * @return [type] [description]
     */
    private function generateMacAddr(){

       return '52:54:00:28:a0:9e';
    }


    /**
     * 修改cpu
     * @param  [type] $cpu [description]
     * @return [type]      [description]
     */
    public function eidtCPU($cpu){
        $cpuNode = $this->getNode('vcpu');
        $cpuNode -> nodeValue = $cpu;
		$this->newXML = $this->dom_link->saveXML();
    }

  	/**
  	 * 修改内存
  	 * @param  [type] $memory [description]
  	 * @return [type]         [description]
  	 */
  	
  	public function editMem($memory){
        $memNode = $this->getNode('memory');
        $memNode->nodeValue = $memory;
        $curMemNode = $this->getNode('currentMemory');
        $curMemNode->nodeValue = $memory;

//        return ($this->dom_link->save('./data.xml')&&$this->dom_link->save('./data.xml'));
		$this->newXML = $this->dom_link->saveXML();
	}

    /**
     *  修改某个节点的信息
     */
    private function editNodeInfo($nodeName,$nodeAttrKey,$nodeAttrValue){

      $node = $this->getNode($nodeName);
      if (!$node) {
         $this->error = '节点信息不存在！';
         return false;
      }
      foreach ($node->attributes as $v) {
          if ($v==$nodeAttrKey) {
               $v->nodeValue = $nodeAttrValue;
          }
      }
//    return  $this->dom_link->save('./data.xml');
		$this->newXML = $this->dom_link->saveXML();
	}

  	/**
  	 * 更改主机模式：演练或者接管
  	 * @param  [type] $type [description]
  	 * @return [type]       [description]
  	 */
  	public function editNetwork($type,$macAddr=''){
		//如果mac地址为空，则将所有网卡类型改为一致
		if (!$macAddr) {
			$interface_nodes = $this->getNode('interface',false);
			$node_count = $interface_nodes->length;
			for ($i=0; $i < $node_count; $i++) {
				$node = $interface_nodes->item($i);
				$node->setAttribute('type','bridge');

				$source_node = $node->getElementsByTagName('source');
				$source_node = $source_node->item(0);

				foreach ($source_node->attributes as $v) {
					if ($v->nodeName == 'network') {
						$source_node->setAttribute('bridge',$type);
						$source_node->setAttribute('network','');
					}
				}
			}
		}else{

			//TODO::如果每个网卡类型不一样，就通过mac地址去区分

		}
		$this->newXML = $this->dom_link->saveXML();
  	}

    private function insertBefore($insertNode,$beforeNodeName){

       $node = $this->drive_node->getElementsByTagName($beforeNodeName);
       $node = $node->item(0);
       $this->drive_node->insertBefore($insertNode,$node);
    }

  	/**
  	 * 获取某个节点对象
  	 * @param  [type]  $nodeName [description]
  	 * @param  boolean $isSignle [description]
  	 * @return [type]            [description]
  	 */
  	protected function getNode($nodeName,$isSignle=true){

  		$items = $this->dom_link->getElementsByTagName($nodeName);
  		
  		if ($items->length<1) {
  			$this->error= $nodeName.'节点不存在！';
  			return false;
  		}
  		if ($isSignle) {
  			return $items->item(0);
  		}
  		return $items;
  	}

  	/**
  	 * 创建带有一级父子关系的节点
  	 * @param  [array] $nodeName [description]
  	 * @param  [array] $nodeAttr 二维数组
  	 */
  	protected function createNode($nodeNames,$nodeAttrs){

  		if (count($nodeNames)==0) {
  			$this->error='节点名称不能为空';
  			return false;
  		}
  		
  		$parentEle = $this->createElem($nodeNames[0],$nodeAttrs[0]);
  		unset($nodeNames[0]);
  		unset($nodeAttrs[0]);
  		
  		foreach ($nodeNames as $key => $value) {
  			$childEle = $this->createElem($value,$nodeAttrs[$key]);
  		 	$parentEle->appendChild($childEle);
  		 }
  		
  		 if ($parentEle){
  		 	return $parentEle;
  		 }else{
  		 	$this->error = '节点创建失败';
  		 	return false;
  		 }
  	}

  	/**
  	 * 创建一个元素
  	 * @param  [type] $elemName [description]
  	 * @param  [type] $elemAttr [description]
  	 * @return [type]           [description]
  	 */
  	protected function createElem($elemName,$elemAttr){
  		
  		if (!$elemName) {
  			$this->error='元素名称不能为空';
  			return false;
  		}
  		
  		$newElem = $this->dom_link->createElement($elemName);
  		
  		foreach ($elemAttr as $k => $v) {
  					$newElem->setAttribute($k,$v);			
  		}
  		
  		if (!$newElem) {
  			$this->error = '元素创建失败';
  			return false;
  		}
  		return $newElem;
  	}


    public function getError(){

      return $this->error;
    }

	public  function getNewXml(){
		return $this->newXML;
	}

}






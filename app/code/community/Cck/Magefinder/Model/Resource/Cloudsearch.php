<?php

class Cck_Magefinder_Model_Resource_Cloudsearch
{

    public function import($data) 
    {
		$client = $this->_getDocClient();
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'action' => 'update',
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        try {
            $client->setRawData(json_encode($data), "application/json");
            $response = $client->request("POST");
        } catch (Exception $e) {
            Mage::logException($e);
        }
	}
    
    public function truncate($storeId)
    {
		$client = $this->_getDocClient();
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'store' => $storeId,
            'action' => 'truncate',
            'version' => Mage::helper('magefinder')->getVersion(),
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        try {
            $response = $client->request();
//            Mage::log($response);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }
    
    public function delete($storeId, $productIds)
    {
		$client = $this->_getDocClient();
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'store' => $storeId,
            'action' => 'delete',
            'version' => Mage::helper('magefinder')->getVersion(),
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        try {
            $client->setRawData(json_encode($productIds), "application/json");
            $response = $client->request("POST");
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }
    
    public function query($queryText, $storeId)
    {
		$client = $this->_getSearchClient();
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'store' => $storeId,
            'q' => $queryText,
            'weight' => Mage::helper('magefinder')->getWeight(),
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        try {
            $response = $client->request();
        } catch (Exception $e) {
            Mage::logException($e);
            return array();
        }
        
        $data = array();
        if($response->getStatus() != 200) {
            return $data; 
        }
        $resultBody = json_decode($response->getBody());
        if($resultBody->found > 0) {
            foreach($resultBody->hits as $hit) {
                $data[] = (array)$hit;
            }
        }
        return $data;
    }
    
    protected function _getDocClient()
    {
        $url = "http://" . Mage::getStoreConfig('magefinder/advanced/doc_endpoint') 
                . "/doc.php";
		return new Zend_Http_Client($url);
    }
    
    protected function _getSearchClient()
    {
        $url = "http://" . Mage::getStoreConfig('magefinder/advanced/search_endpoint') 
                . "/search.php";
		return new Zend_Http_Client($url);
    }

}

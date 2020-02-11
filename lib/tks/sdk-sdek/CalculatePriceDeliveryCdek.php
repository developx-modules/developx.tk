<?php
/**
 * ������ ��������� �������� ����
 * ������ ��� ��������-��������� (��)
 * 
 * @version 1.0
 * @since 21.06.2012
 * @link http://www.edostavka.ru/integrator/
 * @see 3197
 * @author Tatyana Shurmeleva
 */
class CalculatePriceDeliveryCdek {
	
	//������ ������
	private $version = "1.0";
	//url ��� ��������� ������ �� ��������
    private $jsonUrl = 'http://api.cdek.ru/calculator/calculate_price_by_json_request.php';
		
	//����������� ��
	private $authLogin;
	private $authPassword;
	
	//id ������-�����������
	private $senderCityId;
	//id ������-����������
	private $receiverCityId;
	//id ������
	private $tariffId;
	//id ������� �������� (�����-�����, �����-�����)
	private $modeId;
	//������ ���� �����������
	public $goodsList;
	//������ id �������
	public $tariffList;
	//��������� ������� ��������� ����������� ��
	private $result;
    //��������� � ������ ���������� �������
    private $error;
	//����������� ���� ������
	public $dateExecute;
	
	/**
	 * �����������
	 */
	public function __construct() {
	     $this->dateExecute = date('Y-m-d');
	}	
	
	/**
	 * ��������� ����������� ���� ��������
	 * 
	 * @param string $date ���� ����������� ��������, �������� '2012-06-25'
	 */
	public function setDateExecute($date) {
		$this->dateExecute = date($date);
	}
	
	/**
	 * ����������� ��
	 * 
	 * @param string $authLogin �����
	 * @param string $authPassword ������
	 */
	public function setAuth($authLogin, $authPassword) {
		$this->authLogin = $authLogin;
		$this->authPassword = $authPassword;
	}

	/**
	 * ������������� ������ ��� �������� �� ������
	 * 
	 * @return string
	 */
	private function _getSecureAuthPassword() {
		return md5($this->dateExecute . '&' . $this->authPassword);
	}

	/**
	 * �����-�����������
	 * 
	 * @param int $id ������
	 */
	public function setSenderCityId($id) {
		$id = (int) $id;
		if($id == 0) {
			throw new Exception("����������� ����� �����-�����������.");
		}
		$this->senderCityId = $id;
	}	
	
	/**
	 * �����-����������
	 * 
	 * @param int $id ������
	 */
	public function setReceiverCityId($id) {
		$id = (int) $id;
		if($id == 0) {
			throw new Exception("����������� ����� �����-����������.");
		}		
		$this->receiverCityId = $id;
	}	

	/**
	 * ������������� �����
	 * 
	 * @param int $id ������
	 */
	public function setTariffId($id) {
		$id = (int) $id;
		if($id == 0) {
			throw new Exception("����������� ����� �����.");
		}		
		$this->tariffId = $id;
	}
	
	/**
	 * ������������� ����� �������� (�����-�����=1, �����-�����=2, �����-�����=3, �����-�����=4)
	 * 
	 * @param int $id ����� ��������
	 */
	public function setModeDeliveryId($id) {
		$id = (int) $id;
		if(!in_array($id, array(1,2,3,4))) {
			throw new Exception("����������� ����� ����� ��������.");
		}
		$this->modeId = $id;
	}
	
	/**
	 * ���������� ����� � ����������� 
	 * 
	 * @param int $weight ���, ����������
	 * @param int $length �����, ����������
	 * @param int $width ������, ����������
	 * @param int $height ������, ����������
	 */
	public function addGoodsItemBySize($weight, $length, $width, $height) {
		//�������� ����
		$weight = (float) $weight;
		if($weight == 0.00) {
			throw new Exception("����������� ����� ��� ����� � " . (count($this->getGoodslist())+1) . ".");
		}
		//�������� ��������� �������
		$paramsItem = array("�����" 	=> $length, 
							"������" 	=> $width, 
							"������" 	=> $height);
		foreach($paramsItem as $k=>$param) {
			$param = (int) $param;
			if($param==0) {
				throw new Exception("����������� ����� �������� '" . $k . "' ����� � " . (count($this->getGoodslist())+1) . ".");
			}
		}
		$this->goodsList[] = array( 'weight' 	=> $weight, 
									'length' 	=> $length,
									'width' 	=> $width,
									'height' 	=> $height);
	}

	/**
	 * ���������� ����� � ����������� �� ������ (���.�����)
	 * 
	 * @param int $weight ���, ����������
	 * @param int $volume �������� ���, ����� ���������� (� * � * �)
	 */
	public function addGoodsItemByVolume($weight, $volume) {
		$paramsItem = array("���" 			=> $weight, 
							"�������� ���" 	=> $volume);
		foreach($paramsItem as $k=>$param) {
			$param = (float) $param;
			if($param == 0.00) {
				throw new Exception("����������� ����� �������� '" . $k . "' ����� � " . (count($this->getGoodslist())+1) . ".");
			}
		}
		$this->goodsList[] = array( 'weight' 	=> $weight, 
									'volume'	=> $volume );
	}
	
	/**
	 * ��������� ������� ���� �����������
	 * 
	 * @return array
	 */
	public function getGoodslist() {
		if(!isset($this->goodsList)) {
			return NULL;
		}
		return $this->goodsList;
	}
	
	/**
	 * ���������� ������ � ������ ������� � ������������
	 * 
	 * @param int $id �����
	 * @param int $priority default false ���������
	 */
	public function addTariffPriority($id, $priority = 0) {
		$id = (int) $id;
		if($id == 0) {
			throw new Exception("����������� ����� id ������.");
		}
        $priority = ($priority > 0) ? $priority : count($this->tariffList)+1;
		$this->tariffList[] = array( 'priority' => $priority,
									 'id' 		=> $id);
	}
	
	/**
	 * ��������� ������� �������� �������
	 * 
	 * @return array
	 */
	private function _getTariffList() {
		if(!isset($this->tariffList)) {
			return NULL;
		}
		return $this->tariffList;
	}

	/**
	 * ���������� POST-������� �� ������ ��� ��������� ������
	 * �� ������������� ����������.
	 * 
	 * 
	 */
	private function _getRemoteData($data) {
		    
        $bodyData = array (
          'json' => json_encode($data)
        );
        $data_string = http_build_query($bodyData);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->jsonUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //��� ������� ���������������� � ����������� ���� errorlog.txt
        //$fp = fopen(dirname(__FILE__).'/errorlog.txt', 'w');
        //curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //curl_setopt($ch, CURLOPT_STDERR, $fp); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: '.strlen($data_string)
            ) 
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		
		$result = curl_exec($ch); 
		curl_close($ch); 
		
		return json_decode($result, true);
	}
	
	/**
	 * ������ ��������� ��������
	 * 
	 * @return bool
	 */
	public function calculate() {
		//��������� ������ ��� �������� curl-post-�������
		//������� ������ ���� �������� ���������, ������������� ��
		//��� �������� � ��������� ����� ������ �� ������� �������
		$data = array();
		//��������� ���� ������� �������� ������� �� ��������, �.�. � ��� �������� private
		//������� ��������� ������ $data ����
		//��������� �� ������������� ���������� � ��-NULL-��������
		
		//������ ������
		isset($this->version) ? $data['version'] = $this->version : '';
		//���� ����������� ��������, ���� �� �����������, ������ ����������� ����
		$data['dateExecute'] = $this->dateExecute;
		//�����������: �����
		isset($this->authLogin) ? $data['authLogin'] = $this->authLogin : '';
		//�����������: ������
		isset($this->authPassword) ? $data['secure'] = $this->_getSecureAuthPassword() : '';
		//�����-�����������
		isset($this->senderCityId) ? $data['senderCityId'] = $this->senderCityId : '';
		//�����-����������
		isset($this->receiverCityId) ? $data['receiverCityId'] = $this->receiverCityId : '';
		//��������� �����
		isset($this->tariffId) ? $data['tariffId'] = $this->tariffId : '';
		//������ ������� � ������������
		( isset($this->tariffList)  ) ? $data['tariffList'] = $this->tariffList : '';
		//����� ��������
		isset($this->modeId) ? $data['modeId'] = $this->modeId : '';
		
		//������ ����
		if( isset($this->goodsList) ) {
			foreach ($this->goodsList as $idGoods => $goods) {
				$data['goods'][$idGoods] = array();
				//���
				(isset($goods['weight']) && $goods['weight'] <> '' && $goods['weight'] > 0.00) ? $data['goods'][$idGoods]['weight'] = $goods['weight'] : '';
				//�����
				(isset($goods['length']) && $goods['length'] <> '' && $goods['length'] > 0) ? $data['goods'][$idGoods]['length'] = $goods['length'] : '';
				//������
				(isset($goods['width']) && $goods['width'] <> '' && $goods['width'] > 0) ? $data['goods'][$idGoods]['width'] = $goods['width'] : '';
				//������
				(isset($goods['height']) && $goods['height'] <> '' && $goods['height'] > 0) ? $data['goods'][$idGoods]['height'] = $goods['height'] : '';
				//�������� ��� (���.�)
				(isset($goods['volume']) && $goods['volume'] <> '' && $goods['volume'] > 0.00) ? $data['goods'][$idGoods]['volume'] = $goods['volume'] : '';

			}
		}
		//�������� �� ���������� ���������� curl
		if(!extension_loaded('curl')) {
			throw new Exception("�� ���������� ���������� CURL");
		}		
		$response = $this->_getRemoteData($data);
        
        if( isset($response['result']) && !empty($response['result']) ) {
            $this->result = $response;
            return true;
        } else {
            $this->error = $response;
            return false;
        }
        
		//return (isset($response['result']) && (!empty($response['result']))) ? true : false;
		//���������
		//$result = ($this->getResponse());
		//return $result;
	}
	
	/**
	 * �������� ���������� ��������
	 * 
	 * @return array
	 */
	public function getResult() {
		return $this->result;
	}
	
	/**
	 * �������� ��� � ����� ������
	 * 
	 * @return object
	 */
	public function getError() {
		return $this->error;
	}
	
}

?>
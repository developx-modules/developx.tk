<!DOCTYPE html>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>����������� ������ ������������� SDK</title>
	</head>
<body>
<pre>
<?php	
	// ����������� ����� � �������
	require_once('pecom_kabinet.php');

	// �������� ���������� ������
	$sdk = new PecomKabinet('user', 'FA218354B83DB72D3261FA80BA309D5454ADC');

	// ����� ������
	$result = $sdk->call('cargos', 'status', 
		array('cargoCodes' => array(
			'�������-1/1603',
			'�������-3/0103',		
			'�������-4/2903',
		)
	));
	
	// ����� ����������
	var_dump($result);
	
	// ������������ ��������
	$sdk->close();
?>
</pre>
</body>
</html>
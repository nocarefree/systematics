<?php 


return [
	'connections' => [
		'mysql' =>
		[
			'table_prefix' => 'systematics_',
			'table_split'  => '/', 
			'table_name'   => [
				'relations' => 'relations',
				'types' =>'types',
			],	
		]	
	],
];
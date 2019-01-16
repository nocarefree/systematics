<?php 


return [
	'connections' => [
		'mysql' =>
		[
			'table_prefix' => 'systematics_',
			'table_split'  => '/', 
			'table'   => [
				'relations' => [
					'name' => 'relations',
				],
				'types' => [
					'name' => 'types',
				]
			],	
		]	
	],
];
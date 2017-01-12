<?php
 return array(
		'URL_MODEL' => 0,
	    'URL_CASE_INSENSITIVE'  =>  false,
	    'VAR_PAGE'=>'p',
	    'PAGE_SIZE'=>15,
		'DB_TYPE'=>'mysql',
	    'DB_HOST'=>'localhost',
	    'DB_NAME'=>'jianxiu',
	    'DB_USER'=>'root',
	    'DB_PWD'=>'root',
	    'DB_PREFIX'=>'wst_',
	    'DEFAULT_C_LAYER' =>'Action',
	    'DEFAULT_CITY' => '440100',
	    'DATA_CACHE_SUBDIR'=>true,
        'DATA_PATH_LEVEL'=>2, 
	    'SESSION_PREFIX' => 'zixianjianxiu',
        'COOKIE_PREFIX'  => 'zixianjianxiu',
		'MODULE_ALLOW_LIST'    => array('Home','Admin','User','Api'),
		'DEFAULT_MODULE'       =>'Api',
		'LOAD_EXT_CONFIG' => 'wst_config',
 		'DB_DEBUG'  => '', // 数据库调试模式 开启后可以记录SQL日志
 		
	);
?>
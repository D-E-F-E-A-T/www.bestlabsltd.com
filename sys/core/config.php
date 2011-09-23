<?php

return array(

'core' => array(
	'debug'			=> true,
	'error'			=> true,
#	'route_error'	=> 'error',
#	'uri_sufix'		=> '.html',
	# ALLOWED MIME-TYPES
	'mime-types'	=> array(
		'css'	=> 'text/css',
		'js'	=> 'application/javascript',
		'jpeg'  => 'image/jpeg',
		'jpg'	=> 'image/jpg',
		'png'	=> 'image/png',
		'gif'	=> 'image/gif',
		'eot'	=> 'application/vnd.ms-fontobject',
		'otf'	=> 'font/otf',
		'ttf'	=> 'font/ttf',
		'svg'	=> 'image/svg+xml',
		'woff'	=> 'application/octet-stream'
	),

	# LANGUAGES
	# first language is default.
	'language'  => array(
		'es-mx' => array('es', 'Español', 'Español México'),
		'en-us' => array('en', 'English', 'American English')
	)
),

'application' 	=> array(
	# DEFAULT APPLICATION 
	'default'		=> 'main',

	# ALLOWED URI CHARS
	'safe_chars'	=> 'a-zA-Z0-9~%.:_-',

	# APPLICATION ROUTING
	# Refer to [http://php.net/manual/en/function.preg-replace.php]
	# you MUST specify a delimiter ie "/ /", otherwise you'll get an error.
	# example: /(en|es)/ => main/$1
	'routes'		=> array(
		# if an uppercase is sent, 404
		'/[A-Z]/'   => 'notfound', 
		# if it starts with something  differnt than es | en | consola : 404
		'%^(?!consola)([^e]|e(?!s|n)){1,2}.*$%' => 'notfound',
		# if the section has anything different from a-z\- : 404
		'%^.{2}/[^/]*[^a-z\-/]/?%' => 'notfound',
		# everything to main
		'%^(?!consola)(.{2})(/[^/]+)?(/[^/]+)?/?%' => 'main/$1$2$3'
	),

	# DEFAULT HTML TEMPLATE
	'template'      => 'html5',

	# SCOPE EXPIRING DATE
	# time until the Core database consider a scope expired
	# use date var here. ie: date('d')+1 = next day.
	'scope_length'	=> mktime(
		date('H'),	# Hours
		date('i')+3,# Minutes
		date('s'),	# Seconds
		date("m"),	# Month
		date("d"),	# Day
		date("Y")	# Year
	),

	# ALLOWED EXTERNAL APPLICATIONS
	# this type of files will be translated from PUB_URL to APP_PATH folder
	# -	the type must be declared on 'core'=>'mimetypes'
	# - Allowing php or html files, it's a very bad idea, unless you're a ninja.
	'external_allow'=> array('css','js'),

	# Auto adds tags for css & js [if they exist]
	'tags_auto'     => true,

	# default position for js scripts. 
	'tags_posjs' 	=> 'end',

	# if zlib enabled, use this compression level.
	'compression'   => 0
),

'utils' => array(

	'cryptor_secret' => 'h=)(7012'

),

'auth' => array(
	'admin_user' => 'admin',
	'admin_pass' => 'palmera'
)

);
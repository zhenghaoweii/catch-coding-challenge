# Catch Coding Challenge 1

Challenge completed by : Heriyanto | +6285668341706 | Heriyanto293@gmail.com

## Architecture
The way I organize the code is using pipeline (league/pipeline).
and collect (tightenco/collect) for working with arrays of data more convenient.
the purpose is to make the code more readable and let other user understand the flow easier.

## Bootstrap
`git clone https://github.com/zhenghaoweii/catch-coding-challenge.git`

`composer install`

note : need to manually setup the default .env


## Running the unit test
`	php ./vendor/bin/phpunit`



## Running the application
 Export the output to csv 

`php bin/console catch:export-orders`
	
options :	
		--type : `jsonl | xml,yaml,csv,xlsx`							
		--email : `specify_email_system_should_send`
		
e.g :
`	php bin/console catch:export-orders --type=json --email=heriyanto293@gmail.com`

Note : I am using mailtrap for the email receiver
	
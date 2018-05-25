# EveStructures
This provides a consolidated view of all your structures across multiple Eve Online Corporations. Gathers and displays important data like fuel, services, locations, states and much more.

Join Our [Discord!](https://discord.gg/5vUQxMP)  

EveStrucutures is built with the Laravel PHP Framework. For more info on [Laravel](https://laravel.com/docs/5.5)

## Donate
If you liked EveStructures, please consider donating ISK in game to [Brock Khans](https://evewho.com/pilot/Brock+Khans) or [PayPal](paypal.me/skiedude)

## Prequisite Installs
* [php] >= 7.0.0
* mysql or mariadb - Download for your OS (I've found mariadb runs lighter on the memory)
* [composer](https://getcomposer.org/doc/00-intro.md) - Used for installing Laravel, and other updates
* [laravel requirements](https://laravel.com/docs/5.5) - Check page
* [apache/httpd] - Whatever your OS supports

### Eve Developers Site
[Eve Online Developer](https://developers.eveonline.com/) - You will need to setup your own developer application that uses
* esi-universe.read_structures.v1 
* esi-corporations.read_structures.v1 
* esi-characters.read_corporation_roles.v1  
* esi-industry.read_corporation_mining.v1  
Set the callback url to 
```
http(s)://your_domain.com/sso/callback
```
More info can be found [ESI Docs](http://eveonline-third-party-documentation.readthedocs.io/en/latest/esi/index.html)

## Install
### Databases
We need 1 database created.
```
create database evestructures
```
Create one user and give it privileges. 

### Git Repo
Pull down the repo and Run (I made composer a global binary by following [binary](https://getcomposer.org/doc/00-intro.md#globally) )
```
composer install
```

### Environment File
We need to update/add some values to the .env file. (If one is not created, copy the .env.example to be .env
Update the following in the .env file (add if missing) REMOVE THE //COMMENTS
```
APP_NAME=  //used in the emails
APP_ENV=prod  //use prod
APP_URL= //url of your website for the emails
SITE_NAME= //used in disclaimer

DB_DATABASE= // database you created 
DB_USERNAME= // username that has access to both databases
DB_PASSWORD= // password for ^ user
QUEUE_DRIVER= //use database if you intend to use supervisord

MAIL_* // based off what mail setup you wish to use

USERAGENT= //used for the HTTP requests
CALLBACK_URL=https://URL_GOES_HERE.COM/sso/callback  //update the URL there in the middle, adjust for non https
CLIENT_ID=  //retrieved from your developer account
SECRET_KEY= // retrieved from your developer account
```

### Web Service
I won't go in depth on how to configure apache for each OS type. But you need to point the home directory to the public folder of your installation. *Make Sure you update the paths to files in these to your install*  
Here is an example httpd conf file (CentOS7) https://pastebin.com/F71CCb1e  
Here is an example nginx conf file (CentOS7) https://pastebin.com/7xy5dtJn


### Database Migrations
Run the php artisan migration command to set create your database tables
```
php artisan migrate
```

### Logs
Laravel requires special permissions on the the sub folders in storage, you can set everything to 777, but I'd warn against that. I wrote a bash alias that fixes this for me. You will need to adjust this based off the user your web service runs under.
https://stackoverflow.com/a/37266353 . I put this as a cron for the root user to run as * * * * *
```
alias fixstorage='sudo chgrp -R USER_HERE storage bootstrap/cache && sudo chmod -R ug+rwx storage bootstrap/cache'
```
### Supervisord
Supervisord takes care of running the jobs as they enter the queue.
[Supervisord Setup Instructions](https://laravel.com/docs/5.5/queues#supervisor-configuration).    
For Centos7 my files in conf.d needed to be .ini  
Example config that I use
```
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /UPDATE_PATH_TO_INSTALL/artisan queue:work --sleep=3 --tries=3 --timeout=10
autostart=true
autorestart=true
user=UPDATE_TO_YOUR_USER
numprocs=2
redirect_stderr=true
stdout_logfile=/UPDATE_PATH_TO_INSTALL/worker.log

```

### Cron
Updating Structures every 3 hours, and checking for Fuel Notifications to send are run via the Schedule feature of Laravel. This requires running a cron once a minute to see if there are any tasks to schedule (this also schedules the jobs that get passed to supervisord).

Create a cron with the following entry
[Laravel Scheduler](https://laravel.com/docs/5.5/scheduling#introduction)
```
* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
```

From here you should be able to hit your Website in the browser, and play around with it.

### Commands
Custom Commands (Some of these already run on a schedule, but can be ran manually):
```
php artisan update:structures //Kick off a Job for each Character to update their structure data from ESI
php artisan check:fuel //Run the Fuel check and send notifications if required
php artisan check:orphans //Checks for structures with no matching characters of the same corporation_id (cleans up old data)
php artisan check:unanchor //Checks for structures unanchoring and sends notifications if its the right time left
php artisan extraction:daily //Sends a message for the extractions coming up in the next 7 days
php artisan strct:state {structure_id} {old_state} {new_state} //Sends a notification that the State of a Structure changed
```

### Private Hosting
To hide some of the more public features for private installs you can add the following variables to the .env:  
```
Hides the Tools drop down in the menu
PRIVATE_INSTALL=1

Hides the register link (still works if manually navigated to)
DISABLE_REGISTER=1
```

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details



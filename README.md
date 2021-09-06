#Slotegrator test app
Php app based on Yii2 framework, which allows an authorized user to win a random prize.

There are 3 types of prizes:
- Money, random amount, limited, transfer both to account points using multiplier or to bank directly via HTTP request 
- Account points, random amount, non-limited, transfer to user account
- Physical item, limited, shipped by post via email notifications to staff

####Sign up and win your prize!

###Installation
- clone repo
- run ``composer install``
- provide environment in .env.local (need to create one from example .env)
- run ``./yii migrate``
- add some item type prizes to database (you can do it via console command as well)
- run ``./yii serve`` or use your local web server (webroot is web/)
- open app in browser

###Usage
####Web
First you need to sign up, then press the "get the prize" button. Follow further app instructions.
####Console
- ``./yii prize/add-prize "{title}" "{description}" {status}`` use it to add an item prize
- ``./yii prize/change-prize {id} "{attribute}" "{value}"`` use it to change attribute of the item prize
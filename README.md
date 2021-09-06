# Slotegrator test app

Php app based on Yii2 framework, which allows an authorized user to win a random prize.

There are 3 types of prizes:
- Money, random amount, limited, transfer both to account points using multiplier or to bank directly via HTTP request 
- Account points, random amount, non-limited, transfer to user account
- Physical item, limited, shipped by post via email notifications to staff

All bank account transfer are made via queue. (database provider)

#### Sign up and win your prize!

### Installation

- clone repo
- run ``composer install``
- provide environment in .env.local (need to create one from example .env)
- run ``./yii migrate``
- add some item type prizes to database (you can do it via console command as well)
- run ``./yii serve`` or use your local web server (webroot is web/)
- open app in browser

### Usage

#### Web

First you need to sign up, then press the "get the prize" button. Follow further app instructions.

#### Console

- ``./yii prize/add-prize "{title}" "{description}" {status}`` use it to add an item prize
- ``./yii prize/change-prize {id} "{attribute}" "{value}"`` use it to change attribute of the item prize


Console commands are used to execute and manage queued jobs.

``yii queue/listen [timeout]``

The listen command launches a daemon which infinitely queries the queue. 
If there are new tasks they're immediately obtained and executed. 
The timeout parameter specifies the number of seconds to sleep between querying the queue.
This method is most efficient when the command is properly daemonized via supervisor or systemd.

``yii queue/run``

The run command obtains and executes tasks in a loop until the queue is empty. This works well with ``cron``.

The run and listen commands have options:

    --verbose, -v: print execution statuses to console.
    --isolate: each task is executed in a separate child process.
    --color: enable highlighting for verbose mode.

``yii queue/info``

The info command prints out information about the queue status.

``yii queue/clear``

The clear command clears the queue.

``yii queue/remove [id]``

The remove command removes a job from the queue.

### Tests

Run ``./vendor/bin/codecept run`` 

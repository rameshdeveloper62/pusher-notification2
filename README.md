## Laravel queue

(1) first configure queue type : redis,database,sync,beanstalkd,sqs
	default sync type is set in .env file

(2) if you use database queue type so you can run below command
		
	change of queue type in .env file sync to database


	database driver:
	
	This driver stores queued jobs in the database. Before enabling this driver, you will need to create database tables to store your queued and failed jobs:

	**php artisan queue:table** for store job record
	**php artisan queue:failed-table** for store failed job record
	**php artisan migrate**

	sync driver:

	Sync, or synchronous, is the default queue driver which runs a queued job within your existing process. With this driver enabled, you effectively have no queue as the queued job runs immediately. This is useful for local or testing purposes

	sqs driver:

	This queue driver uses Amazon's Simple Queue Service to manage queued jobs. Before enabling this job you must install the following composer package **aws/aws-sdk-php ~3.0**

	iron driver:
	This queue drivers uses Iron to manage queued jobs.

	redis driver:

	This queue driver uses an instance of Redis to manage queued jobs. Before using this queue driver, you will need to configure a copy of Redis and install the following composer dependency: predis/predis ~1.0

	beanstalkd driver:

	This queue driver uses an instance of Beanstalk to manage queued jobs. Before using this queue driver, you will need to configure a copy of Beanstalk and install the following composer dependency: pda/pheanstalk ~3.0



(3) if you use redis queue type so you need to install redis server in your 	laravel by using below command 
	
	change of queue type in .env file sync to redis
	
	**composer require predis/predis**

	-install redis in your system

	- for start redis server
		/etc/init.d/redis-server start
	- for stop redis server
		/etc/init.d/redis-server stop
	- check redis server start or not in your system
		redis-cli ping
		 Output: PONG

(4) create mail
	
	-for create new file in your app\Mail\SendEmail.php
	**php artisan make:mail SendEmail**

	- create email template in your resources/email/email.blade.php file

(6)  create job
	
	-for create new file in your app\Jobs\EmailJob.php
	**php artisan make:job EmailJob**

(7) In app\Http\Controllers\HomeController.php for Dispatch job
	
	**EmailJob::dispatch($data);**

	public function sendEmail(Request $request)
    {
        $data=[
            'subject'=>'demo for send email by job',
            'message'=>'queue and job demo for send email.'
        ];

        EmailJob::dispatch($data);
        return redirect('home');
    }
(8) In app\Jobs\EmailJob.php

	- we can send email by handle method in EmailJob

		public function handle()
    	{
        	Log::info("send email");
        	Mail::to(env('EMAIL'), env('NAME'))->send(new SendEmail($this->data));
    	}

(9) In app\Mail\SendEmail.php
	
	we set email template with require data

	public function build()
    {
        return $this->view('emails.email',['data'=>$this->data]);
    }


 (10) we need to run job by cron job
 	
 	-for specific queue
 	
 	 $schedule->job(new EmailJob)->everyFiveMinutes();

    - for all queue
    
    	$schedule->command('queue:work')->everyMinute();

  (11) you must set cron job in your server
  	
  	In your terminal run below command 
  	
  	-crontab -l for list of cron job

  	-cronjob -e for add or edit new cron job

  	-your laravel project path (queue is my laravel folder name)

  	* * * * * php /var/www/html/queue/artisan schedule:run >> /dev/null 2>&1

	(12)  Queue method and property

  	* Job Queue property and method 
	-Method
		retryUntil()
		handle()
		failed()

	* Running The Queue Worker
	
		php artisan queue:work 

	 	queue workers are long-lived processes and store the booted application state in memory. As a result, they will not notice changes in your code base after they have been started. So, during your deployment process, be sure to restart your queue Workers

	* Running The Queue listen

		php artisan queue:listen

		Alternatively, you may run the queue:listen command. When using the queue:listen command, you don't have to manually restart the worker after your code is changed.

	* Processing A Single Job

		php artisan queue:work --once

	* Processing All Queued Jobs & Then Exiting
	
		php artisan queue:work --stop-when-empty

	* Specifying The Connection & Queue
 
	 	php artisan queue:work database --queue=processing

		you can set queue_driver from .evn file

		default is type of default queue.

		if you set custom queue onQueue('processing') then you should run php artisan queue:work --queue=processing

	* Queue Priorities
		php artisan queue:work --queue=processing,low
		
		dispatch((new OrderStatus)->onQueue('processing'));

	* Queue Workers & Deployment

		php artisan queue:restart


	* Worker Timeouts
		php artisan queue:work --timeout=60

	* Worker Sleep Duration

		php artisan queue:work --sleep=3

	* Retrying Failed Jobs
		
		php artisan queue:failed
		php artisan queue:retry 5
		php artisan queue:retry all
		php artisan queue:forget 5
		php artisan queue:flush
	```
	namespace Illuminate\Bus;

	trait Queueable
	{
	    /**
	     * The name of the connection the job should be sent to.
	     *
	     * @var string|null
	     */
	    public $connection;
	 
	    /**
	     * The name of the queue the job should be sent to.
	     *
	     * @var string|null
	     */
	    public $queue;

	    /**
	     * The name of the connection the chain should be sent to.
	     *
	     * @var string|null
	     */
	    public $chainConnection;

	    /**
	     * The name of the queue the chain should be sent to.
	     *
	     * @var string|null
	     */
	    public $chainQueue;

	    /**
	     * The number of seconds before the job should be made available.
	     *
	     * @var \DateTimeInterface|\DateInterval|int|null
	     */
	    public $delay;

	    /**
	     * The jobs that should run if this job is successful.
	     *
	     * @var array
	     */
	    public $chained = [];

	    /**
	     * Set the desired connection for the job.
	     *
	     * @param  string|null  $connection
	     * @return $this
	     */
	    public function onConnection($connection)
	    {
	        $this->connection = $connection;

	        return $this;
	    }

	    /**
	     * Set the desired queue for the job.
	     *
	     * @param  string|null  $queue
	     * @return $this
	     */
	    public function onQueue($queue)
	    {
	        $this->queue = $queue;

	        return $this;
	    }

	    /**
	     * Set the desired connection for the chain.
	     *
	     * @param  string|null  $connection
	     * @return $this
	     */
	    public function allOnConnection($connection)
	    {
	        $this->chainConnection = $connection;
	        $this->connection = $connection;

	        return $this;
	    }

	    /**
	     * Set the desired queue for the chain.
	     *
	     * @param  string|null  $queue
	     * @return $this
	     */
	    public function allOnQueue($queue)
	    {
	        $this->chainQueue = $queue;
	        $this->queue = $queue;

	        return $this;
	    }

	    /**
	     * Set the desired delay for the job.
	     *
	     * @param  \DateTimeInterface|\DateInterval|int|null  $delay
	     * @return $this
	     */
	    public function delay($delay)
	    {
	        $this->delay = $delay;

	        return $this;
	    }

	    /**
	     * Set the jobs that should run if this job is successful.
	     *
	     * @param  array  $chain
	     * @return $this
	     */
	    public function chain($chain)
	    {
	        $this->chained = collect($chain)->map(function ($job) {
	            return serialize($job);
	        })->all();

	        return $this;
	    }

	    /**
	     * Dispatch the next job on the chain.
	     *
	     * @return void
	     */
	    public function dispatchNextJobInChain()
	    {
	        if (! empty($this->chained)) {
	            dispatch(tap(unserialize(array_shift($this->chained)), function ($next) {
	                $next->chained = $this->chained;

	                $next->onConnection($next->connection ?: $this->chainConnection);
	                $next->onQueue($next->queue ?: $this->chainQueue);

	                $next->chainConnection = $this->chainConnection;
	                $next->chainQueue = $this->chainQueue;
	            }));
	        }
	    }
	}
	```





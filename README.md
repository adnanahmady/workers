# Workers
a useful php worker framework based on callback objects.

## Running workers
Application Runs on a base command on base directory `php listen queue:myqueue`,
this command runs php worker and tell the worker to listen to `myqueue` and sub queue `order` 
now your worker is listening to rabbitmq, see if there is some work at the queue and take it.
 
## Sub queue types
there is four default types of sub queues `priority, order, success, fails`.

    # hightest order
    priority 
    # normal order
    order
    # if there is need to put success operations on rabbit current queue
    success
    # if there is need to put failed operations on rabbit current queue
    fails
    
sub queues seperate of queue with `.` sign like `myqueue.order` .

but if you have some other one in your mind, 
with this command you can tell worker to listen to that one like:

    php listen queue:myqueue sub:mysubqueue

## Set timer on worker
for setting timer on a worker you must add two more arguments `start` and `stop`.
these arguments gets a time with `H:i:s` format this means `00:00:00` for midnight 
and `01:30:00` for one hour and a half without seconds 
and `01:30:24` for one hour and a half and 24 seconds.

to set timer for worker you must run a command like this:

    php listen queue:myqueue start:01:00:00 stop:11:00:00

### Be careful
be careful with using times. because of these reasons:
1) stop time must be greater than start time that 
    means you can not set start time to `23:00:00`
    and set stop time to `01:00:00`
2) if worker gets a task out of time that is set worker
    will send task to fails sub queue of current queue
    
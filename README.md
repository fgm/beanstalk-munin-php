# Beanstalk-Munin-PHP

A few simple Munin plugins to monitor beanstalkd.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/FGM/beanstalk-munin-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/FGM/beanstalk-munin-php/?branch=master)

Requires PHP >= 7.4, Composer + Pheanstalk 4. APLv2 License.

List tubes to monitor using the `TUBES` variable in Munin's plugin conf.

It is basically a PHP port of the original Urban Airship plugins written in Python.


## Usage configuration

The plugins can take the server configuration from the environment:

- `BEANSTALKD_HOST`: defines the hostname for the Beanstalkd server, defaulting to `localhost`.
- `BEANSTALKD_PORT`: defines the TCP port for the Beanstalkd server, defaulting to 11300.
- `BEANSTALKD_TIMEOUT`: defines the connection timeout in seconds, defaulting to 10.
- `BEANSTALKD_TUBES`: a space-separated list of tubes to monitor, defaulting to `default`. Only used by the Queue Age plugin.


## Original Python version

https://github.com/urbanairship/beanstalk-munin


## Suggested Beanstalkd UI

https://xuri.me/aurora/

# Install the Munin plugins for Beanstalkd

## Requirements

* PHP > 5.4, including 7.0 to 7.2
* Composer (see https://getcomposer.org/download/ )
* Munin 2.x
  * Ensure Munin itself is already operational by visiting `http://(site)/munin`


## Deployment on Debian / Ubuntu

Download the plugins by cloning the repository

```bash
$ cd /opt
$ sudo git clone https://github.com/fgm/beanstalk-munin-php.git
$ cd beanstalk-munin-php
$ sudo chown -R $(whoami):www-data .
$
```

Now install the autoloaded dependencies.

```bash
$ composer install
$
```

At this point, the plugins are available. Check them manually, by doing e.g.:

```bash
$ php plugins/bs_cmd_rate.php
cmd_put.value 0
cmd_reserve.value 0
cmd_reserve_timeout.value 0
cmd_delete.value 0
cmd_touch.value 0
cmd_release.value 0
cmd_bury.value 0
$ 
```

If your Beanstalkd instance has already served jobs, the counters should be
non-zero.


## Configuration on Debian / Ubuntu

Symlink the plugins to the Munin `plugins` directory:

```bash
$ cd /etc/munin/plugins
> for plugin in /opt/beanstalk-munin-php/plugins
> do
>  sudo ln -s $plugin
> done
$
```

Now reload `munin-node` to discover the plugins:

```bash
$ sudo systemctl restart munin-node
$
```

You can now check the plugins by telnetting to `munin-node`. Note that, unlike
most such servers, the `munin-node` daemon offers no telnet prompt: just go
ahead and type commands `list`, `config bs_connections.php` or 
`fetch bs_connections.php`. The end of result lists is shown by a single dot.

_Note_: In the listing below, lines have been folded with a `\ ` for readability, which
does not exist in actual `munin-node` output.

```bash
$ telnet localhost munin
Trying ::1...
Connected to localhost.localdomain.
Escape character is '^]'.
# munin node at ubuntu
list
bs_cmd_rate.php bs_connections.php bs_jobs_rate.php bs_queue_age.php \
bs_queue_size.php bs_timeouts.php cpu df df_inode entropy forks fw_packets \
if_ens33 if_err_ens33 interrupts irqstats load memory munin_stats netstat \
open_files open_inodes proc_pri processes swap threads uptime users vmstat
config bs_connections.php
graph_title Open connections
graph_vlabel Connections
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no
connections.label Connections
connections.type GAUGE
connections.min 0
.
fetch bs_connections.php
connections.value 1
.
quit
$ 
```

The actual list of plugins and the host name and IP on your system will vary,
the important points to check are:

* the fact that the `bs_*.php` plugins are listed,
* their configuration is reported as above (values may differ),
* fetching their values works as above (values may differ).

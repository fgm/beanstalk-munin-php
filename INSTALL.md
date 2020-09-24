# Install the Munin plugins for Beanstalkd

## Requirements

* PHP >= 7.2, 8.0 is not validated yet but should work.
* Composer (see https://getcomposer.org/download/ )
* Munin 2.x
  * Ensure Munin itself is already operational by visiting `http://(site)/munin`
  * Some users also report success with Munin 1.4.x, but this is not supported,
    since Munin 2 has been available since 2011.

## Deployment on Debian / Ubuntu

Download the plugins by cloning the repository

```bash
$ cd /opt
$ sudo git clone https://github.com/fgm/beanstalk-munin-php.git
$ cd beanstalk-munin-php
$ sudo chown -R $(whoami):www-data .
$
```

Now install the autoloaded dependencies, without the development dependencies,
which are only useful to contribute to the plugins development.

```bash
$ composer install --no-dev
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


## Extra configuration on other systems

On some systems (e.g. Centos), configuring the identifiers running the plugins
may be needed. In this case, edit `/etc/munin/plugin-conf.d/munin-node` to add
this section for the plugins:

```ini
[bs_*]
user root
group root
```


## Troubleshooting

### Talking to `munin-node`

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


### Changing the plugins shebang to adapt to the PHP distribution

Some non-default PHP deployments use the PHP CGI version when running the `php`
command, and will only use the PHP CLI version when called as `php-cli`. This is
know to happen on some cPanel/whm configurations, frequent on shared hosting.
This causes a problem as the CGI version emits headers by default (PHP signature
and Content-Type), which `munin-node` does not expect.

In this case, edit the shebang line on the `plugins/bs_*.php` files, replacing
`php` by `php-cli`.

On other systems (e.g. macOS), it is possible to pass arguments in the shebang,
so changing `php` to `php -q` will also remove the headers if they are present,
but this does *not* work on most Linux versions.

<?php

$VERSION = "0.0.14";

// Define the commands to run
$COMMANDS = [
    "loadavg" => "cat /proc/loadavg",
    "disks" => "df",
    "inodes" => "df -i",
    "iostat" => "iostat -x 2 2",
    "cpu" => "cat /proc/cpuinfo",
    "cpu_temperature" => "sensors",
    "cpu_dmi" => "dmidecode -t processor",
    "lsb" => "lsb_release -a",
    "memory" => "cat /proc/meminfo",
    "memory_dmi" => "dmidecode --type 17",
    "ifconfig" => "ifconfig",
    "ssacli" => "ssacli ctrl all show config",
    "perccli" => "perccli64 /c0 show",
    "system" => "dmidecode -t system",
    "upaimte" => "cat /proc/uptime",
    "uptime_cmd" => "uptime",
    "uname" => "uname -mrs",
    "netstat_statistics" => "netstat -s",
    "netstat_listen_tcp" => "netstat -antp | grep LISTEN",
    "netstat_listen_udp" => "netstat -anup | grep LISTEN",
    "freebsd_top" => "top -n",

    # list docker compose stacks
    "docker_compose_stacks" => "docker compose ls --format json",
    # list number of restarts of each docker container
    "docker-restarts" =>
    "docker ps -q | xargs -I{} docker inspect --format '{{.Name}} - Restarts: {{.RestartCount}}' {}",

    "lshw" => "lshw",
    "ufw_status" => "ufw status verbose",

    # list installed APT packages
    "apt_list" => "apt list --installed",
    
    # list failed systemd units
    "systemd-failed-units" => "systemctl list-units --state=failed",

    # date should be the last command, so we can compare with the monitoring server
    "date" => "date +%s"
];

$FUNCTIONS = [
    "reboot" => fn() => file_exists("/var/run/reboot-required") ? 'yes' : 'no',
    "updates" => fn() => file_get_contents("/var/lib/update-notifier/updates-available")
];

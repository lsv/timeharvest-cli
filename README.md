Timeharvest CLI
===============

This is done so you can easily from your terminal track your time to [Timeharvest](https://www.getharvest.com/)

### Requirements

- PHP >7.4
- Only tested on linux

### Install

You can find the newest time.phar file on [github releases](https://github.com/lsv/timeharvest-cli/releases)

Download the time.phar and put in either in your `$PATH` or in `/usr/local/bin`

Now you can from anywhere call `time.phar`

The first thing you need to do is to register your api key

`time.phar app:install` - This will guide you how to obtain an api key.

### Usage

On time commands, you can add `-p` for part of a project name, to filter out the projects when selecting, and `-t` for a task name to filter.

##### Insert time on a project

This command will add working time to a project, and a task on timeharvest. 

```bash
time.phar time <hours> "<note>"
# You will now come to the project and task selection
```
where `<hours>` are in the format `HH:mm` and <note> is a text string

###### Examples

```bash
time.phar time 0:35 "#TaskID - Fixing the intial bugs"
```

With `-p` and `-t`

```bash
time.phar time -p "My home pro" -t design 0:35 "#TaskID - Changing colors"
```

##### Start a clock on a project

This will add a running clock on a project

```bash
time.phar start
# You will now come to the project and task selection
```

###### Examples

```bash
time.phar start
```

```bash
time.phar start -p "My home pro" -t design
```

##### Stop a clock on a project

This will stop a running clock on a project.

If there is more than 1 running clock, this will not work, due to its not implemented.

```bash
time.phar stop
```

###### Examples

```bash
time.phar stop
```

### Other commands

#### Lists

Here you can get a list of your working time

##### Day list

```bash
time.phar day
```

This will list for the current day.

```bash
time.phar day 2020-02-22
```

This will list for 22nd feb. 2020

##### Week list

```bash
time.phar week
```

This will list for the current week.

```bash
time.phar week 50 2019
```

This will list for week 50 in year 2019

#### Config

You can add a default project, for a folder - So in this folder it will also choose the same project.

You can also add a default task for global or for a folder. (if the folder configuration is set it will overwrite global)

##### Default project

Set the default project for the current directory

```bash
time.phar config:set:project
# You will now come to the project selection -p can also be used
```

Remove the default project for the current directory

```bash
time.phar config:remove:project
```

##### Default task

Set the default task for the current directory (or if you use -g in the command, it will set the global)

As the task name is pr project, but many sets some default tasks for each project, this will just use a name to filter out the tasks when choosing a project

```bash
# For current directory
time.phar config:set:task "Task name"
```

```bash
# Global
time.phar config:set:task -g "Task name"
```

Remove the default task for the current directory (or if you -g in the command, it will remove from global)

```bash
# For current directory
time.phar config:remove:task
```

```bash
# Global
time.phar config:remove:task -g
```

#### Validate your api key

To validate if your api key is still valid, you can use

```bash
time.phar app:validate
```

### Auto update

*TODO*

### Uninstall

To uninstall, you can run this command

```bash
time.phar app:uninstall
```

This will remove the configuration file from your machine.
